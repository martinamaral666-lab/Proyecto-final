<?php

namespace App\Http\Controllers;

use App\Mail\NotificacionCobroAdmin;
use App\Models\Cobros;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CobrosController extends Controller
{
    public function index()
    {
        $cobros = Cobros::latest()->paginate(10);

        return view('cobros.index', compact('cobros'));
    }

    public function pdf(Cobros $cobro)
    {
        $rutaPdf = 'recibos/recibo_'.$cobro->id.'.pdf';

        abort_unless(Storage::disk('public')->exists($rutaPdf), 404);

        return response()->file(Storage::disk('public')->path($rutaPdf), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recibo_'.$cobro->id.'.pdf"',
        ]);
    }

    public function create()
    {
        $itemsInventario = Inventario::query()
            ->select('id', 'nombre_item', 'precio', 'stock_actual')
            ->orderBy('nombre_item')
            ->get();

        return view('cobros.create', compact('itemsInventario'));
    }

    public function adminIndex()
    {
        $cobros = Cobros::latest()->paginate(15);

        return view('admin.cobros.index', compact('cobros'));
    }

    public function store(Request $request)
    {
        $cooldownKey = 'charge-receipt:'.$request->user()->getAuthIdentifier();

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $waitSeconds = RateLimiter::availableIn($cooldownKey);

            return back()
                ->withErrors([
                    'receipt' => "Espera {$waitSeconds} segundos antes de generar otro recibo.",
                ])
                ->with('receipt_wait_seconds', $waitSeconds);
        }

        RateLimiter::hit($cooldownKey, 15);

        $validated = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'telefono' => 'required|string',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'monto_base' => 'nullable|numeric|min:0',
            'mano_de_obra' => 'required|in:si,no',
            'motivo_no_realizado' => 'nullable|required_if:mano_de_obra,no|string|max:255',
            'productos' => 'sometimes|array',
            'productos.*' => 'required|array:inventario_id,cantidad',
            'productos.*.inventario_id' => 'required|integer|exists:inventarios,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $cantidadesPorInventario = collect($validated['productos'] ?? [])
            ->groupBy('inventario_id')
            ->map(fn ($productos) => $productos->sum('cantidad'));
        $montoManoObraEnCentavos = $validated['mano_de_obra'] === 'si'
            ? (int) round(
                (float) ($validated['monto_base'] ?? (
                    $cantidadesPorInventario->isEmpty() ? $validated['monto'] : 0
                )) * 100
            )
            : 0;

        $cobro = DB::transaction(function () use ($cantidadesPorInventario, $montoManoObraEnCentavos, $validated) {
            $itemsInventario = Inventario::query()
                ->whereIn('id', $cantidadesPorInventario->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cantidadesPorInventario as $id => $cantidad) {
                $item = $itemsInventario->get($id);

                if (! $item || $cantidad > $item->stock_actual) {
                    $nombreItem = $item?->nombre_item ?? 'seleccionado';

                    throw ValidationException::withMessages([
                        'productos' => "No hay suficiente stock para {$nombreItem}.",
                    ]);
                }
            }

            $montoProductosEnCentavos = 0;
            $productosRecibo = [];

            foreach ($cantidadesPorInventario as $id => $cantidad) {
                $item = $itemsInventario->get($id);
                $precioUnitarioEnCentavos = (int) round((float) $item->precio * 100);
                $subtotalEnCentavos = $precioUnitarioEnCentavos * $cantidad;
                $montoProductosEnCentavos += $subtotalEnCentavos;
                $productosRecibo[] = [
                    'nombre_item' => $item->nombre_item,
                    'cantidad' => $cantidad,
                    'precio_unitario' => number_format($precioUnitarioEnCentavos / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalEnCentavos / 100, 2, '.', ''),
                ];
                $item->stock_actual -= $cantidad;
                $item->save();
            }

            $montoTotal = number_format(
                ($montoManoObraEnCentavos + $montoProductosEnCentavos) / 100,
                2,
                '.',
                ''
            );

            return Cobros::create([
                'nombre_cliente' => $validated['nombre_cliente'],
                'telefono' => $validated['telefono'],
                'concepto' => $validated['concepto'],
                'monto' => $montoTotal,
                'productos' => $productosRecibo,
                'monto_mano_obra' => number_format($montoManoObraEnCentavos / 100, 2, '.', ''),
                'mano_de_obra' => $validated['mano_de_obra'],
                'motivo_no_realizado' => $validated['mano_de_obra'] === 'no'
                    ? $validated['motivo_no_realizado']
                    : null,
            ]);
        });

        //  Genera el PDF y enviar email
        try {
            if (view()->exists('cobros.pdf')) {
                $pdf = Pdf::loadView('cobros.pdf', compact('cobro'));
                $nombreArchivo = 'recibo_'.$cobro->id.'.pdf';
                Storage::disk('public')->put('recibos/'.$nombreArchivo, $pdf->output());
                $rutaFisicaPdf = Storage::disk('public')->path('recibos/'.$nombreArchivo);

                if (config('mail.default') !== 'array' && config('mail.mailers.smtp.host')) {
                    $emailAdmin = env('ADMIN_EMAIL', 'admin@admin.com');
                    Mail::to($emailAdmin)->send(new NotificacionCobroAdmin($cobro, $rutaFisicaPdf));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar PDF/Email: '.$e->getMessage());
        }

        // Formatear teléfono para Uruguay (598)
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598'.substr($telefonoLimpio, 1);
        }

        $estadoManoObra = ($request->mano_de_obra === 'si')
            ? 'Realizada'
            : 'No realizada ('.($request->motivo_no_realizado ?? 'N/A').')';

        $detalleProductos = collect($cobro->productos ?? [])
            ->map(fn (array $producto) => '* '.$producto['nombre_item']
                .' x'.$producto['cantidad']
                .' — $'.number_format((float) $producto['precio_unitario'], 2).' c/u'
                .' = $'.number_format((float) $producto['subtotal'], 2))
            ->implode("\n");

        // Fecha en formato local de Uruguay para el ticket
        $fechaLocal = $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i');

        //  Formato Ticket de Pago para WhatsApp
        $ticketTexto = "==========================\n"
                     ."       *RECIBO DE PAGO*       \n"
                     ."==========================\n"
                     .'*N° Comprobante:* #'.str_pad($cobro->id, 6, '0', STR_PAD_LEFT)."\n"
                     .'*Fecha:* '.$fechaLocal."\n\n"
                     .'*Cliente:* '.$request->nombre_cliente."\n"
                     .'*Concepto:* '.$request->concepto."\n"
                     .($detalleProductos !== ''
                         ? "*Productos:*\n{$detalleProductos}\n"
                         : '')
                     .'*Mano de Obra:* '.$estadoManoObra."\n"
                     .((float) $cobro->monto_mano_obra > 0
                         ? '*Monto de mano de obra:* $'.number_format((float) $cobro->monto_mano_obra, 2)."\n"
                         : '')
                     ."--------------------------\n"
                     .'*MONTO TOTAL:* $'.number_format((float) $cobro->monto, 2)."\n"
                     ."==========================\n\n"
                     .'¡Muchas gracias por su preferencia!';

        $urlWhatsapp = 'https://api.whatsapp.com/send?phone='.$telefonoLimpio.'&text='.urlencode($ticketTexto);

        return redirect()->away($urlWhatsapp);
    }

    public function adminmenu()
    {
        $now = Carbon::now('America/Montevideo');

        // Totales calculados
        $ventasHoy = Cobros::whereDate('created_at', $now->toDateString())->sum('monto');

        $ventasSemana = Cobros::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->sum('monto');

        $ventasMes = Cobros::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('monto');

        // Construcción de días de la semana actual (Lunes a Domingo)
        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $totalesPorDia = [];
        $inicioSemana = $now->copy()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $fechaDia = $inicioSemana->copy()->addDays($i);
            $totalesPorDia[] = (float) Cobros::whereDate('created_at', $fechaDia->toDateString())->sum('monto');
        }

        return view('admin.menu', compact(
            'ventasHoy',
            'ventasSemana',
            'ventasMes',
            'dias',
            'totalesPorDia'
        ));
    }
}
