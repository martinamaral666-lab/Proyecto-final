<?php

namespace App\Http\Controllers;

use App\Models\Cobros;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cliente;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCobroAdmin;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCobrosRequest;
use App\Http\Requests\UpdateCobrosRequest;
// Importación de utilidades de Laravel para manejo de cadenas de texto y fechas
use Illuminate\Support\Str;
 use Carbon\Carbon;



class CobrosController extends Controller
{
    public function index()
    {

    $cobros = Cobros::paginate(10);


    $totalHoy = Cobros::whereDate('created_at', today())->sum('monto');

    return view('cobros.index', compact('cobros', 'totalHoy'));
}


    public function create()
    {

       return view('cobros.create');
    }


    public function store(Request $request)
    {
       $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'telefono'       => 'required|string',
            'concepto'       => 'required|string|max:255',
            'monto'          => 'required|numeric',
            'mano_de_obra'   => 'required|in:si,no',
        ]);


        $cobro = Cobros::create([
            'nombre_cliente'       => $request->nombre_cliente,
            'telefono'             => $request->telefono,
            'concepto'             => $request->concepto,
            'monto'                => $request->monto,
            'mano_de_obra'         => $request->mano_de_obra,
            'motivo_no_realizado' => $request->mano_de_obra === 'no' ? $request->motivo_no_realizado : null,
        ]);


        $pdf = Pdf::loadView('cobros.pdf', compact('cobro'));
        $nombreArchivo = 'recibo_' . $cobro->id . '.pdf';
        Storage::disk('public')->put('recibos/' . $nombreArchivo, $pdf->output());
        $rutaFisicaPdf = storage_path('app/public/recibos/' . $nombreArchivo);


        $emailAdmin = env('ADMIN_EMAIL', 'admin@admin.com');
        Mail::to($emailAdmin)->send(new NotificacionCobroAdmin($cobro, $rutaFisicaPdf));


        $telefonoLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598' . substr($telefonoLimpio, 1);
        }

        $estadoManoObra = ($request->mano_de_obra === 'si')
            ? "Realizada"
            : "No realizada (" . ($request->motivo_no_realizado ?? 'N/A') . ")";

        // Formato estilo Ticket de Pago en texto plano de WhatsApp
        $ticketTexto = "==========================\n"
                     . "       *RECIBO DE PAGO*       \n"
                     . "==========================\n"
                     . "*N° Comprobante:* #" . str_pad($cobro->id, 6, '0', STR_PAD_LEFT) . "\n"
                     . "*Fecha:* " . $cobro->created_at->format('d/m/Y H:i') . "\n\n"
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
     $ultimosCobros = Cobros::latest()->take(10)->get();


    $ventasHoy = Cobros::whereDate('created_at', Carbon::today())->sum('monto');
    $ventasSemana = Cobros::whereBetween('created_at', [
        Carbon::now()->startOfWeek(),
        Carbon::now()->endOfWeek()
    ])->sum('monto');
    $ventasMes = Cobros::whereMonth('created_at', Carbon::now()->month)
                       ->whereYear('created_at', Carbon::now()->year)
                       ->sum('monto');


    $dias = [];
    $totalesPorDia = [];

    for ($i = 6; $i >= 0; $i--) {
        $fecha = Carbon::today()->subDays($i);
        $dias[] = $fecha->format('d/m'); // Ejemplo: "16/08"
        $totalesPorDia[] = Cobros::whereDate('created_at', $fecha)->sum('monto');
    }

    return view('admin.menu', compact(
        'ultimosCobros',
        'ventasHoy',
        'ventasSemana',
        'ventasMes',
        'dias',
        'totalesPorDia'
    ));

}

    }


