<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cobro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <!-- HEADER -->
        <div class="bg-emerald-800 text-white p-6 text-center">
            <img src="{{ asset('imagenes/logo listo 3.png') }}" alt="Logo GestiónCash" class="w-16 h-16 object-contain mx-auto mb-3 rounded-xl bg-white/10 p-2">
            <h1 class="text-xl font-bold">Registrar Cobro</h1>
            <p class="text-xs text-emerald-100 mt-1">Ingresa los datos del pago recibido</p>
        </div>

        <!-- FORMULARIO -->
        <form id="receipt-form" action="{{ route('cobros.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- NOMBRE CLIENTE -->
            <div>
                <label for="nombre_cliente" class="block text-xs font-bold uppercase text-gray-600 mb-1">Nombre del Cliente</label>
                <input type="text" name="nombre_cliente" id="nombre_cliente" required placeholder="Ej. Juan Pérez"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <!-- TELÉFONO -->
            <div>
              <label for="telefono" class="block text-xs font-bold uppercase text-gray-600 mb-1">Teléfono (WhatsApp)</label>
              <input type="tel" name="telefono" id="telefono" required placeholder="Ej. 099123456"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
           </div>

            @include('cobros._inventario')

            <!-- CONCEPTO -->
            <div>
                <label for="concepto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Concepto</label>
                <input type="text" name="concepto" id="concepto" required placeholder="Ej. Cuota de servicio"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <!-- ¿SE REALIZÓ LA MANO DE OBRA? -->
            <div>
                <div class="mb-2 border-b-2 border-gray-200 pb-1 text-sm font-bold uppercase tracking-wide text-emerald-700">
                    Datos de la mano de obra
                </div>
                <label for="mano_de_obra" class="block text-xs font-bold uppercase text-gray-600 mb-1">¿Se realizó la mano de obra?</label>
                <select name="mano_de_obra" id="mano_de_obra" onchange="toggleMotivo(this.value)" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600">
                    <option value="si" selected>Sí, realizada</option>
                    <option value="no">No realizada</option>
                </select>
            </div>

            <div id="grupo-monto-manual" class="hidden">
                <label for="monto_base" class="mb-1 block text-xs font-bold uppercase text-gray-600">
                    Monto manual (mano de obra/otros) ($)
                </label>
                <input
                    type="number"
                    step="1"
                    min="0"
                    name="monto_base"
                    id="monto_base"
                    value="{{ old('monto_base', '0.00') }}"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-800 focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                >
            </div>

            <!-- MOTIVO POR EL CUAL NO SE REALIZÓ (OCULTO POR DEFECTO) -->
            <div id="campo_motivo" class="hidden">
                <label for="motivo_no_realizado" class="block text-xs font-bold uppercase text-red-600 mb-1">Motivo por el cual no se realizó</label>
                <textarea name="motivo_no_realizado" id="motivo_no_realizado" rows="2" placeholder="Explica la razón..."
                    class="w-full bg-red-50 border border-red-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-red-500"></textarea>
            </div>

            <!-- BOTONES -->
            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full bg-emerald-800 hover:bg-emerald-900 text-white font-bold py-3 rounded-xl transition shadow-md">
                    Guardar y Generar Recibo
                </button>
                <a href="/admin/menu" class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl text-sm transition">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

    <!-- SCRIPT MOSTRAR/OCULTAR MOTIVO -->
    <script>
        function toggleMotivo(valor) {
            const campoMotivo = document.getElementById('campo_motivo');
            const grupoMontoManual = document.getElementById('grupo-monto-manual');
            const montoManual = document.getElementById('monto_base');
            if (valor === 'no') {
                campoMotivo.classList.remove('hidden');
                grupoMontoManual.classList.add('hidden');
                montoManual.value = '0.00';
            } else {
                campoMotivo.classList.add('hidden');
                grupoMontoManual.classList.remove('hidden');
            }
            montoManual.dispatchEvent(new Event('input', { bubbles: true }));
        }
        toggleMotivo(document.getElementById('mano_de_obra').value);
    </script>

</body>
</html>
