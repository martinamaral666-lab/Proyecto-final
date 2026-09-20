<?php

namespace App\Http\Controllers;

use App\Mail\NotificacionCobroAdmin;
use App\Models\Cobros;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CobrosController extends Controller
{
    public function index()
    {
        $cobros = Cobros::latest()->paginate(10);

        return view('cobros.index', compact('cobros'));
    }

    public function pdf(Cobros $cobro)
    {
        $rutaPdf = 'recibos/recibo_' . $cobro->id . '.pdf';

        abort_unless(
            Storage::disk('public')->exists($rutaPdf),
            404
        );

        return response()->file(
            Storage::disk('public')->path($rutaPdf),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="recibo_' . $cobro->id . '.pdf"',
            ]
        );
    }

    public function create()
    {
        return view('cobros.create');
    }

    public function adminIndex()
    {
        $cobros = Cobros::latest()->paginate(15);

        return view('admin.cobros.index', compact('cobros'));
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. VALIDAR LOS DATOS
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'tipo_registro' => 'required|in:venta,mano_obra',

            'nombre_cliente' => 'required|string|max:255',

            'telefono' => 'required|string',

            'concepto' => 'required|string|max:255',

            'monto' => 'required|numeric|min:0',

            'metodo_pago' => 'required|in:efectivo,tarjeta',

            'mano_de_obra' => [
                'required_if:tipo_registro,mano_obra',
                'nullable',
                'in:si,no',
            ],

            'motivo_no_realizado' => [
                'nullable',
                'string',
                'max:255',
                'required_if:tipo_registro,mano_obra',
                'required_if:mano_de_obra,no',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. DETERMINAR LOS DATOS DE MANO DE OBRA
        |--------------------------------------------------------------------------
        |
        | Venta en caja:
        |   - mano_de_obra = no
        |   - motivo = null
        |
        | Mano de obra realizada:
        |   - mano_de_obra = si
        |   - motivo = null
        |
        | Mano de obra no realizada:
        |   - mano_de_obra = no
        |   - motivo obligatorio
        |
        */

        $manoDeObra = $request->tipo_registro === 'mano_obra'
            ? $request->mano_de_obra
            : 'no';

        $motivoNoRealizado =
            $request->tipo_registro === 'mano_obra'
            && $manoDeObra === 'no'
                ? $request->motivo_no_realizado
                : null;

        /*
        |--------------------------------------------------------------------------
        | 3. GUARDAR EL COBRO
        |--------------------------------------------------------------------------
        */

        $cobro = Cobros::create([
            'tipo_registro' => $request->tipo_registro,
            'user_id' => auth()->id(),
            'nombre_cliente' => $request->nombre_cliente,
            'telefono' => $request->telefono,
            'concepto' => $request->concepto,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'mano_de_obra' => $manoDeObra,
            'motivo_no_realizado' => $motivoNoRealizado,
        ]);

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
            $request->telefono
        );

        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598' . substr(
                $telefonoLimpio,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. PREPARAR INFORMACIÓN DE MANO DE OBRA
        |--------------------------------------------------------------------------
        */

        if ($request->tipo_registro === 'venta') {

            $estadoManoObra = 'No corresponde';

        } elseif ($request->mano_de_obra === 'si') {

            $estadoManoObra = 'Realizada';

        } else {

            $estadoManoObra = 'No realizada (' .
                ($request->motivo_no_realizado ?? 'N/A') .
                ')';
        }

        /*
        |--------------------------------------------------------------------------
        | 7. PREPARAR INFORMACIÓN DEL MEDIO DE PAGO
        |--------------------------------------------------------------------------
        */

        $metodoPagoTexto = $request->metodo_pago === 'efectivo'
            ? 'Efectivo'
            : 'Tarjeta';

        /*
        |--------------------------------------------------------------------------
        | 8. FECHA LOCAL DE URUGUAY
        |--------------------------------------------------------------------------
        */

        $fechaLocal = $cobro->created_at
            ->timezone('America/Montevideo')
            ->format('d/m/Y H:i');

        /*
        |--------------------------------------------------------------------------
        | 9. TEXTO DEL RECIBO PARA WHATSAPP
        |--------------------------------------------------------------------------
        */

        $tipoRegistroTexto = $request->tipo_registro === 'venta'
            ? 'Venta en caja'
            : 'Mano de obra';

        $ticketTexto =
            "==========================\n"
            . "       *RECIBO DE PAGO*       \n"
            . "==========================\n"
            . "*N° Comprobante:* #"
            . str_pad(
                $cobro->id,
                6,
                '0',
                STR_PAD_LEFT
            )
            . "\n"
            . "*Fecha:* "
            . $fechaLocal
            . "\n\n"
            . "*Tipo de registro:* "
            . $tipoRegistroTexto
            . "\n"
            . "*Cliente:* "
            . $request->nombre_cliente
            . "\n"
            . "*Concepto:* "
            . $request->concepto
            . "\n"
            . "*Medio de pago:* "
            . $metodoPagoTexto
            . "\n"
            . "*Mano de Obra:* "
            . $estadoManoObra
            . "\n"
            . "--------------------------\n"
            . "*MONTO TOTAL:* $"
            . number_format(
                $request->monto,
                2
            )
            . "\n"
            . "==========================\n\n"
            . "¡Muchas gracias por su preferencia!";

        /*
        |--------------------------------------------------------------------------
        | 10. ABRIR WHATSAPP
        |--------------------------------------------------------------------------
        */

        $urlWhatsapp =
            'https://api.whatsapp.com/send?phone='
            . $telefonoLimpio
            . '&text='
            . urlencode($ticketTexto);

        return redirect()->away($urlWhatsapp);
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
