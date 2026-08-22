<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cobros</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

    <!-- ENCABEZADO -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.menu') ?? url()->previous() }}" class="p-2 rounded-lg bg-[#033b2b] hover:bg-[#02281d] text-emerald-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="font-black text-2xl tracking-wider text-white">LOGO</span>
        </div>
        <div class="flex items-center gap-6">
            <span class="text-xs text-emerald-200/80">Panel Administrador</span>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 p-6 max-w-4xl mx-auto w-full space-y-6">

        <!-- CABECERA DE SECCIÓN -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-black text-slate-900">Mis Cobros</h1>
                <p class="text-xs text-gray-500 mt-0.5">Resumen de actividad e historial de recibos</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Hoy</span>
                <span class="text-xl font-black text-emerald-800">${{ number_format($totalHoy ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- LISTADO DE COBROS -->
        <div class="space-y-3">
            @forelse ($cobros as $cobro)
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition-all hover:border-emerald-200">
                    <div class="space-y-1">
                        <!-- Nombre del cliente -->
                        <span class="font-bold text-gray-800 text-sm block">
                            {{ $cobro->nombre ?? $cobro->cliente->nombre ?? 'Cliente' }}
                        </span>

                        <!-- Concepto -->
                        <span class="text-xs text-gray-500 block">
                            {{ $cobro->concepto ?? 'Cuota de servicio' }}
                        </span>

                        <!-- Fecha y hora exacta -->
                        <span class="text-[10px] font-mono text-gray-400 block">
                            {{ \Carbon\Carbon::parse($cobro->created_at)->format('d/m/Y H:i') }} hs
                        </span>
                    </div>

                    <div class="text-right space-y-1">
                        <!-- Monto correcto -->
                        <span class="font-black text-emerald-700 text-base block">
                            ${{ number_format($cobro->monto ?? 0, 2) }}
                        </span>

                        <!-- Enlace de recibo -->
                        @if(!empty($cobro->receipt_token))
                            <a href="{{ route('recibo.publico', ['token' => $cobro->receipt_token]) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                                Recibo
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        @else
                            <span class="text-xs text-gray-400 block">Sin recibo</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-sm text-gray-500">No hay cobros registrados todavía.</p>
                </div>
            @endforelse
        </div>

        <!-- PAGINACIÓN -->
        <div class="mt-4">
            {{ $cobros->links() }}
        </div>

    </main>

</body>
</html>
