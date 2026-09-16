<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cobro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { background: #009966; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 40px 20px; position: relative; }
        .card-cobro { background: #fff; padding: 30px; border-radius: 16px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,.15); }
        .card-header { text-align: center; margin-bottom: 20px; }
        .card-header img {
            display: block;
            width: 84px;
            height: 84px;
            object-fit: contain;
            margin: 0 auto 12px;
            background: #ecfdf5;
            border-radius: 18px;
            padding: 10px;
        }
        .card-header h2 { color: #0f172a; font-size: 1.4rem; margin-bottom: 4px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #334155; }
        .form-control { width: 100%; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e0; border-radius: 8px; outline: none; font-size: 0.95rem; }
        .btn-submit { width: 100%; padding: 12px; background: #009966; color: #fff; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #058257; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; }
        .seccion-titulo { font-size: 0.9rem; font-weight: bold; color: #009966; margin: 20px 0 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .tipo-registro { display: flex; gap: 15px; margin-top: 5px; }
        .tipo-opcion { display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #cbd5e0; padding: 8px 12px; border-radius: 8px; flex: 1; cursor: pointer; }
        .tipo-opcion label { margin-bottom: 0; cursor: pointer; font-weight: normal; }
        .oculto { display: none !important; }
    </style>
</head>
<body>

    <!-- BOTÓN CERRAR SESIÓN  -->
    <form method="POST" action="{{ route('logout') }}" style="position: absolute; top: 20px; right: 20px;">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all cursor-pointer">
            Cerrar Sesión
        </button>
    </form>

    <!-- TARJETA PRINCIPAL UNICA -->
    <div class="card-cobro">
        <div class="card-header">
            <img src="{{ asset('imagenes/logo listo 3.png') }}" alt="Logo GestiónCash">
            <h2>Registrar Cobro</h2>
        </div>

        <!-- ERRORES -->
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cobros.store') }}" method="POST">
            @csrf

            <!-- TIPO DE REGISTRO -->
            <div class="form-group">
                <label>¿Qué deseas registrar?</label>
                <div class="tipo-registro">
                    <!-- VENTA EN CAJA -->
                    <div class="tipo-opcion">
                        <input type="radio" id="venta_caja" name="tipo_registro" value="venta" checked>
                        <label for="venta_caja"> Venta en caja</label>
                    </div>
                    <!-- MANO DE OBRA -->
                    <div class="tipo-opcion">
                        <input type="radio" id="mano_obra" name="tipo_registro" value="mano_obra">
                        <label for="mano_obra"> Mano de obra</label>
                    </div>
                </div>
            </div>

            <!-- DATOS DEL CLIENTE -->
            <div class="seccion-titulo">Datos del cliente</div>

            <div class="form-group">
                <label for="nombre_cliente">Nombre del Cliente</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono (WhatsApp)</label>
                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="099123456" required>
            </div>

            <!-- DATOS DEL COBRO -->
            <div class="seccion-titulo">Datos del cobro</div>

            <div class="form-group">
                <label for="concepto">Concepto / Descripción</label>
                <input type="text" id="concepto" name="concepto" class="form-control" placeholder="Ej: Materiales eléctricos" required>
            </div>

            <div class="form-group">
                <label for="monto">Monto ($)</label>
                <input type="number" id="monto" name="monto" class="form-control" step="0.01" min="0" placeholder="0.00" required>
            </div>

            <!-- SECCIÓN MANO DE OBRA -->
            <div id="seccion-mano-obra" class="oculto">
                <div class="seccion-titulo">Datos de la mano de obra</div>

                <div class="form-group">
                    <label for="mano_de_obra">¿Se realizó la mano de obra?</label>
                    <select id="mano_de_obra" name="mano_de_obra" class="form-control">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group oculto" id="grupo-motivo">
                    <label for="motivo_no_realizado">Motivo por el que no se realizó</label>
                    <input type="text" id="motivo_no_realizado" name="motivo_no_realizado" class="form-control" placeholder="Ingresá el motivo">
                </div>
            </div>

            <!-- BOTÓN GUARDAR -->
            <button type="submit" class="btn-submit">
                Guardar y Generar Recibo
            </button>
        </form>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        const ventaCaja = document.getElementById('venta_caja');
        const manoObra = document.getElementById('mano_obra');
        const seccionManoObra = document.getElementById('seccion-mano-obra');
        const selectManoObra = document.getElementById('mano_de_obra');
        const grupoMotivo = document.getElementById('grupo-motivo');
        const motivoNoRealizado = document.getElementById('motivo_no_realizado');

        function actualizarTipoRegistro() {
            if (manoObra.checked) {
                seccionManoObra.classList.remove('oculto');
                selectManoObra.required = true;
            } else {
                seccionManoObra.classList.add('oculto');
                selectManoObra.required = false;
                grupoMotivo.classList.add('oculto');
                motivoNoRealizado.required = false;
            }
        }

        function actualizarMotivo() {
            if (manoObra.checked && selectManoObra.value === 'no') {
                grupoMotivo.classList.remove('oculto');
                motivoNoRealizado.required = true;
            } else {
                grupoMotivo.classList.add('oculto');
                motivoNoRealizado.required = false;
                motivoNoRealizado.value = '';
            }
        }

        ventaCaja.addEventListener('change', function () {
            actualizarTipoRegistro();
        });

        manoObra.addEventListener('change', function () {
            actualizarTipoRegistro();
            actualizarMotivo();
        });

        selectManoObra.addEventListener('change', function () {
            actualizarMotivo();
        });

        actualizarTipoRegistro();
        actualizarMotivo();
    </script>

</body>
</html>
