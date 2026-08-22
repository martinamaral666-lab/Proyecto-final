<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <!-- Tailwind CSS CDN & Chart.js -->
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
            <!-- Botón desplegable Sidebar -->
            <button id="btnToggleSidebar" class="p-2 rounded-lg bg-[#033b2b] hover:bg-[#02281d] text-emerald-200 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex items-center gap-2">
                <span class="font-black text-2xl tracking-wider text-white">LOGO</span>
                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-800/60 text-emerald-200 rounded-full border border-emerald-600/40">Panel Administrador</span>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <span class="text-xs text-emerald-200/80 hidden sm:inline">ID: <strong>2</strong> | Rol: <strong class="text-white">Admin</strong></span>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 relative overflow-hidden">

        <!-- BARRA LATERAL (SIDEBAR) -->
        <aside id="sidebar" class="w-64 bg-[#044e39] text-white p-5 flex flex-col shrink-0 transition-sidebar z-20">
            <h2 class="text-xs font-bold text-emerald-300/70 uppercase tracking-wider mb-4">Acciones Principales</h2>

            <nav class="space-y-3">
                <!-- 1. Ver Ventas y Reportes -->
                <button id="btnToggleReportes" class="w-full text-left p-3.5 bg-emerald-800/40 hover:bg-emerald-700/50 rounded-xl border border-emerald-600/30 transition-all flex flex-col group focus:outline-none">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Ver Ventas y Reportes</span>
                    <span class="text-xs text-emerald-300/80 mt-0.5">Totales y gráfico de ingresos</span>
                </button>

                <!-- 2. Cobros -->
                <a href="{{ route('cobros.index') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Cobros</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Información detallada de cobros</span>
                </a>

                <!-- 3. Clientes -->
                <a href="#" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Clientes</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Información de cada cliente</span>
                </a>

                <!-- 4. Registrar Empleado -->
                <a href="{{ route('admin.empleados.create') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Registrar Empleado</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Crear accesos para personal</span>
                </a>

                <!-- 5. Registrar Cliente -->
                <a href="{{ route('clientes.create') }}" class="block p-3.5 bg-emerald-900/30 hover:bg-emerald-800/40 rounded-xl border border-emerald-700/30 transition-all group">
                    <span class="font-bold text-sm text-white group-hover:text-emerald-200">Registrar Cliente</span>
                    <span class="block text-xs text-emerald-300/80 mt-0.5">Alta de nuevos clientes</span>
                </a>
            </nav>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="max-w-6xl mx-auto space-y-8">

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

                    <!-- GRÁFICO -->
                    <section class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                        <h3 class="text-base font-bold text-slate-800 mb-4">Rendimiento Semanal</h3>
                        <div class="h-72 w-full">
                            <canvas id="graficoVentas"></canvas>
                        </div>
                    </section>
                </div>

            </div>
        </main>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Mostrar/Ocultar Sección de Reportes
            const btnToggleReportes = document.getElementById('btnToggleReportes');
            const seccionReportes = document.getElementById('seccionReportes');

            if (btnToggleReportes && seccionReportes) {
                btnToggleReportes.addEventListener('click', function () {
                    seccionReportes.classList.toggle('hidden');
                });
            }

            // 2. Desplegar/Plegar Sidebar
            const btnToggleSidebar = document.getElementById('btnToggleSidebar');
            const sidebar = document.getElementById('sidebar');

            if (btnToggleSidebar && sidebar) {
                btnToggleSidebar.addEventListener('click', function () {
                    sidebar.classList.toggle('-ml-64');
                });
            }

            // 3. Inicializar Chart.js con respaldo por si no hay datos del controlador
            const canvas = document.getElementById('graficoVentas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($dias ?? ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']) !!},
                        datasets: [{
                            label: 'Ventas ($)',
                            data: {!! json_encode($totalesPorDia ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                            backgroundColor: '#059669',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // 3. Mostrar/Ocultar Reportes y refrescar gráfico
            const btnToggleReportes = document.getElementById('btnToggleReportes');
            const seccionReportes = document.getElementById('seccionReportes');

            if (btnToggleReportes && seccionReportes) {
                btnToggleReportes.addEventListener('click', function () {
                    if (seccionReportes.style.display === 'none' || seccionReportes.style.display === '') {
                        seccionReportes.style.display = 'block';

                        // Creamos el gráfico o lo redimensionamos justo al abrirse la sección
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
                    }
                });
            }
        });
    </script>
</body>
</html>
