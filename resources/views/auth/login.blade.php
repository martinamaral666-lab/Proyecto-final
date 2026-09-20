<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Iniciar sesión - GestiónCash</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #009966;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .login-container {
        width: 100%;
        max-width: 420px;
    }

    .login-card {
        background: white;
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    /* LOGO Y TÍTULO */
    .logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo img {
        display: block;
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin: 0 auto 15px auto;
    }

    .logo h1 {
        color: #044e39;
        font-size: 30px;
        font-weight: bold;
        margin: 0;
    }

    /* CAMPOS */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
    }

    .form-group input {
        width: 100%;
        padding: 13px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: 0.2s;
    }

    .form-group input:focus {
        border-color: #009966;
        box-shadow: 0 0 0 2px rgba(0, 153, 102, 0.15);
    }

    /* CONTRASEÑA */
    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 48px;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: #047857;
    }

    .eye-icon {
        width: 20px;
        height: 20px;
    }

    /* BOTÓN */
    .btn-submit {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        background: #044e39;
        color: white;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        background: #033b2b;
    }

    /* MENSAJES */
    .error-message {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .success-message {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>


</head>

<body>

<div class="login-container">


<div class="login-card">

    <!-- LOGO -->
    <div class="logo">

        <img
            src="{{ asset('imagenes/logo listo 3.png') }}"
            alt="Logo"
        >

        <h1>Iniciar sesión</h1>

    </div>

    <!-- ERROR -->
    @if($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- ÉXITO -->
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO -->
    <form method="POST" action="{{ route('login.post') }}">

        @csrf

        <!-- CORREO -->
        <div class="form-group">

            <label for="email">
                Correo Electrónico
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="tu@correo.com"
            >

        </div>

        <!-- CONTRASEÑA -->
        <div class="form-group">

            <label for="password">
                Contraseña
            </label>

            <div class="password-wrapper">

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                >

                <!-- OJO -->
                <button
                    type="button"
                    onclick="togglePassword('password', this)"
                    class="toggle-password"
                    aria-label="Mostrar contraseña"
                    title="Mostrar contraseña"
                >

                    <svg
                        class="eye-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7z"
                        />

                    </svg>

                </button>

            </div>

        </div>

        <!-- INGRESAR -->
        <button
            type="submit"
            class="btn-submit"
        >
            Ingresar
        </button>

    </form>

</div>


</div>

<script>
function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);
    const icon = button.querySelector('.eye-icon');

    if (input.type === 'password') {

        input.type = 'text';

        button.setAttribute(
            'aria-label',
            'Ocultar contraseña'
        );

        button.setAttribute(
            'title',
            'Ocultar contraseña'
        );

        icon.innerHTML = `
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592"
            />

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6.18 6.18A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.042 5.05"
            />

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 3l18 18"
            />
        `;

    } else {

        input.type = 'password';

        button.setAttribute(
            'aria-label',
            'Mostrar contraseña'
        );

        button.setAttribute(
            'title',
            'Mostrar contraseña'
        );

        icon.innerHTML = `
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7 9.542 7z"
            />
        `;
    }
}
</script>

</body>
</html>
