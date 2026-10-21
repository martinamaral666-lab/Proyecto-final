<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Cliente - GestiónCash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
        <div class="flex items-center gap-3">
            <span class="text-xl font-bold tracking-wide">GestiónCash</span>
        </div>
        <div>
            <a href="{{ route('admin.infodeclientes.index') }}" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
                ← Volver al Directorio
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
                <span class="block text-xs text-emerald-200 mt-0.5">Control de personal y accesos</span>
            </a>

            <a href="{{ route('admin.infodeclientes.index') }}" class="block p-3.5 bg-emerald-800/60 rounded-xl border border-emerald-600/50 shadow-sm">
                <span class="font-bold text-sm text-white">Clientes</span>
                <span class="block text-xs text-emerald-200 mt-0.5">Información de cada cliente</span>
            </a>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-3xl mx-auto space-y-6">

                <!-- Encabezado de la ficha -->
                <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-slate-800">{{ $cliente->name }}</h1>
                            <p class="text-xs text-slate-500 mt-0.5">Ficha detallada del cliente habitual</p>
                        </div>
                    </div>
                    <div>
                        @if(method_exists($cliente, 'isRegularCustomer') && $cliente->isRegularCustomer())
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                Habitual ({{ $cliente->currentMonthSalesCount() }} compras este mes)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Ocasional
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Tarjeta de Información Detallada -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Información de Contacto</h2>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block text-xs text-slate-400 font-medium">Teléfono / WhatsApp</span>
                            <span class="font-semibold text-slate-700">{{ $cliente->phone ?? 'No especificado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 font-medium">Correo Electrónico</span>
                            <span class="font-semibold text-slate-700">{{ $cliente->email ?? 'No registrado' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Historial o Registros de Compras (Opcional / Espacio para futuras expansiones) -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Historial de Actividad Reciente</h2>

                    <div class="text-center py-6 text-slate-400 text-sm">
                        Registros de compras, encargos de material eléctrico o mano de obra vinculados.
                    </div>
                </div>

                <!-- Botones de navegación inferiores -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.infodeclientes.edit', $cliente->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                        Editar este cliente
                    </a>
                    <a href="{{ route('admin.infodeclientes.index') }}" class="px-5 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                        Volver al listado
                    </a>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
