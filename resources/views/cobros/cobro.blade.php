<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Cobro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            background: #009966;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
        }

        .card-cobro {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0,0,0,.15);
        }

        .card-header {
            text-align: center;
            margin-bottom: 20px;
        }

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
<<<<<<< HEAD
        .card-header h2 { color: #0f172a; font-size: 1.4rem; margin-bottom: 4px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #334155; }
        .form-control { width: 100%; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e0; border-radius: 8px; outline: none; font-size: 0.95rem; }
        .btn-submit { width: 100%; padding: 12px; background: #009966; color: #fff; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #058257; }
        .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; }
        .seccion-titulo { font-size: 0.9rem; font-weight: bold; color: #009966; margin: 20px 0 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .tipo-registro { display: flex; gap: 15px; margin-top: 5px; }
        .tipo-opcion { display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #cbd5e0; padding: 8px 12px; border-radius: 8px; flex: 1; cursor: pointer; }
        .tipo-opcion label { margin-bottom: 0; cursor: pointer; font-weight: normal; }
        .oculto { display: none !important; }
=======

        .card-header h2 {
            color: #0f172a;
            font-size: 1.4rem;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            outline: none;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #009966;
        }

        .seccion-titulo {
            font-size: 0.9rem;
            font-weight: bold;
            color: #009966;
            margin: 20px 0 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tipo-registro {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .tipo-opcion {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            padding: 8px 12px;
            border-radius: 8px;
            flex: 1;
            cursor: pointer;
        }

        .tipo-opcion label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: normal;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #009966;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #058257;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        /* SECCIÓN MANO DE OBRA */
        #seccion-mano-obra {
            display: none;
        }

        /* MOTIVO */
        #grupo-motivo {
            display: none;
        }
>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
    </style>
</head>

<body>

    <!-- ================================ -->
    <!-- CERRAR SESIÓN -->
    <!-- ================================ -->

    <form
        method="POST"
        action="{{ route('logout') }}"
        style="position: absolute; top: 20px; right: 20px;"
    >
        @csrf

        <button
            type="submit"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all cursor-pointer"
        >
            Cerrar Sesión
        </button>
    </form>


    <!-- ================================ -->
    <!-- TARJETA PRINCIPAL -->
    <!-- ================================ -->

    <div class="card-cobro">

        <!-- ENCABEZADO -->

        <div class="card-header">

            <img
                src="{{ asset('imagenes/logo.png') }}"
                alt="Logo GestiónCash"
            >

            <h2>Registrar Cobro</h2>

        </div>


        <!-- ================================ -->
        <!-- ERRORES -->
        <!-- ================================ -->

        @if ($errors->any())

            <div class="alert-error">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

<<<<<<< HEAD
        <form id="receipt-form" action="{{ route('cobros.store') }}" method="POST">
=======

        <!-- ================================ -->
        <!-- FORMULARIO -->
        <!-- ================================ -->

        <form
            action="{{ route('cobros.store') }}"
            method="POST"
        >

>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
            @csrf


            <!-- ================================ -->
            <!-- TIPO DE REGISTRO -->
            <!-- ================================ -->

            <div class="form-group">

                <label>
                    ¿Qué deseas registrar?
                </label>

                <div class="tipo-registro">

                    <!-- VENTA EN CAJA -->

                    <div class="tipo-opcion">

                        <input
                            type="radio"
                            id="venta_caja"
                            name="tipo_registro"
                            value="venta"
                            checked
                        >

                        <label for="venta_caja">
                            Venta en caja
                        </label>

                    </div>


                    <!-- MANO DE OBRA -->

                    <div class="tipo-opcion">

                        <input
                            type="radio"
                            id="mano_obra"
                            name="tipo_registro"
                            value="mano_obra"
                        >

                        <label for="mano_obra">
                            Mano de obra
                        </label>

                    </div>

                </div>

            </div>


            <!-- ================================ -->
            <!-- DATOS DEL CLIENTE -->
            <!-- ================================ -->

            <div class="seccion-titulo">
                Datos del cliente
            </div>


            <!-- NOMBRE -->

            <div class="form-group">

                <label for="nombre_cliente">
                    Nombre del Cliente
                </label>

                <input
                    type="text"
                    id="nombre_cliente"
                    name="nombre_cliente"
                    class="form-control"
                    required
                >

            </div>


            <!-- TELÉFONO -->

            <div class="form-group">

                <label for="telefono">
                    Teléfono (WhatsApp)
                </label>

                <input
                    type="text"
                    id="telefono"
                    name="telefono"
                    class="form-control"
                    placeholder="099123456"
                    required
                >

            </div>


            <!-- ================================ -->
            <!-- DATOS DEL COBRO -->
            <!-- ================================ -->

<<<<<<< HEAD
            @include('cobros._inventario')

            <div class="form-group">
                <label for="concepto">Concepto / Descripción</label>
                <input type="text" id="concepto" name="concepto" class="form-control" placeholder="Ej: Materiales eléctricos" required>
=======
            <div class="seccion-titulo">
                Datos del cobro
>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
            </div>


            <!-- CONCEPTO -->

            <div class="form-group">

                <label for="concepto">
                    Concepto / Descripción
                </label>

                <input
                    type="text"
                    id="concepto"
                    name="concepto"
                    class="form-control"
                    placeholder="Ej: Materiales eléctricos"
                    required
                >

            </div>


            <!-- MONTO -->

            <div class="form-group">

                <label for="monto">
                    Monto ($)
                </label>

                <input
                    type="number"
                    id="monto"
                    name="monto"
                    class="form-control"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    required
                >

            </div>


            <!-- ================================ -->
            <!-- MEDIO DE PAGO -->
            <!-- ================================ -->

            <div class="form-group">

                <label for="metodo_pago">
                    Medio de pago
                </label>

                <select
                    id="metodo_pago"
                    name="metodo_pago"
                    class="form-control"
                    required
                >

                    <option
                        value=""
                        disabled
                        selected
                    >
                        Seleccioná un medio de pago
                    </option>

                    <option value="efectivo">
                        💵 Efectivo
                    </option>

                    <option value="tarjeta">
                        💳 Tarjeta
                    </option>

                </select>

            </div>


            <!-- ================================ -->
            <!-- SECCIÓN MANO DE OBRA -->
            <!-- ================================ -->

            <div id="seccion-mano-obra">

                <div class="seccion-titulo">
                    Datos de la mano de obra
                </div>


                <!-- ¿SE REALIZÓ? -->

                <div id="grupo-monto-manual" class="form-group">
                    <label for="monto_base">Monto manual (mano de obra/otros) ($)</label>
                    <input
                        type="number"
                        id="monto_base"
                        name="monto_base"
                        class="form-control"
                        step="0.01"
                        min="0"
                        value="{{ old('monto_base', '0.00') }}"
                    >
                </div>

                <div class="form-group">

                    <label for="mano_de_obra">
                        ¿Se realizó la mano de obra?
                    </label>

                    <select
                        id="mano_de_obra"
                        name="mano_de_obra"
                        class="form-control"
                    >

                        <option value="si">
                            Sí
                        </option>

                        <option value="no">
                            No
                        </option>

                    </select>

                </div>


                <!-- MOTIVO -->

                <div
                    id="grupo-motivo"
                    class="form-group"
                >

                    <label for="motivo_no_realizado">
                        Motivo por el que no se realizó
                    </label>

                    <input
                        type="text"
                        id="motivo_no_realizado"
                        name="motivo_no_realizado"
                        class="form-control"
                        placeholder="Ingresá el motivo"
                    >

                </div>

            </div>


            <!-- ================================ -->
            <!-- BOTÓN GUARDAR -->
<<<<<<< HEAD
            <button id="receipt-submit-button" type="submit" class="btn-submit" @disabled($receiptWaitSeconds > 0)>
                @if ($receiptWaitSeconds > 0)
                    Espera {{ $receiptWaitSeconds }}s
                @else
                    Guardar y Generar Recibo
                @endif
=======
            <!-- ================================ -->

            <button
                type="submit"
                class="btn-submit"
            >
                Guardar y Generar Recibo
>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
            </button>

        </form>

    </div>


    <!-- ================================ -->
    <!-- JAVASCRIPT -->
    <!-- ================================ -->

    <script>
<<<<<<< HEAD
        const ventaCaja = document.getElementById('venta_caja');
        const manoObra = document.getElementById('mano_obra');
        const seccionManoObra = document.getElementById('seccion-mano-obra');
        const selectManoObra = document.getElementById('mano_de_obra');
        const grupoMotivo = document.getElementById('grupo-motivo');
        const motivoNoRealizado = document.getElementById('motivo_no_realizado');
        const montoManual = document.getElementById('monto_base');
        const receiptForm = document.getElementById('receipt-form');
        const receiptSubmitButton = document.getElementById('receipt-submit-button');

        function actualizarTipoRegistro() {
            if (manoObra.checked) {
                seccionManoObra.classList.remove('oculto');
                selectManoObra.required = true;
                montoManual.required = true;
            } else {
                seccionManoObra.classList.add('oculto');
                selectManoObra.required = false;
                montoManual.required = false;
                montoManual.value = '0.00';
                montoManual.dispatchEvent(new Event('input', { bubbles: true }));
                grupoMotivo.classList.add('oculto');
                motivoNoRealizado.required = false;
=======

        document.addEventListener('DOMContentLoaded', function () {

            const ventaCaja =
                document.getElementById('venta_caja');

            const manoObra =
                document.getElementById('mano_obra');

            const seccionManoObra =
                document.getElementById('seccion-mano-obra');

            const selectManoObra =
                document.getElementById('mano_de_obra');

            const grupoMotivo =
                document.getElementById('grupo-motivo');

            const motivoNoRealizado =
                document.getElementById('motivo_no_realizado');


            /* =====================================
               MOSTRAR / OCULTAR MANO DE OBRA
            ===================================== */

            function actualizarTipoRegistro() {

                if (manoObra.checked) {

                    seccionManoObra.style.display = 'block';

                    selectManoObra.required = true;

                    actualizarMotivo();

                } else {

                    seccionManoObra.style.display = 'none';

                    selectManoObra.required = false;

                    grupoMotivo.style.display = 'none';

                    motivoNoRealizado.required = false;

                    motivoNoRealizado.value = '';

                }

>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
            }


            /* =====================================
               MOSTRAR / OCULTAR MOTIVO
            ===================================== */

            function actualizarMotivo() {

                if (
                    manoObra.checked &&
                    selectManoObra.value === 'no'
                ) {

                    grupoMotivo.style.display = 'flex';

                    motivoNoRealizado.required = true;

                } else {

                    grupoMotivo.style.display = 'none';

                    motivoNoRealizado.required = false;

                    motivoNoRealizado.value = '';

                }

            }


            /* =====================================
               EVENTOS
            ===================================== */

            ventaCaja.addEventListener(
                'change',
                actualizarTipoRegistro
            );

            manoObra.addEventListener(
                'change',
                actualizarTipoRegistro
            );

            selectManoObra.addEventListener(
                'change',
                actualizarMotivo
            );


            /* =====================================
               ESTADO INICIAL
            ===================================== */

            actualizarTipoRegistro();

        });

<<<<<<< HEAD
        manoObra.addEventListener('change', function () {
            actualizarTipoRegistro();
            actualizarMotivo();
        });

        selectManoObra.addEventListener('change', function () {
            actualizarMotivo();
        });

        actualizarTipoRegistro();
        actualizarMotivo();

        let receiptWaitSeconds = Number(@json($receiptWaitSeconds));

        if (receiptWaitSeconds > 0) {
            receiptSubmitButton.disabled = true;

            const updateReceiptCountdown = () => {
                if (receiptWaitSeconds <= 0) {
                    receiptSubmitButton.disabled = false;
                    receiptSubmitButton.textContent = 'Guardar y Generar Recibo';
                    window.clearInterval(receiptCountdown);

                    return;
                }

                receiptSubmitButton.textContent = `Espera ${receiptWaitSeconds}s`;
                receiptWaitSeconds -= 1;
            };

            updateReceiptCountdown();
            const receiptCountdown = window.setInterval(updateReceiptCountdown, 1000);
        }

        receiptForm.addEventListener('submit', () => {
            receiptSubmitButton.disabled = true;
            receiptSubmitButton.textContent = 'Generando recibo...';
        });
=======
>>>>>>> remotes/origin/administrador-de-cuenta-de-admin
    </script>

</body>
</html>
