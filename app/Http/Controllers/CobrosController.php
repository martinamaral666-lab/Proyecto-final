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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CobrosController extends Controller
{
    public function index()
    {
        $cobros = Cobros::latest()->paginate(10);

        return view('cobros.index', compact('cobros'));
    }

    public function pdf(Cobros $cobro)
    {
        // El PDF se genera en el momento con la vista actual
        // (resources/views/cobros/pdf.blade.php). Antes se leía un archivo guardado
        // al crear el cobro, por eso los cambios de diseño no llegaban a los recibos
        // ya existentes y, si el archivo faltaba, daba 404.
        return Pdf::loadView('cobros.pdf', compact('cobro'))
            ->stream('recibo_' . $cobro->id . '.pdf');
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

        /*
        |--------------------------------------------------------------------------
        | 1. VALIDAR LOS DATOS
        |--------------------------------------------------------------------------
        | Antes el resultado de validate() no se guardaba en ninguna variable,
        | por eso fallaba con "Undefined variable $validated".
        */

        $validated = $request->validate([
            'tipo_registro' => 'required|in:venta,mano_obra',
            'nombre_cliente' => 'required|string|max:255',
            'telefono' => 'required|string',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'monto_base' => 'nullable|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta',

            'mano_de_obra' => [
                'nullable',
                'required_if:tipo_registro,mano_obra',
                'in:si,no',
            ],

            // El motivo solo es obligatorio para mano de obra NO realizada.
            // Una venta en caja puede mandar mano_de_obra = "no" sin motivo.
            'motivo_no_realizado' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    fn () => $request->tipo_registro === 'mano_obra'
                        && $request->mano_de_obra === 'no'
                ),
            ],

            'productos' => 'sometimes|array',
            'productos.*' => 'required|array:inventario_id,cantidad',
            'productos.*.inventario_id' => 'required|integer|exists:inventarios,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // El cooldown se aplica recién cuando los datos son válidos,
        // así un error de tipeo no bloquea al usuario 15 segundos.
        RateLimiter::hit($cooldownKey, 15);

        /*
        |--------------------------------------------------------------------------
        | 2. DETERMINAR LOS DATOS DE MANO DE OBRA
        |--------------------------------------------------------------------------
        |
        | Venta en caja:                 mano_de_obra = no, motivo = null
        | Mano de obra realizada:        mano_de_obra = si, motivo = null
        | Mano de obra no realizada:     mano_de_obra = no, motivo obligatorio
        */

        $tipo = $validated['tipo_registro'];

        $manoDeObra = $tipo === 'mano_obra'
            ? ($validated['mano_de_obra'] ?? 'no')
            : 'no';

        $motivoNoRealizado = ($tipo === 'mano_obra' && $manoDeObra === 'no')
            ? ($validated['motivo_no_realizado'] ?? null)
            : null;

        /*
        |--------------------------------------------------------------------------
        | 3. CALCULAR MONTOS Y GUARDAR EL COBRO (UNA SOLA VEZ)
        |--------------------------------------------------------------------------
        | Antes se creaba el cobro dos veces (una acá y otra dentro de la
        | transacción), lo que dejaba registros duplicados.
        */

        $cantidadesPorInventario = collect($validated['productos'] ?? [])
            ->groupBy('inventario_id')
            ->map(fn ($productos) => $productos->sum('cantidad'));

        // Monto manual (mano de obra, servicio u otros): se suma a los productos.
        //  - Venta en caja: siempre se cuenta.
        //  - Mano de obra: solo si fue realizada (si no se realizó, no se cobra).
        // Antes, en una venta el monto escrito se ignoraba cuando había productos.
        $contarMontoManual = $tipo === 'venta' || $manoDeObra === 'si';

        $montoManoObraEnCentavos = $contarMontoManual
            ? (int) round(
                (float) ($validated['monto_base'] ?? (
                    $cantidadesPorInventario->isEmpty() ? $validated['monto'] : 0
                )) * 100
            )
            : 0;

        try {
            $cobro = DB::transaction(function () use (
                $cantidadesPorInventario,
                $montoManoObraEnCentavos,
                $validated,
                $tipo,
                $manoDeObra,
                $motivoNoRealizado
            ) {
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
                    'tipo_registro' => $tipo,
                    'user_id' => auth()->id(),
                    'nombre_cliente' => $validated['nombre_cliente'],
                    'telefono' => $validated['telefono'],
                    'concepto' => $validated['concepto'],
                    'metodo_pago' => $validated['metodo_pago'],
                    'monto' => $montoTotal,
                    'productos' => $productosRecibo,
                    'monto_mano_obra' => number_format($montoManoObraEnCentavos / 100, 2, '.', ''),
                    'mano_de_obra' => $manoDeObra,
                    'motivo_no_realizado' => $motivoNoRealizado,
                ]);
            });
        } catch (ValidationException $e) {
            // Falta de stock: se libera el cooldown para que pueda corregir y reintentar.
            RateLimiter::clear($cooldownKey);

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. GENERAR PDF Y ENVIAR EMAIL
        |--------------------------------------------------------------------------
        */

        try {
            if (view()->exists('cobros.pdf')) {

                $pdf = Pdf::loadView(
                    'cobros.pdf',
                    compact('cobro')
                );

                $nombreArchivo = 'recibo_' . $cobro->id . '.pdf';

                Storage::disk('public')->put(
                    'recibos/' . $nombreArchivo,
                    $pdf->output()
                );

                $rutaFisicaPdf = Storage::disk('public')->path(
                    'recibos/' . $nombreArchivo
                );

                if (
                    config('mail.default') !== 'array'
                    && config('mail.mailers.smtp.host')
                ) {
                    $emailAdmin = env(
                        'ADMIN_EMAIL',
                        'admin@admin.com'
                    );

                    Mail::to($emailAdmin)->send(
                        new NotificacionCobroAdmin(
                            $cobro,
                            $rutaFisicaPdf
                        )
                    );
                }
            }
        } catch (\Exception $e) {

            Log::error(
                'Error al procesar PDF/Email: ' . $e->getMessage()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. FORMATEAR TELÉFONO PARA WHATSAPP
        |--------------------------------------------------------------------------
        */

        $telefonoLimpio = preg_replace(
            '/[^0-9]/',
            '',
            $validated['telefono']
        );

        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598' . substr(
                $telefonoLimpio,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. PREPARAR LOS DATOS DEL RECIBO
        |--------------------------------------------------------------------------
        | Antes había dos bloques de ticket: el primero usaba $estadoManoObra
        | antes de definirla y el segundo lo pisaba. Ahora hay uno solo, con
        | el detalle de productos y el monto de mano de obra incluidos.
        */

        if ($tipo === 'venta') {
            $estadoManoObra = 'No corresponde';
        } elseif ($manoDeObra === 'si') {
            $estadoManoObra = 'Realizada';
        } else {
            $estadoManoObra = 'No realizada (' . ($motivoNoRealizado ?? 'N/A') . ')';
        }

        $metodoPagoTexto = $validated['metodo_pago'] === 'efectivo'
            ? 'Efectivo'
            : 'Tarjeta';

        $tipoRegistroTexto = $tipo === 'venta'
            ? 'Venta en caja'
            : 'Mano de obra';

        $productosCobro = is_array($cobro->productos)
            ? $cobro->productos
            : (json_decode($cobro->productos ?? '[]', true) ?: []);

        $detalleProductos = collect($productosCobro)
            ->map(fn (array $producto) => '* '.$producto['nombre_item']
                .' x'.$producto['cantidad']
                .' — $'.number_format((float) $producto['precio_unitario'], 2).' c/u'
                .' = $'.number_format((float) $producto['subtotal'], 2))
            ->implode("\n");

        $fechaLocal = $cobro->created_at
            ->timezone('America/Montevideo')
            ->format('d/m/Y H:i');

        /*
        |--------------------------------------------------------------------------
        | 7. TEXTO DEL RECIBO PARA WHATSAPP
        |--------------------------------------------------------------------------
        */

        $ticketTexto = "==========================\n"
            ."       *RECIBO DE PAGO*       \n"
            ."==========================\n"
            .'*N° Comprobante:* #'.str_pad($cobro->id, 6, '0', STR_PAD_LEFT)."\n"
            .'*Fecha:* '.$fechaLocal."\n\n"
            .'*Tipo de registro:* '.$tipoRegistroTexto."\n"
            .'*Cliente:* '.$validated['nombre_cliente']."\n"
            .'*Concepto:* '.$validated['concepto']."\n"
            .'*Medio de pago:* '.$metodoPagoTexto."\n"
            .($detalleProductos !== ''
                ? "*Productos:*\n{$detalleProductos}\n"
                : '')
            .'*Mano de Obra:* '.$estadoManoObra."\n"
            .((float) $cobro->monto_mano_obra > 0
                ? '*'.($tipo === 'venta' ? 'Servicio / monto adicional' : 'Monto de mano de obra').':* $'.number_format((float) $cobro->monto_mano_obra, 2)."\n"
                : '')
            ."--------------------------\n"
            .'*MONTO TOTAL:* $'.number_format((float) $cobro->monto, 2)."\n"
            ."==========================\n\n"
            .'¡Muchas gracias por su preferencia!';

        /*
        |--------------------------------------------------------------------------
        | 8. ABRIR WHATSAPP
        |--------------------------------------------------------------------------
        | Con Inertia, un redirect()->away() hacia otro dominio falla en el
        | navegador (CORS). Inertia::location() hace la redirección completa.
        */

        $urlWhatsapp =
            'https://api.whatsapp.com/send?phone='
            . $telefonoLimpio
            . '&text='
            . urlencode($ticketTexto);

        return Inertia::location($urlWhatsapp);
    }

    public function adminmenu()
    {
        $now = Carbon::now('America/Montevideo');

        // Totales calculados

        $ventasHoy = Cobros::whereDate(
            'created_at',
            $now->toDateString()
        )->sum('monto');

        $ventasSemana = Cobros::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->sum('monto');

        $ventasMes = Cobros::whereMonth(
            'created_at',
            $now->month
        )
        ->whereYear(
            'created_at',
            $now->year
        )
        ->sum('monto');

        // Construcción de días de la semana actual

        $dias = [
            'Lun',
            'Mar',
            'Mié',
            'Jue',
            'Vie',
            'Sáb',
            'Dom',
        ];

        $totalesPorDia = [];

        $inicioSemana = $now->copy()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {

            $fechaDia = $inicioSemana
                ->copy()
                ->addDays($i);

            $totalesPorDia[] = (float) Cobros::whereDate(
                'created_at',
                $fechaDia->toDateString()
            )->sum('monto');
        }

        return view(
            'admin.menu',
            compact(
                'ventasHoy',
                'ventasSemana',
                'ventasMes',
                'dias',
                'totalesPorDia'
            )
        );
    }
}
