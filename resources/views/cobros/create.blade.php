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
        <form action="{{ route('cobro.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

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
            <!-- CONCEPTO -->
            <div>
                <label for="concepto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Concepto</label>
                <input type="text" name="concepto" id="concepto" required placeholder="Ej. Cuota de servicio"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <!-- MONTO -->
            <div>
                <label for="monto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Monto ($)</label>
                <input type="number" step="0.01" name="monto" id="monto" required placeholder="0.00"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <!-- ¿SE REALIZÓ LA MANO DE OBRA? -->
            <div>
                <label for="mano_de_obra" class="block text-xs font-bold uppercase text-gray-600 mb-1">¿Se realizó la mano de obra?</label>
                <select name="mano_de_obra" id="mano_de_obra" onchange="toggleMotivo(this.value)" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600">
                    <option value="si" selected>Sí, realizada</option>
                    <option value="no">No realizada</option>
                </select>
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
            if (valor === 'no') {
                campoMotivo.classList.remove('hidden');
            } else {
                campoMotivo.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
