<?php

namespace App\Http\Controllers;

use App\Models\Cobros;
<<<<<<< HEAD
use App\Models\Cliente;
use App\Http\Requests\StoreCobrosRequest;
use App\Http\Requests\UpdateCobrosRequest;
// Importación de utilidades de Laravel para manejo de cadenas de texto y fechas
use Illuminate\Support\Str;
 use Carbon\Carbon;

class CobrosController extends Controller
{


public function adminmenu()
{
   // Definición de rangos de fechas actuales usando Carbon
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();

        // Cálculo de las sumas totales vendidas hoy, esta semana y este mes
        $ventasHoy = Cobros::whereDate('created_at', $hoy)->sum('cantidad');
        $ventasSemana = Cobros::where('created_at', '>=', $inicioSemana)->sum('cantidad');
        $ventasMes = Cobros::where('created_at', '>=', $inicioMes)->sum('cantidad');

        // Arreglos para almacenar las etiquetas de los últimos 7 días y sus respectivos totales
        $dias = [];
        $totalesPorDia = [];

        // Bucle para iterar sobre los últimos 7 días (de hace 6 días a hoy)
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $dias[] = $fecha->format('d/m'); // Formato día/mes
            $totalesPorDia[] = Cobros::whereDate('created_at', $fecha)->sum('cantidad'); // Suma por día
        }

        // Obtención de los últimos 5 cobros registrados cargando la relación con el cliente
        $ultimosCobros = Cobros::with('cliente')->latest()->take(5)->get();

        // Retorna la vista con todas las variables estadísticas calculadas
        return view('admin.menu', compact(
            'ventasHoy',
            'ventasSemana',
            'ventasMes',
            'dias',
            'totalesPorDia',
            'ultimosCobros'
        ));
    }

    /**
     * Muestra el listado de cobros correspondientes al usuario autenticado.
     */
    public function index()
    {
        // Obtiene los cobros del usuario actual, paginados de a 15 y ordenados por el más reciente
        $cobros = Cobros::where('user_id', auth()->id())
          ->with('cliente')
          ->latest()
          ->paginate(15);

        // Calcula el total recaudado en el día de hoy por el usuario autenticado
        $totalHoy = Cobros::where('user_id', auth()->id())
          ->whereDate('fecha_de_pago', today())
          ->sum('cantidad');

        // Retorna la vista principal de cobros con el listado y el total de hoy
        return view('cobros.index', compact('cobros', 'totalHoy'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para registrar un nuevo cobro.
     */
    public function create()
    {
        // Obtiene el listado completo de clientes
        $cliente = Cliente::all();

        // Retorna la vista del formulario pasándole los clientes
        return view('cobros.create', compact('cliente'));
    }

    /**
     * Store a newly created resource in storage.
     * Guarda un nuevo cobro en la base de datos.
     */
    public function store(StoreCobrosRequest $request)
=======
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
>>>>>>> correcion-de-errores-de-rutas1
    {
        // Validación de los datos recibidos en la petición HTTP
        $request->validate([
            'nombre_cliente'      => 'required|string|max:255',
            'telefono'            => 'required|string',
            'concepto'            => 'required|string|max:255',
            'monto'               => 'required|numeric',
            'mano_de_obra'        => 'required|in:si,no',
            'motivo_no_realizado' => 'nullable|required_if:mano_de_obra,no|string|max:255',
        ]);

<<<<<<< HEAD
        try {
            // Intenta crear el registro del nuevo cobro en la base de datos
            $cobro = Cobros::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => auth()->id(),
                'cantidad' => $request->cantidad,
                'concepto' => $request->concepto,
                'estado' => 'completado',
                'receipt_token' => Str::random(32), // Genera un token aleatorio único de 32 caracteres
                'fecha_de_pago' => now(),
            ]);

            // Redirecciona al index con mensaje de éxito si todo salió bien
            return redirect()->route('cobros.index')
              ->with('succes', 'el recibo se envio correctamente.');
        } catch (\Exeption $e) {
            // Si ocurre algún error en la creación, redirige de vuelta conservando los datos ingresados
            return back()
            ->with('Error', 'No se puedo enviar el recibo.')
            ->withInput();
        }
    }

    /**
     * Genera un mensaje con el enlace al recibo público y redirige a la API de WhatsApp Web/App.
     */
    private function redirectToWhatsApp(Cobros $cobro)
    {
        // Obtiene la relación de cliente o busca manualmente por cliente_id si no está precargada
        $cliente = $cobro->Cliente ?? Cliente::find($cobro->cliente_id);

        // Si no existe el cliente asociado, vuelve atrás con mensaje de error
        if (!$cliente) {
            return redirect()->back()->with('Error', 'No se encontro el cliente asociado.');
        }

        // Genera la URL pública del recibo utilizando el token único
        $receiptUrl = route('recibo.publico', ['token' => $cobro->receipt_token]);

        // Construye el mensaje personalizado para el cliente
        $message = "Hola {$cliente->nombre}, confirmamos tu recepcion de pago por $" . number_format($cobro->cantidad, 2) . ". Puedes ver tu comprobante aqui: {$receiptUrl}";

        // Limpia el número telefónico para dejar solo caracteres numéricos
        $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);

        // Genera la URL de redirección a WhatsApp encoded adecuadamente
        $WhatsAppUrl = "https://wa.me/{$telefono}?text=" . Urlencode($message);

        // Redirige fuera de la aplicación hacia la URL externa de WhatsApp
        return redirect()->away($WhatsAppUrl);
    }

    /**
     * Muestra el recibo público accesible mediante el token dinámico sin requerir autenticación.
     */
    public function showPublicReceipt($token)
    {
        // Busca el cobro mediante el token recibido; carga relaciones cliente y empleado o lanza error 404 si no existe
        $cobro = Cobros::where('receipt_token', $token)
            ->with(['cliente', 'empleado'])
            ->firstOrFail();

        // Retorna la vista pública del recibo
        return view('recibos.show', compact('cobro'));
    }
=======
        // 1. Guardar en la Base de Datos
        $cobro = Cobros::create([
            'nombre_cliente'      => $request->nombre_cliente,
            'telefono'            => $request->telefono,
            'concepto'            => $request->concepto,
            'monto'               => $request->monto,
            'mano_de_obra'        => $request->mano_de_obra,
            'motivo_no_realizado' => $request->mano_de_obra === 'no' ? $request->motivo_no_realizado : null,
        ]);

        // 2. Generar PDF y enviar email
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

        // 3. Formatear teléfono para Uruguay (598)
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $request->telefono);
        if (str_starts_with($telefonoLimpio, '0')) {
            $telefonoLimpio = '598' . substr($telefonoLimpio, 1);
        }
>>>>>>> correcion-de-errores-de-rutas1

        $estadoManoObra = ($request->mano_de_obra === 'si')
            ? "Realizada"
            : "No realizada (" . ($request->motivo_no_realizado ?? 'N/A') . ")";

        // Fecha en formato local de Uruguay para el ticket
        $fechaLocal = $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i');

        // 4. Formato Ticket de Pago para WhatsApp
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
