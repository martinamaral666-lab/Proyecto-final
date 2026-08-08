<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Cobro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

        <div class="bg-emerald-700 text-white p-6 text-center">
            <h1 class="text-xl font-bold">Registrar Cobro</h1>
            <p class="text-xs text-emerald-100 mt-1">Ingresa los datos del pago recibido</p>
        </div>


        <form action="{{ route('cobros.store') }}" method="POST" class="p-6 space-y-4">
            @csrf


            <div>
                <label for="cliente_id" class="block text-xs font-bold uppercase text-gray-600 mb-1">Cliente</label>
                <select name="cliente_id" id="cliente_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    <option value="" disabled selected>Selecciona un cliente</option>
                    @foreach($cliente as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>


            <div>
                <label for="concepto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Concepto</label>
                <input type="text" name="concepto" id="concepto" placeholder="Ej. Cuota de servicio" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>


            <div>
                <label for="cantidad" class="block text-xs font-bold uppercase text-gray-600 mb-1">Monto ($)</label>
                <input type="number" step="0.01" name="cantidad" id="cantidad" placeholder="0.00" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>


            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition active:scale-[0.99]">
                    Guardar y Generar Recibo
                </button>
                <a href="{{ route('cobros.index') }}" class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-4 rounded-xl text-xs transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</body>
</html>
