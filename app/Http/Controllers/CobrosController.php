<?php

namespace App\Http\Controllers;

use App\Models\Cobros;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCobroAdmin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CobrosController extends Controller
{
    public function index()
    {
        $cobros = Cobros::latest()->paginate(10);
        return view('cobros.index', compact('cobros'));
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
        $request->validate([
            'nombre_cliente'      => 'required|string|max:255',
            'telefono'            => 'required|string',
            'concepto'            => 'required|string|max:255',
            'monto'               => 'required|numeric',
            'mano_de_obra'        => 'required|in:si,no',
            'motivo_no_realizado' => 'nullable|required_if:mano_de_obra,no|string|max:255',
        ]);

        //  Guarda los datos en la Base de Datos
        $cobro = Cobros::create([
            'nombre_cliente'      => $request->nombre_cliente,
            'telefono'            => $request->telefono,
            'concepto'            => $request->concepto,
            'monto'               => $request->monto,
            'mano_de_obra'        => $request->mano_de_obra,
            'motivo_no_realizado' => $request->mano_de_obra === 'no' ? $request->motivo_no_realizado : null,
        ]);

        //  Genera el PDF y enviar email
        try {
            if (view()->exists('cobros.pdf')) {
                $pdf = Pdf::loadView('cobros.pdf', compact('cobro'));
                $nombreArchivo = 'recibo_' . $cobro->id . '.pdf';
                Storage::disk('public')->put('recibos/' . $nombreArchivo, $pdf->output());
                $rutaFisicaPdf = Storage::disk('public')->path('recibos/' . $nombreArchivo);

                if (config('mail.default') !== 'array' && config('mail.mailers.smtp.host')) {
                    $emailAdmin = env('ADMIN_EMAIL', 'admin@admin.com');
                    Mail::to($emailAdmin)->send(new NotificacionCobroAdmin($cobro, $rutaFisicaPdf));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al procesar PDF/Email: " . $e->getMessage());
        }

        // Formatear teléfono para Uruguay (598)
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598' . substr($telefonoLimpio, 1);
        }

        $estadoManoObra = ($request->mano_de_obra === 'si')
            ? "Realizada"
            : "No realizada (" . ($request->motivo_no_realizado ?? 'N/A') . ")";

        // Fecha en formato local de Uruguay para el ticket
        $fechaLocal = $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i');

        //  Formato Ticket de Pago para WhatsApp
        $ticketTexto = "==========================\n"
                     . "       *RECIBO DE PAGO*       \n"
                     . "==========================\n"
                     . "*N° Comprobante:* #" . str_pad($cobro->id, 6, '0', STR_PAD_LEFT) . "\n"
                     . "*Fecha:* " . $fechaLocal . "\n\n"
                     . "*Cliente:* " . $request->nombre_cliente . "\n"
                     . "*Concepto:* " . $request->concepto . "\n"
                     . "*Mano de Obra:* " . $estadoManoObra . "\n"
                     . "--------------------------\n"
                     . "*MONTO TOTAL:* $" . number_format($request->monto, 2) . "\n"
                     . "==========================\n\n"
                     . "¡Muchas gracias por su preferencia!";

        $urlWhatsapp = "https://api.whatsapp.com/send?phone=" . $telefonoLimpio . "&text=" . urlencode($ticketTexto);

        return redirect()->away($urlWhatsapp);
    }

    public function adminmenu()
    {
        $now = Carbon::now('America/Montevideo');

        // Totales calculados
        $ventasHoy = Cobros::whereDate('created_at', $now->toDateString())->sum('monto');

        $ventasSemana = Cobros::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
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
<<<<<<< HEAD




=======
>>>>>>> c14ab50 (feat: diseño de registro de empleados y recibos)
