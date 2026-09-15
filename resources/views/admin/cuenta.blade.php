<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mi Cuenta - GestiónCash</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-slate-100 min-h-screen">


    <!-- HEADER -->

    <header class="bg-[#044e39] text-white px-6 py-4 shadow-lg">

        <div class="max-w-4xl mx-auto flex items-center justify-between">

            <div>

                <h1 class="text-xl font-bold">
                    GestiónCash
                </h1>

                <p class="text-emerald-200 text-sm">
                    Mi cuenta
                </p>

            </div>


            <a
                href="{{ route('admin.menu') }}"
                class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-sm transition"
            >
                Volver
            </a>

        </div>

    </header>



    <!-- CONTENIDO -->

    <main class="max-w-4xl mx-auto p-6">


        <!-- MENSAJE DE ÉXITO -->

        @if(session('success'))

            <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">

                {{ session('success') }}

            </div>

        @endif



        <!-- ERRORES -->

        @if($errors->any())

            <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">

                <ul class="list-disc list-inside">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif



        <!-- TARJETA -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">


            <!-- TITULO -->

            <div class="mb-8">

                <h2 class="text-2xl font-bold text-slate-800">
                    Configuración de cuenta
                </h2>

                <p class="text-slate-500 mt-1">
                    Actualizá tus datos personales y contraseña.
                </p>

            </div>



            <!-- FORMULARIO -->

            <form
                method="POST"
                action="{{ route('admin.cuenta.update') }}"
                class="space-y-6"
            >

                @csrf

                @method('PUT')



                <!-- NOMBRE -->

                <div>

                    <label
                        for="name"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Nombre
                    </label>


                    <input
                        type="text"
                        id="name"
                        value="{{ $usuario->name }}"
                        disabled
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed"
                    >


                    <p class="text-xs text-slate-400 mt-1">
                        El nombre no puede modificarse desde esta sección.
                    </p>

                </div>



                <!-- EMAIL -->

                <div>

                    <label
                        for="email"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Correo electrónico
                    </label>


                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $usuario->email) }}"
                        required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >

                </div>



                <!-- TELEFONO -->

                <div>

                    <label
                        for="telefono"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Teléfono
                    </label>


                    <input
                        type="text"
                        name="telefono"
                        id="telefono"
                        value="{{ old('telefono', $usuario->telefono) }}"
                        maxlength="20"
                        placeholder="Ej: 099 123 456"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >

                </div>



                <!-- SECCION CONTRASEÑA -->

                <div class="border-t border-slate-200 pt-6">


                    <h3 class="text-lg font-bold text-slate-800 mb-2">
                        Cambiar contraseña
                    </h3>


                    <p class="text-sm text-slate-500 mb-4">
                        Para cambiar tu contraseña, ingresá primero la contraseña actual.
                    </p>



                    <div class="space-y-4">


                        <!-- CONTRASEÑA ACTUAL -->

                        <div>

                            <label
                                for="password_actual"
                                class="block text-sm font-semibold text-slate-700 mb-2"
                            >
                                Contraseña actual
                            </label>


                            <div class="relative">

                                <input
                                    type="password"
                                    name="password_actual"
                                    id="password_actual"
                                    autocomplete="current-password"
                                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                                >


                                <button
                                    type="button"
                                    onclick="togglePassword('password_actual', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                                    aria-label="Mostrar contraseña"
                                >

                                    <svg
                                        class="eye-icon w-5 h-5"
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
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                    </svg>

                                </button>

                            </div>


                            <p class="text-xs text-slate-400 mt-1">
                                Es necesario ingresar la contraseña actual para establecer una nueva.
                            </p>

                        </div>



                        <!-- NUEVA CONTRASEÑA -->

                        <div>

                            <label
                                for="password"
                                class="block text-sm font-semibold text-slate-700 mb-2"
                            >
                                Nueva contraseña
                            </label>


                            <div class="relative">

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    minlength="8"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                                >


                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                                    aria-label="Mostrar contraseña"
                                >

                                    <svg
                                        class="eye-icon w-5 h-5"
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
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                    </svg>

                                </button>

                            </div>


                            <p class="text-xs text-slate-400 mt-1">
                                Debe tener al menos 8 caracteres y ser diferente de la contraseña actual.
                            </p>

                        </div>



                        <!-- CONFIRMAR CONTRASEÑA -->

                        <div>

                            <label
                                for="password_confirmation"
                                class="block text-sm font-semibold text-slate-700 mb-2"
                            >
                                Confirmar nueva contraseña
                            </label>


                            <div class="relative">

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    minlength="8"
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                                >


                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                                    aria-label="Mostrar contraseña"
                                >

                                    <svg
                                        class="eye-icon w-5 h-5"
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
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>



                        <!-- OLVIDE MI CONTRASEÑA -->

                        <div class="pt-2">

                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline"
                            >
                                ¿Olvidaste tu contraseña?
                            </a>

                        </div>


                    </div>

                </div>



                <!-- BOTON -->

                <div class="flex justify-end pt-4">

                    <button
                        type="submit"
                        class="bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition"
                    >
                        Guardar cambios
                    </button>

                </div>


            </form>

        </div>

    </main>



    <!-- JAVASCRIPT PARA MOSTRAR / OCULTAR CONTRASEÑA -->

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
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                    />
                `;

            }

        }

    </script>


</body>

</html>