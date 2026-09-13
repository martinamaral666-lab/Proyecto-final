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
            padding: 20px 0;
            position: relative;
        }

        .card-cobro {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,.15);
        }

        .card-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            color: #0f172a;
            font-size: 1.4rem;
            margin-bottom: 4px;
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
            box-shadow: 0 0 0 2px rgba(0, 153, 102, 0.15);
        }

        .tipo-registro {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tipo-opcion {
            flex: 1;
            position: relative;
        }

        .tipo-opcion input {
            display: none;
        }

        .tipo-opcion label {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 70px;
            padding: 10px;
            border: 2px solid #cbd5e0;
            border-radius: 10px;
            background: #f8fafc;
            color: #334155;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tipo-opcion input:checked + label {
            border-color: #009966;
            background: #ecfdf5;
            color: #007a52;
        }

        .seccion-titulo {
            font-size: 13px;
            font-weight: bold;
            color: #009966;
            margin-bottom: 12px;
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
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: #007a52;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        .oculto {
            display: none;
        }
    </style>
</head>

<body>

    <!-- BOTÓN CERRAR SESIÓN -->
    <form
        method="POST"
        action="{{ route('logout') }}"
        style="position: absolute; top: 20px; right: 20px;"
    >
        @csrf

        <button
            type="submit"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all"
        >
            Cerrar Sesión
        </button>
    </form>


    <!-- TARJETA -->
    <div class="card-cobro">

        <div class="card-header">
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
                            🛒 Venta en caja
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
                            🧰 Mano de obra
                        </label>

                    </div>

                </div>

            </div>


            <!-- DATOS DEL CLIENTE -->

            <div class="seccion-titulo">
                Datos del cliente
            </div>


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


            <!-- DATOS DEL COBRO -->

            <div class="seccion-titulo">
                Datos del cobro
            </div>


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


            <!-- SECCIÓN MANO DE OBRA -->

            <div id="seccion-mano-obra" class="oculto">

                <div class="seccion-titulo">
                    Datos de la mano de obra
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


                <div
                    class="form-group oculto"
                    id="grupo-motivo"
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


            <!-- BOTÓN -->

            <button
                type="submit"
                class="btn-submit"
            >
                Guardar y Generar Recibo
            </button>

        </form>

    </div>


    <!-- JAVASCRIPT -->

    <script>

        const ventaCaja = document.getElementById('venta_caja');

        const manoObra = document.getElementById('mano_obra');

        const seccionManoObra =
            document.getElementById('seccion-mano-obra');

        const selectManoObra =
            document.getElementById('mano_de_obra');

        const grupoMotivo =
            document.getElementById('grupo-motivo');

        const motivoNoRealizado =
            document.getElementById('motivo_no_realizado');


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR ENTRE VENTA EN CAJA Y MANO DE OBRA
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR MOTIVO SI NO SE REALIZÓ LA MANO DE OBRA
        |--------------------------------------------------------------------------
        */

        function actualizarMotivo() {

            if (
                manoObra.checked &&
                selectManoObra.value === 'no'
            ) {

                grupoMotivo.classList.remove('oculto');

                motivoNoRealizado.required = true;

            } else {

                grupoMotivo.classList.add('oculto');

                motivoNoRealizado.required = false;

                motivoNoRealizado.value = '';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */

        ventaCaja.addEventListener(
            'change',
            function () {

                actualizarTipoRegistro();

            }
        );


        manoObra.addEventListener(
            'change',
            function () {

                actualizarTipoRegistro();

                actualizarMotivo();

            }
        );


        selectManoObra.addEventListener(
            'change',
            function () {

                actualizarMotivo();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ESTADO INICIAL
        |--------------------------------------------------------------------------
        */

        actualizarTipoRegistro();

        actualizarMotivo();

    </script>

</body>
</html>