<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cobros</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <div class="max-w-md mx-auto p-4 space-y-4">

        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Mis Cobros</h1>
                <p class="text-xs text-gray-500">Resumen de actividad</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-emerald-600 font-bold uppercase tracking-wider block">Total Hoy</span>
                <span class="text-xl font-extrabold text-emerald-700">${{ number_format($totalHoy, 2) }}</span>
            </div>
        </div>


        <a href="{{ route('cobros.create') }}" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl shadow-sm flex items-center justify-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Cobro
        </a>

        <div class="space-y-3">
            @forelse ($cobros as $cobro)
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div class="space-y-1">
                        <span class="font-bold text-gray-800 text-sm block">{{ $cobro->cliente->nombre }}</span>
                        <span class="text-xs text-gray-500 block">{{ $cobro->concepto }}</span>
                        <span class="text-[10px] font-mono text-gray-400 block">
                            {{ \Carbon\Carbon::parse($cobro->fecha_de_pago)->format('d/m/Y H:i') }} hs
                        </span>
                    </div>

                    <div class="text-right space-y-2">
                        <span class="font-black text-emerald-700 text-base block">
                            ${{ number_format($cobro->cantidad, 2) }}
                        </span>


                        <a href="{{ route('recibo.publico', ['token' => $cobro->receipt_token]) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition">
                            Recibo
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl text-center border border-gray-100 text-gray-400">
                    <p class="text-sm">No has registrado cobros aún.</p>
                </div>
            @endforelse
        </div>


        <div class="mt-4">
            {{ $cobros->links() }}
        </div>

    </div>

</body>
</html>
