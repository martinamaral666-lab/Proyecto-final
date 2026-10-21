<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empleados - GestiónCash</title>
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

            <a href="{{ route('admin.empleados.index') }}" class="block p-3.5 bg-emerald-800/60 rounded-xl border border-emerald-600/50 shadow-sm">
                <span class="font-bold text-sm text-white">Empleados</span>
                <span class="block text-xs text-emerald-200 mt-0.5">Control de personal y accesos</span>
            </a>

            <a href="{{ route('admin.infodeclientes.index') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                <span class="font-bold text-sm text-white group-hover:text-emerald-200">Clientes</span>
                <span class="block text-xs text-emerald-300/80 mt-0.5">Información de cada cliente</span>
            </a>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto">

                <!-- Encabezado de la sección y botón de crear -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Panel de Empleados</h1>
                        <p class="text-sm text-slate-500 mt-1">Personal registrado y accesos habilitados en el sistema.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.empleados.create') }}" class="px-4 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                            + Registrar Empleado
                        </a>
                    </div>
                </div>

                <!-- Listado en formato de Tarjeta/Carpeta -->
                <div class="space-y-4">
                    @forelse($empleados ?? [] as $empleado)
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">

                            <!-- Información del empleado -->
                            <div class="flex items-center gap-4">

                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-bold text-slate-800 text-base">
                                            {{ $empleado->name }}
                                        </h3>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Correo: <span class="text-slate-700 font-medium">{{ $empleado->email }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Botones de Acción (Editar contraseña y Dar de baja) -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.empleados.edit', $empleado->id) }}"
                                   class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl border border-slate-200 transition-all flex items-center gap-1.5">
                                 Editar cuenta
                                </a>

                                <form action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de dar de baja a este empleado?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-xs rounded-xl border border-rose-200 transition-all flex items-center gap-1.5">
                                        Dar de baja
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                            No hay empleados registrados todavía. Haz clic en "Registrar Empleado" para agregar uno.
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

</body>
</html>
