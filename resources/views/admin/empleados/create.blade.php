<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="{{ asset('css/cobro.css') }}">
    <style>

        body { background: #009966; font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card-cobro { background: #fff; width: 100%; max-width: 400px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.15); }
        .card-header h2 { margin: 0 0 20px; color: #0f172a; font-size: 1.4rem; text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; color: #334155; font-size: .88rem; font-weight: 600; }
        .form-control { width: 100%; padding: 10px 12px; background: #f8fafc; border: 1px solid #cbd5e0; border-radius: 8px; font-size: .95rem; box-sizing: border-box; outline: none; }
        .btn-submit { width: 100%; padding: 12px; background: #009966; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: #007a52; }
        .alert-error { color: #991b1b; background: #fee2e2; border: 1px solid #fca5a5; padding: 10px; margin-bottom: 15px; border-radius: 8px; font-size: .85rem; }
        .alert-error ul { margin: 0; padding-left: 18px; }
        .alert-success { color: #065f46; background: #d1fae5; border: 1px solid #6ee7b7; padding: 10px; margin-bottom: 15px; border-radius: 8px; font-size: .85rem; text-align: center; }

        .navbar {
            position:absolute;
            top: 0;
            width: 100%;
            height: 40px;
            color: #fff;
            padding: 15px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .btn-back { background: rgba(255, 255, 255, 0.15); color: #fff; text-decoration: none; padding: 8px 12px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; }
        .btn-back:hover { background: rgba(255, 255, 255, 0.25); }
        .brand-title { font-size: 1.25rem; font-weight: 700; }

    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <a href="{{ route('admin.menu') }}" class="btn-back">← Volver</a>
            <img src="{{ asset('imagenes/logo listo 3.png') }}" alt="Logo GestiónCash" style="width: 38px; height: 38px; object-fit: contain; border-radius: 10px; background: rgba(255,255,255,0.08); padding: 4px;">
            <span class="brand-title">GestiónCash</span>
        </div>
    </nav>

<div class="card-cobro">
    <div class="card-header">
        <img src="{{ asset('imagenes/logo listo 3.png') }}" alt="Logo GestiónCash" style="display: block; width: 84px; height: 84px; object-fit: contain; margin: 0 auto 12px; background: #ecfdf5; border-radius: 18px; padding: 10px;">
        <h2>Registrar Nuevo Empleado</h2>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.empleados.store') }}" method="POST" class="form-cobro">
        @csrf
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
