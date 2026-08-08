<?php

namespace App\Http\Controllers;

use App\Models\Cobros;
use App\Models\Cliente;
use App\Http\Requests\StoreCobrosRequest;
use App\Http\Requests\UpdateCobrosRequest;
use Illuminate\Support\Str;
 use Carbon\Carbon;

class CobrosController extends Controller
{


public function adminmenu()
{
    $hoy = Carbon::today();
    $inicioSemana = Carbon::now()->startOfWeek();
    $inicioMes = Carbon::now()->startOfMonth();


    $ventasHoy = Cobros::whereDate('created_at', $hoy)->sum('cantidad');
    $ventasSemana = Cobros::where('created_at', '>=', $inicioSemana)->sum('cantidad');
    $ventasMes = Cobros::where('created_at', '>=', $inicioMes)->sum('cantidad');


    $dias = [];
    $totalesPorDia = [];

    for ($i = 6; $i >= 0; $i--) {
        $fecha = Carbon::today()->subDays($i);
        $dias[] = $fecha->format('d/m');
        $totalesPorDia[] = Cobros::whereDate('created_at', $fecha)->sum('cantidad');
    }

    $ultimosCobros = Cobros::with('cliente')->latest()->take(5)->get();

    return view('admin.menu', compact(
        'ventasHoy',
        'ventasSemana',
        'ventasMes',
        'dias',
        'totalesPorDia',
        'ultimosCobros'
    ));
}


    public function index()
    {
        $cobros = Cobros::where('user_id', auth()->id())
          ->with('cliente')
          ->latest()
          ->paginate(15);

        $totalHoy = Cobros::where('user_id', auth()->id())
          ->whereDate('fecha_de_pago', today())
          ->sum('cantidad');

        return view('cobros.index', compact('cobros', 'totalHoy'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cliente = Cliente::all();
        return view('cobros.create', compact('cliente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCobrosRequest $request)
    {
        $request->validate([
          'cliente_id' => 'required|exists:clientes,id',
          'cantidad' => 'required|numeric|min:1',
          'concepto' => 'required|string|max:250',
        ]);

    try{
          $cobro = Cobros::create([
            'cliente_id' => $request->cliente_id,
            'user_id' =>auth()->id(),
            'cantidad' =>$request->cantidad,
            'concepto' =>$request->concepto,
            'estado' => 'completado',
            'receipt_token' => Str::random(32),
            'fecha_de_pago' => now(),
        ]);
       return redirect()->route('cobros.index')
          ->with('succes', 'el recibo se envio correctamente.');
    } catch (\Exeption $e) {
        return back()
        ->with('Error', 'No se puedo enviar el recibo.')
        ->withInput();
    }
    }

        private function redirectToWhatsApp(Cobros $cobro)
        {
          $cliente = $cobro->Cliente ?? Cliente::find($cobro->cliente_id);

          if (!$cliente){
            return redirect()->back()->with('Error', 'No se encontro el cliente asociado.');
          }

          $receiptUrl = route('recibo.publico', ['token' => $cobro->receipt_token]);
          $message = "Hola {$cliente->nombre}, confirmamos tu recepcion de pago por $" . number_format($cobro->cantidad, 2).". Puedes ver tu comprobante aqui: {$receiptUrl}";

          $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);

          $WhatsAppUrl = "https://wa.me/{$telefono}?text=" . Urlencode($message);

          return redirect()->away($WhatsAppUrl);
        }

        public function showPublicReceipt($token)
        {
            $cobro = Cobros::where('receipt_token', $token)
            ->with(['cliente', 'empleado'])
            ->firstOrFail();

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
