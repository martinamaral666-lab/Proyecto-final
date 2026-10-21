<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - GestiónCash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
        <div class="flex items-center gap-3">
            <span class="text-xl font-bold tracking-wide">GestiónCash</span>
        </div>
        <div>
            <a href="{{ route('admin.menu') }}" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
                ← Volver al Menú
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <!-- MENÚ LATERAL -->
        <aside class="w-64 bg-[#044e39] text-white flex flex-col p-4 space-y-2 shrink-0">
            <div class="text-xs font-semibold uppercase text-emerald-300/70 px-3 mb-2">Acciones Principales</div>

            <a href="{{ route('admin.menu') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                <span class="font-bold text-sm text-white group-hover:text-emerald-200">Inventario</span>
                <span class="block text-xs text-emerald-300/80 mt-0.5">Gestión general de stock</span>
            </a>

            <a href="{{ route('admin.empleados.index') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                <span class="font-bold text-sm text-white group-hover:text-emerald-200">Empleados</span>
                <span class="block text-xs text-emerald-300/80 mt-0.5">Control de personal y accesos</span>
            </a>

            <a href="{{ route('admin.infodeclientes.index') }}" class="block p-3.5 bg-emerald-800/60 rounded-xl border border-emerald-600/50 shadow-sm">
                <span class="font-bold text-sm text-white">Clientes</span>
                <span class="block text-xs text-emerald-200 mt-0.5">Información de cada cliente</span>
            </a>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto">

                <!-- Encabezado de la sección -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Directorio de Clientes</h2>
                    <p class="text-sm text-slate-500">Información de contacto y registro de clientes del sistema.</p>
                </div>

                @forelse($clientes as $cliente)
                    <!-- Tarjeta contenedora de cada cliente -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-4 flex items-center justify-between">

                        <!-- Info del Cliente (Lado Izquierdo) -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="font-bold text-slate-800 text-base">
                                    {{ $cliente->nombre }}
                                </h3>
                                <span class="text-xs text-slate-400 font-medium">
                                    ({{ $cliente->telefono ?? 'Sin teléfono' }})
                                </span>

                                @if($cliente->total_compras >= 3)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        Habitual ({{ $cliente->total_compras }} compras)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Ocasional ({{ $cliente->total_compras }})
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-500">
                                Email: <span class="font-medium text-slate-700">{{ $cliente->email ?? 'No registrado' }}</span>
                            </p>
                        </div>

                        <!-- Botones de Acción (Lado Derecho) -->
                     <div class="flex items-center gap-2">
    <a href="{{ route('admin.infodeclientes.show', $cliente->nombre) }}"
       class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-medium text-xs rounded-xl border border-emerald-200 transition-all flex items-center gap-1.5">
        Ver Detalladamente
    </a>

    <a href="{{ route('admin.infodeclientes.edit', $cliente->nombre) }}"
       class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl border border-slate-200 transition-all flex items-center gap-1.5">
        Editar
    </a>
</div>

                    </div>
                @empty
                    <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                        No hay clientes registrados en el sistema todavía.
                    </div>
                @endforelse

            </div>
        </main>
    </div>

</body>
</html>
