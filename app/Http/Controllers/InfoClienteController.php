<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InfoClienteController extends Controller
{
    // Compras del mes necesarias para que un cliente aparezca en el directorio (cliente habitual)
    private const COMPRAS_MINIMAS = 3;

    /**
     * Mes actual en hora de Uruguay, convertido a la zona horaria con la que
     * Laravel guarda created_at.
     */
    private function rangoDelMes(): array
    {
        $ahora = Carbon::now('America/Montevideo');
        $zona = config('app.timezone');

        return [
            $ahora->copy()->startOfMonth()->setTimezone($zona),
            $ahora->copy()->endOfMonth()->setTimezone($zona),
        ];
    }

    public function index(Request $request)
    {
        [$desde, $hasta] = $this->rangoDelMes();

        // Se compara por nombre en minúsculas y sin espacios sobrantes,
        // para que "Juan" y "juan " se cuenten como el mismo cliente.
        $clave = fn ($nombre) => mb_strtolower(trim((string) $nombre));

        // Clientes ya registrados en la tabla "clientes"
        $registrados = Cliente::all()->keyBy(fn ($c) => $clave($c->nombre));

        // Clientes que aparecen en los cobros, aunque nunca se hayan registrado
        $deCobros = DB::table('cobros')
            ->select('nombre_cliente', DB::raw('MAX(telefono) as telefono'))
            ->groupBy('nombre_cliente')
            ->get()
            ->keyBy(fn ($c) => $clave($c->nombre_cliente));

        // Compras de cada cliente en el mes actual
        $comprasMes = DB::table('cobros')
            ->select('nombre_cliente', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('nombre_cliente')
            ->get()
            ->groupBy(fn ($c) => $clave($c->nombre_cliente))
            ->map(fn ($grupo) => (int) $grupo->sum('total'));

        $clientes = $registrados->keys()
            ->merge($deCobros->keys())
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->map(function ($k) use ($registrados, $deCobros, $comprasMes) {
                $registro = $registrados->get($k);
                $cobro = $deCobros->get($k);

                return [
                    'id' => $registro?->id,
                    'nombre' => $registro?->nombre ?? $cobro?->nombre_cliente,
                    'telefono' => $registro?->telefono ?: $cobro?->telefono,
                    'email' => $registro?->email,
                    'total_compras' => $comprasMes->get($k, 0),
                ];
            });

        // Solo aparecen los clientes habituales (3 o más compras en el mes) y los que ya
        // fueron registrados a mano en la tabla "clientes" (id distinto de null).
        $clientes = $clientes->filter(
            fn ($c) => $c['id'] !== null || $c['total_compras'] >= self::COMPRAS_MINIMAS
        )->values();

        // Búsqueda por nombre, teléfono o correo (sin distinguir mayúsculas)
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $buscado = mb_strtolower($search);

            $clientes = $clientes->filter(
                fn ($c) => str_contains(mb_strtolower((string) $c['nombre']), $buscado)
                    || str_contains(mb_strtolower((string) $c['telefono']), $buscado)
                    || str_contains(mb_strtolower((string) $c['email']), $buscado)
            )->values();
        }

        return Inertia::render('admin/infodeclientes/index', [
            'clientes' => $clientes,
            'filters' => ['search' => $search],
        ]);
    }

    public function show($nombre)
    {
        $nombreDecoded = urldecode($nombre);
        [$desde, $hasta] = $this->rangoDelMes();

        // Solo lectura: antes se hacía firstOrCreate y se intentaba insertar un
        // cliente nuevo, que fallaba porque "telefono" no admite valor vacío.
        $registro = Cliente::where('nombre', $nombreDecoded)->first();

        $telefono = $registro?->telefono ?: DB::table('cobros')
            ->where('nombre_cliente', $nombreDecoded)
            ->max('telefono');

        $totalCompras = DB::table('cobros')
            ->where('nombre_cliente', $nombreDecoded)
            ->whereBetween('created_at', [$desde, $hasta])
            ->count();

        // Últimos cobros del cliente para el historial de la ficha
        $cobros = DB::table('cobros')
            ->where('nombre_cliente', $nombreDecoded)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get([
                'id', 'tipo_registro', 'concepto', 'monto', 'metodo_pago',
                'mano_de_obra', 'motivo_no_realizado', 'productos', 'created_at',
            ])
            ->map(function ($cobro) {
                // "productos" se guarda como JSON; acá no hay cast del modelo.
                $cobro->productos = is_string($cobro->productos)
                    ? (json_decode($cobro->productos, true) ?: [])
                    : ($cobro->productos ?? []);

                return $cobro;
            });

        return Inertia::render('admin/infodeclientes/ver', [
            'cliente' => [
                'id' => $registro?->id,
                'nombre' => $nombreDecoded,
                'name' => $nombreDecoded,
                'telefono' => $telefono,
                'phone' => $telefono,
                'email' => $registro?->email,
                'total_compras' => $totalCompras,
            ],
            'cobros' => $cobros,
        ]);
    }

    public function edit($nombre)
    {
        $nombreDecoded = urldecode($nombre);

        $registro = Cliente::where('nombre', $nombreDecoded)->first();

        $cliente = [
            'id' => $nombreDecoded,
            'name' => $nombreDecoded,
            'phone' => $registro?->telefono ?: DB::table('cobros')
                ->where('nombre_cliente', $nombreDecoded)
                ->max('telefono'),
            'email' => $registro?->email ?? '',
        ];

        return Inertia::render('admin/infodeclientes/editarcliente', [
            'cliente' => $cliente,
        ]);
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
        // La columna "telefono" no acepta NULL, por eso se guarda vacío en vez de null.
        $cliente->telefono = $request->input('phone') ?? '';
        $cliente->email = $request->input('email');
        $cliente->save();

        return redirect()
            ->route('admin.infodeclientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }
}
