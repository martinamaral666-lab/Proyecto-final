<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago #{{ $cobro->id }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="print-card w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

        <div class="bg-emerald-700 text-white text-center p-6 relative">
            <div class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-800/60 rounded-full text-xs font-semibold text-emerald-100 mb-3 border border-emerald-500/30">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                Pago Confirmado
            </div>
            <h1 class="text-xl font-extrabold tracking-wide">Comprobante Digital</h1>
            <p class="text-[11px] text-emerald-100 mt-1 font-mono break-all opacity-90">
                Transacción #{{ $cobro->receipt_token }}
            </p>
        </div>


        <div class="p-6">

            <div class="text-center pb-6 border-b border-dashed border-gray-200">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Monto Total</span>
                <span class="text-3xl font-black text-emerald-700">
                    ${{ number_format($cobro->cantidad, 2) }}
                </span>
            </div>


            <div class="py-4 space-y-3.5 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Cliente</span>
                    <span class="font-bold text-gray-800">{{ $cobro->cliente->nombre }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Concepto</span>
                    <span class="font-medium text-gray-700">{{ $cobro->concepto }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Fecha de pago</span>
                    <span class="font-mono text-xs text-gray-700">
                        {{ \Carbon\Carbon::parse($cobro->fecha_de_pago)->format('d/m/Y H:i') }} hs
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Cobrado por</span>
                    <span class="font-medium text-gray-700">
                        {{ $cobro->empleado->name ?? $cobro->user->name ?? 'Empleado' }}
                    </span>
                </div>
            </div>

            <div class="mt-4 no-print">
                <button onclick="window.print()" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>

        <div class="bg-gray-50 border-t border-gray-100 py-3 px-4 text-center">
            <p class="text-xs text-gray-400">Gracias por su pago. Vuelva pronto.</p>
        </div>
    </div>

</body>
</html>
