<?php

namespace App\Http\Controllers;

use App\Models\InfoCliente;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoClienteController extends Controller
{

  public function index()
{
    $clientes = DB::table('cobros')
        ->select(
            'nombre_cliente as id',
            'nombre_cliente as name',
            DB::raw('count(*) as total_compras'),
            DB::raw('MAX(created_at) as ultima_compra'),
            DB::raw('MAX(telefono) as phone'),
            DB::raw("'' as email")
        )
        ->whereMonth('created_at', now()->month)
        ->groupBy('nombre_cliente')
        ->get()
        ->map(function ($cliente) {
            $cliente->total_mes = $cliente->total_compras;
            return $cliente;
        });

        $clientes = Cliente::all()->map(function ($cliente) {
        $cliente->total_compras = DB::table('cobros')
            ->where('nombre_cliente', $cliente->nombre)
            ->whereMonth('created_at', now()->month)
            ->count();
        return $cliente;
    });

    return view('admin.infodeclientes.index', compact('clientes'));
}

   public function show($nombre)
{
    $nombreDecoded = urldecode($nombre);

    $cliente = Cliente::firstOrCreate(['nombre' => $nombreDecoded]);

    $cliente->name = $cliente->nombre;
    $cliente->phone = $cliente->telefono;

    $cliente->total_compras = DB::table('cobros')
        ->where('nombre_cliente', $nombreDecoded)
        ->whereMonth('created_at', now()->month)
        ->count();

    return view('admin.infodeclientes.ver', compact('cliente'));
}

public function edit($nombre)
{
    $nombreDecoded = urldecode($nombre);

    $cliente = (object) [
        'id' => $nombreDecoded,
        'name' => $nombreDecoded,
        'phone' => DB::table('cobros')->where('nombre_cliente', $nombreDecoded)->max('telefono'),
        'email' => ''
    ];

    return view('admin.infodeclientes.editarcliente', compact('cliente'));
}

public function update(Request $request, $nombre)
{
    $nombreDecoded = urldecode($nombre);

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email',
    ]);

    $cliente = Cliente::where('nombre', $nombreDecoded)->firstOrNew();

    $cliente->nombre = $request->input('name');
    $cliente->telefono = $request->input('phone');
    $cliente->email = $request->input('email');
    $cliente->save();

    return redirect()->route('admin.infodeclientes.index')->with('success', 'Cliente actualizado correctamente.');
}
}
