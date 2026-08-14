<?php

namespace App\Http\Controllers;

use App\Models\Cobros;
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
    {
        // Validación de los datos recibidos en la petición HTTP
        $request->validate([
          'cliente_id' => 'required|exists:clientes,id',
          'cantidad' => 'required|numeric|min:1',
          'concepto' => 'required|string|max:250',
        ]);

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


    /**
     * Display the specified resource.
     */
    public function show(Cobros $cobros)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cobros $cobros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCobrosRequest $request, Cobros $cobros)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cobros $cobros)
    {
        //
    }
}
