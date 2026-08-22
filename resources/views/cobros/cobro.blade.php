<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cobro</title>

    <link rel="stylesheet" href="{{ asset('css/cobro.css') }}">
</head>
<body>

<div class="card-cobro">
    <div class="card-header">
        <h2>Registrar Cobro</h2>
        <small>Ingresa los datos del pago recibido</small>
    </div>

    <form action="{{ route('cobro.store') }}" method="POST" class="form-cobro">
        @csrf

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-group">
            <label for="nombre_cliente">Nombre del Cliente</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="numero_cuenta">Número de cuenta o contrato</label>
            <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción del trabajo</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" id="se_realizo" name="se_realizo" value="1">
            <label for="se_realizo">¿Se realizó el trabajo?</label>
        </div>

        <button type="submit" class="btn-submit">Guardar y Generar Recibo</button>
    </form>
</div>

</body>
</html>
