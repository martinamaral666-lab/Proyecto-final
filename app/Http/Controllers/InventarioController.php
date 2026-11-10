<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cobros;
use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class InventarioController extends Controller
{
    // Por debajo de este stock, un ítem se considera crítico
    private const STOCK_CRITICO = 5;

    public function index(Request $request)
    {
        $query = Inventario::query();

        // El where agrupado evita que el orWhere rompa otros filtros que agregues después
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nombre_item', 'like', '%'.$search.'%')
                  ->orWhere('categoria', 'like', '%'.$search.'%');
            });
        }

        // es_critico se calcula con el stock actual, así el estado se actualiza solo
        // cuando el stock baja (por ejemplo, después de una venta) o se repone.
        $items = $query->get()->map(function ($item) {
            $item->es_critico = $item->stock_actual < self::STOCK_CRITICO;

            return $item;
        });

        $stockCritico = Inventario::where('stock_actual', '<', self::STOCK_CRITICO)->count();
        $enUsoObra = Inventario::where('estado', 'like', '%En Uso%')->count();
        $nuevosIngresos = Inventario::where('created_at', '>=', now()->subWeek())->count();

        /*
        | VENTAS Y GRÁFICO SEMANAL
        | Los días se calculan en hora de Uruguay y se convierten a la zona
        | horaria de la app, que es en la que Laravel guarda created_at.
        | Si no, una venta hecha de noche caía en el día equivocado.
        */
        $now = Carbon::now('America/Montevideo');
        $appTz = config('app.timezone');

        $rango = fn (Carbon $desde, Carbon $hasta) => [
            $desde->copy()->setTimezone($appTz),
            $hasta->copy()->setTimezone($appTz),
        ];

        $ventasHoy = (float) Cobros::whereBetween(
            'created_at',
            $rango($now->copy()->startOfDay(), $now->copy()->endOfDay())
        )->sum('monto');

        $ventasSemana = (float) Cobros::whereBetween(
            'created_at',
            $rango($now->copy()->startOfWeek(), $now->copy()->endOfWeek())
        )->sum('monto');

        $ventasMes = (float) Cobros::whereBetween(
            'created_at',
            $rango($now->copy()->startOfMonth(), $now->copy()->endOfMonth())
        )->sum('monto');

        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $inicioSemana = $now->copy()->startOfWeek();
        $totalesPorDia = [];

        for ($i = 0; $i < 7; $i++) {
            $dia = $inicioSemana->copy()->addDays($i);

            $totalesPorDia[] = (float) Cobros::whereBetween(
                'created_at',
                $rango($dia->copy()->startOfDay(), $dia->copy()->endOfDay())
            )->sum('monto');
        }

        return Inertia::render('admin/menu', [
            'items' => $items,
            'stockCritico' => $stockCritico,
            'enUsoObra' => $enUsoObra,
            'nuevosIngresos' => $nuevosIngresos,
            'ventasHoy' => $ventasHoy,
            'ventasSemana' => $ventasSemana,
            'ventasMes' => $ventasMes,
            'dias' => $dias,
            'totalesPorDia' => $totalesPorDia,
            'filters' => $request->only(['search']),
            'routes' => [
                'cuenta' => route('admin.cuenta'),
                'cobros' => route('cobros.index'),
                'empleadosCreate' => route('admin.empleados.create'),
                'empleadosIndex' => route('admin.empleados.index'),
                'clientesIndex' => route('admin.infodeclientes.index'),
                'modulos' => route('admin.modulos'),
                'inventarioCrear' => route('inventario.crear'),
                'inventarioIndex' => route('admin.menu'),
            ]
        ]);
    }

    public function create()
    {
        $cooldownKey = 'inventory-save:'.request()->user()->getAuthIdentifier();
        $waitSeconds = RateLimiter::availableIn($cooldownKey);

        return Inertia::render('admin/inventario', [
            'saveWaitSeconds' => max(0, $waitSeconds),
            'routes' => [
                'adminMenu' => route('admin.menu'),
                'inventarioIndex' => route('admin.menu'),
                'inventarioStore' => route('inventario.store'),
                'logout' => route('logout'),
            ]
        ]);
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
            'precio' => 'required|numeric|min:0',
        ]);

        Inventario::create($validated);

        return redirect()->route('admin.menu')->with('success', 'Ítem agregado correctamente.');
    }

    // NUEVO: muestra el formulario de edición
   public function edit($id)
{
    $item = Inventario::findOrFail($id);

    return Inertia::render('admin/editar', [
        'item' => $item,
    ]);
}

    // NUEVO: guarda los cambios del formulario de edición
    public function update(Request $request, $id)
    {
        $item = Inventario::findOrFail($id);

        $validated = $request->validate([
            'nombre_item' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'stock_actual' => 'required|integer',
            'unidad' => 'required|string',
            'ubicacion' => 'required|string',
            'estado' => 'required|string',
            'precio' => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('admin.menu')->with('success', 'Ítem actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = Inventario::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.menu')->with('success', 'Ítem eliminado.');
    }

    public function uso($id)
    {
        $item = Inventario::findOrFail($id);

        $item->estado = 'en uso';
        $item->save();

        return redirect()->back()->with('success', 'El ítem está en uso.');
    }
}
