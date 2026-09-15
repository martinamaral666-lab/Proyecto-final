<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nueva contraseña - GestiónCash</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">


    <div class="w-full max-w-md">


        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8">


            <!-- TITULO -->

            <div class="text-center mb-8">

                <h1 class="text-2xl font-bold text-[#044e39]">
                    GestiónCash
                </h1>

                <h2 class="text-xl font-semibold text-slate-700 mt-4">
                    Crear nueva contraseña
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    Ingresá y confirmá tu nueva contraseña.
                </p>

            </div>



            <!-- ERRORES -->

            @if($errors->any())

                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">

                    <ul class="list-disc list-inside">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif



            <!-- FORMULARIO -->

            <form
                method="POST"
                action="{{ route('password.update') }}"
                class="space-y-5"
            >

                @csrf


                <!-- TOKEN -->

                <input
                    type="hidden"
                    name="token"
                    value="{{ $token }}"
                >



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
                        value="{{ old('email', $email) }}"
                        required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >

                </div>



                <!-- NUEVA CONTRASEÑA -->

                <div>

                    <label
                        for="password"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Nueva contraseña
                    </label>


                    <input
                        type="password"
                        name="password"
                        id="password"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >


                    <p class="text-xs text-slate-400 mt-1">
                        Debe tener al menos 8 caracteres.
                    </p>

                </div>



                <!-- CONFIRMAR -->

                <div>

                    <label
                        for="password_confirmation"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Confirmar nueva contraseña
                    </label>


                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >

                </div>



                <!-- BOTON -->

                <button
                    type="submit"
                    class="w-full bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition"
                >
                    Cambiar contraseña
                </button>


            </form>



            <!-- VOLVER -->

            <div class="text-center mt-6">

                <a
                    href="{{ route('login') }}"
                    class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline"
                >
                    Volver al inicio de sesión
                </a>

            </div>


        </div>

    </div>


</body>

</html>