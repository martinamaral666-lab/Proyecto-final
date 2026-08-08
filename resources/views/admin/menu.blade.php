<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800" x-data="{ menuOpen: false, modalVentas: false }">

    <header class="bg-emerald-800 text-white px-6 py-4 flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-6">
            <span class="font-black text-xl uppercase tracking-wider">Logo</span>
            <h1 class="text-lg font-semibold border-l border-emerald-600 pl-4">Panel Administrador</h1>
        </div>
        <div class="flex items-center space-x-6">
            <div class="text-right text-xs">
                <p class="font-bold">ID: {{ auth()->id() ?? '1' }}</p>
                <p class="text-emerald-200">Rol: Administrador</p>
            </div>
            <button @click="menuOpen = !menuOpen" class="p-1 hover:bg-emerald-700 rounded-lg transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <div x-show="menuOpen"
                     x-cloak
                    class="fixed inset-y-0 right-0 w-72 bg-emerald-950 text-white shadow-2xl z-50 flex flex-col justify-between">
    <div>
<div>

        <div class="bg-emerald-900 p-4 flex items-center justify-between border-b border-emerald-800 shadow-sm">
            <h3 class="font-bold text-sm uppercase tracking-wider text-emerald-100">Menú de Gestión</h3>
            <button @click="menuOpen = false" class="text-emerald-300 hover:text-white focus:outline-none text-xl font-bold p-1">
                &times;
            </button>
        </div>

</div>


            </button>
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-68px)]">

        <div class="w-full md:w-80 bg-emerald-900 text-white p-6 space-y-6 shrink-0 border-r border-emerald-800 flex flex-col justify-between">
            <div class="space-y-6">
                <div>
                    <h2 class="text-base font-bold uppercase tracking-wider text-emerald-100">Acciones Principales</h2>
                    <div class="w-10 h-1 bg-red-500 mt-1 mb-4"></div>
                </div>

                <button @click="modalVentas = true" class="w-full bg-emerald-700 hover:bg-emerald-600 border border-emerald-500/50 text-white p-4 rounded-xl flex items-center justify-between transition shadow-md group">
                    <div class="flex items-center gap-3">
                        <div class="text-left">
                            <span class="font-bold block text-sm">Ver Ventas y Reportes</span>
                            <span class="text-[11px] text-emerald-200">Totales y gráfico de ingresos</span>
                        </div>
                    </div>
                    <span class="text-emerald-300 group-hover:translate-x-1 transition">→</span>
                </button>

                <a href="{{ route('cobros.create') }}" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white p-4 rounded-xl flex items-center gap-3 font-bold text-sm transition shadow-md">

                    <span>Registrar Nuevo Cobro</span>
                </a>
            </div>

            <div class="border-t border-emerald-800/80 pt-4 text-xs text-emerald-300">
                <p class="font-semibold text-emerald-200">Estado del Sistema</p>
                <p class="mt-1">Servidor activo y sincronizado.</p>
            </div>
        </div>


        <div class="flex-1 bg-gray-50 p-6 md:p-8">
            <div class="max-w-4xl mx-auto space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div>
                        <h3 class="text-xl font-bold text-emerald-900">Historial de Recibos</h3>
                        <p class="text-xs text-gray-500">Últimas transacciones registradas</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($ultimosCobros as $cobro)
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between hover:border-emerald-500 transition">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $cobro->cliente->nombre ?? 'Cliente sin nombre' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $cobro->concepto }} • {{ $cobro->created_at->format('d/m/Y H:i') }} hs</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-extrabold text-emerald-700 text-base">${{ number_format($cobro->cantidad, 2) }}</span>
                                <a href="{{ route('recibo.publico', $cobro->token ?? 'sin-token') }}" target="_blank" class="bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 text-xs px-3 py-1.5 rounded-lg font-bold transition">
                                    Ver Recibo ↗
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-xl border text-center text-gray-500">No hay cobros registrados.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="modalVentas"
             x=cloak
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-cloak style="display: none;">
            <div @click.away="modalVentas = false" class="bg-white rounded-2xl max-w-3xl w-full p-6 space-y-6 shadow-2xl relative">

                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-lg font-bold text-emerald-900">Reporte General de Ventas</h3>
                    <button @click="modalVentas = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl">
                        <span class="text-xs font-bold text-emerald-700 uppercase block">Ventas Hoy</span>
                        <span class="text-2xl font-black text-emerald-900 mt-1 block">${{ number_format($ventasHoy, 2) }}</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl">
                        <span class="text-xs font-bold text-blue-700 uppercase block">Esta Semana</span>
                        <span class="text-2xl font-black text-blue-900 mt-1 block">${{ number_format($ventasSemana, 2) }}</span>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 p-4 rounded-xl">
                        <span class="text-xs font-bold text-purple-700 uppercase block">Este Mes</span>
                        <span class="text-2xl font-black text-purple-900 mt-1 block">${{ number_format($ventasMes, 2) }}</span>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Ventas de los últimos 7 días</h4>
                    <canvas id="graficoVentas" class="max-h-60 w-full"></canvas>
                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('graficoVentas').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dias) !!},
                    datasets: [{
                        label: 'Ventas ($)',
                        data: {!! json_encode($totalesPorDia) !!},
                        backgroundColor: '#059669',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>
