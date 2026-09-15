<?php

namespace App\Http\Controllers;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller {
    public function index(Request $request) {
    $query = Inventario::query();

    if ($request->has('search')) {
        $query->where('nombre_item', 'like', '%' . $request->search . '%')
              ->orWhere('categoria', 'like', '%' . $request->search . '%');
    }

    $items = $query->get();

    $stockCritico = Inventario::where('estado', 'like', '%Crítico%')->count();
    $enUsoObra = Inventario::where('estado', 'like', '%En Uso%')->count();
    $nuevosIngresos = Inventario::where('created_at', '>=', now()->subWeek())->count();

    return view('admin.menu', compact('items', 'stockCritico', 'enUsoObra', 'nuevosIngresos'));
}

    public function store(Request $request) {
        $request->validate([
            'nombre_item' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'stock_actual' => 'required|integer',
            'unidad' => 'required|string',
            'ubicacion' => 'required|string',
            'estado' => 'required|string',
        ]);

        Inventario::create($request->all());

        return redirect()->route('inventario.index')->with('success', 'Ítem agregado correctamente.');
    }

    public function destroy($id) {
        $item = Inventario::findOrFail($id);
        $item->delete();

        return redirect()->route('inventario.index')->with('success', 'Ítem eliminado.');
    }

    public function uso($id)
{
    $item = Inventario::findOrFail($id);


    $item->estado = 'en uso';
    $item->save();

    return redirect()->back()->with('el item esta en uso.');
}
}
