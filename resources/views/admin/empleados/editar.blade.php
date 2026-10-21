<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado - GestiónCash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
        <div class="flex items-center gap-3">
            <span class="text-xl font-bold tracking-wide">GestiónCash</span>
        </div>
        <div>
            <a href="{{ route('admin.empleados.index') }}" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
                ← Volver al Panel
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
        <main class="flex-1 overflow-y-auto p-8 flex items-center justify-center">
            <div class="max-w-xl w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">

                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold text-slate-800">Editar Datos del Empleado</h1>
                    <p class="text-xs text-slate-500 mt-1">Modifica la información de acceso o deja la contraseña en blanco si no deseas cambiarla.</p>
                </div>

                <!-- Formulario de Edición -->
                <form action="{{ route('admin.empleados.update', $empleado->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name', $empleado->name) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $empleado->email) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nueva Contraseña <span class="text-slate-400 font-normal">(Opcional)</span></label>
                        <input type="password" name="password" placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Rellénalo solo si necesitas reestablecer la clave del empleado.</p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.empleados.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

</body>
</html>
