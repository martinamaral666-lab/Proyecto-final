<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="{{ asset('css/cobro.css') }}">
</head>
<body>

<div class="card-cobro">
    <div class="card-header">
        <h2>Registrar Nuevo Empleado</h2>
        <small>Crea las credenciales de acceso para el personal</small>
    </div>

    <form action="{{ route('admin.empleados.store') }}" method="POST" class="form-cobro">
        @csrf

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-group">
            <label for="name">Nombre Completo</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn-submit">Crear Empleado</button>
    </form>
</div>

</body>
</html>
