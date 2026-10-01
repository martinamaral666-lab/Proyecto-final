<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query();

        if ($request->has('search')) {
            $query->where('nombre_item', 'like', '%'.$request->search.'%')
                ->orWhere('categoria', 'like', '%'.$request->search.'%');
        }

        $items = $query->get();

        $stockCritico = Inventario::where('estado', 'like', '%Crítico%')->count();
        $enUsoObra = Inventario::where('estado', 'like', '%En Uso%')->count();
        $nuevosIngresos = Inventario::where('created_at', '>=', now()->subWeek())->count();

        return view('admin.menu', compact('items', 'stockCritico', 'enUsoObra', 'nuevosIngresos'));
    }

    public function store(Request $request)
    {
        $cooldownKey = 'inventory-save:'.$request->user()->getAuthIdentifier();

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $waitSeconds = RateLimiter::availableIn($cooldownKey);

            return back()
                ->withErrors([
                    'save' => "Espera {$waitSeconds} segundos antes de guardar otro ítem.",
                ])
                ->with('save_wait_seconds', $waitSeconds);
        }

        RateLimiter::hit($cooldownKey, 15);

        $validated = $request->validate([
            'nombre_item' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'stock_actual' => 'required|integer',
            'unidad' => 'required|string',
            'ubicacion' => 'required|string',
            'estado' => 'required|string',
        ]);

        Inventario::create($validated);

        return redirect()->route('inventario.index')->with('success', 'Ítem agregado correctamente.');
    }

    public function destroy($id)
    {
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
