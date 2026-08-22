<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #059669;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 32px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(204, 11, 11, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h1 {
            color: #0f172a;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background-color: #f8fafc;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }


        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #059669;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(8, 9, 9, 0.3);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #7f7e8c;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>

        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tu@correo.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">Ingresar</button>
        </form>
    </div>

</body>
</html>
