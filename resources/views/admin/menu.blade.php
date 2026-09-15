<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .transition-sidebar { transition: margin-left 0.3s ease-in-out, transform 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

    <!-- ENCABEZADO UNIFICADO -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
        <div class="flex items-center gap-4">
            <button id="btnToggleSidebar" class="p-2 rounded-lg bg-[#033b2b] hover:bg-[#02281d] text-emerald-200 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-white tracking-wide">GestiónCash</span>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 relative overflow-hidden">

        <!-- BARRA LATERAL -->
        <aside id="sidebar" class="w-64 bg-[#044e39] text-white p-5 flex flex-col shrink-0 transition-sidebar z-20">
            <h2 class="text-xs font-bold text-emerald-300/70 uppercase tracking-wider mb-4">Acciones Principales</h2>

            <nav class="space-y-3">
                <button id="btnToggleReportes" class="w-full text-left p-3.5 bg-emerald-800/40 hover:bg-emerald-700/50 rounded-xl border border-emerald-600/30 transition-all flex flex-col group focus:outline-none">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Ver Ventas y Reportes</span>
                    <span class="text-xs text-emerald-300/80 mt-0.5">Totales y gráfico de ingresos</span>
                </button>

                <a href="{{ route('cobros.index') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Cobros</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Información detallada de cobros</span>
                </a>

                <a href="#" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Clientes</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Información de cada cliente</span>
                </a>

                <a href="{{ route('admin.empleados.create') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Registrar Empleado</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Crear accesos para personal</span>
                </a>
            </nav>
</aside>

                <!-- SECCIÓN REPORTES Y GRÁFICOS  -->
                <div id="seccionReportes" class="hidden space-y-8 transition-all">
                    <!-- TARJETAS DE MÉTRICAS -->
                    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-2xl shadow-sm">
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wide">Ventas Hoy</span>
                            <span class="block text-2xl font-black text-emerald-900 mt-1">${{ number_format($ventasHoy ?? 0, 2) }}</span>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 p-6 rounded-2xl shadow-sm">
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wide">Esta Semana</span>
                            <span class="block text-2xl font-black text-blue-900 mt-1">${{ number_format($ventasSemana ?? 0, 2) }}</span>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 p-6 rounded-2xl shadow-sm">
                            <span class="text-xs font-bold text-purple-700 uppercase tracking-wide">Este Mes</span>
                            <span class="block text-2xl font-black text-purple-900 mt-1">${{ number_format($ventasMes ?? 0, 2) }}</span>
                        </div>
                    </section>

                    <!-- GRÁFICO CON ALTURA EXPLÍCITA -->
                    <section class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 mb-4">Rendimiento Semanal</h3>
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="graficoVentas"></canvas>
                        </div>
                    </section>
                </div>

                <!-- SECCIÓN INVENTARIO  -->
<div id="seccionInventario" class="max-w-6xl mx-auto px-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Inventario General</h1>
    </div>

    <!-- Tarjetas de Resumen Superior -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Artículos con Stock Crítico</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">({{ $stockCritico ?? 0 }})</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Materiales en Uso (Obra)</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">({{ $enUsoObra ?? 0 }})</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Nuevos Ingresos (Semana)</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">({{ $nuevosIngresos ?? 0 }})</h3>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('inventario.index') }}" class="w-full flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-1/3 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar artículo, categoría..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-[#044e39] text-sm">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">Buscar</button>
            </div>
        </form>

        <a href="{{ route('inventario.crear') }}" class="w-full md:w-auto bg-[#044e39] hover:bg-[#033b2b] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 shrink-0">
            <span>+</span> Agregar Nuevo Ítem
        </a>
    </div>

    <!-- Inventario -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Nombre del Ítem</th>
                        <th class="py-4 px-6">Categoría</th>
                        <th class="py-4 px-6">Stock Actual</th>
                        <th class="py-4 px-6">Unidad</th>
                        <th class="py-4 px-6">Ubicación</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @isset($items)
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-800">{{ $item->nombre_item }}</td>
                            <td class="py-4 px-6">{{ $item->categoria }}</td>
                            <td class="py-4 px-6 font-medium">{{ $item->stock_actual }}</td>
                            <td class="py-4 px-6">{{ $item->unidad }}</td>
                            <td class="py-4 px-6">{{ $item->ubicacion }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ strtolower($item->estado) == 'critico' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($item->estado) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Botón Editar -->
                                    <a href="{{ route('inventario.editar', $item->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <!-- Botón Asignar a En Uso (Obra) -->
                                    <form action="{{ route('inventario.uso', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Marcar en uso">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('inventario.destroy', $item->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ítem?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400 font-medium">No hay registros encontrados en el inventario.</td>
                        </tr>
                        @endforelse
                    @endisset
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const btnToggleSidebar = document.getElementById('btnToggleSidebar');
            const sidebar = document.getElementById('sidebar');

            if (btnToggleSidebar && sidebar) {
                btnToggleSidebar.addEventListener('click', function () {
                    sidebar.classList.toggle('-ml-64');
                });
            }


            let miGrafico = null;
            const canvas = document.getElementById('graficoVentas');

            function crearGrafico() {
                if (!canvas || miGrafico) return;

                const ctx = canvas.getContext('2d');
                miGrafico = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($dias ?? ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']) !!},
                        datasets: [{
                            label: 'Ventas ($)',
                            data: {!! json_encode($totalesPorDia ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                            backgroundColor: '#044e39',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) { return '$' + value; }
                                }
                            }
                        }
                    }
                });
            }

            // Mostrar/Ocultar Reportes y alternar con el Inventario
            const btnToggleReportes = document.getElementById('btnToggleReportes');
            const seccionReportes = document.getElementById('seccionReportes');
            const seccionInventario = document.getElementById('seccionInventario');

            if (btnToggleReportes && seccionReportes && seccionInventario) {
                btnToggleReportes.addEventListener('click', function () {
                    if (seccionReportes.style.display === 'none' || seccionReportes.style.display === '') {
                        seccionReportes.style.display = 'block';
                        seccionInventario.style.display = 'none';

                        setTimeout(() => {
                            if (!miGrafico) {
                                crearGrafico();
                            } else {
                                miGrafico.resize();
                                miGrafico.update();
                            }
                        }, 50);
                    } else {
                        seccionReportes.style.display = 'none';
                        seccionInventario.style.display = 'block';
                    }
                });
            }
        });
    </script>
</body>
</html>
