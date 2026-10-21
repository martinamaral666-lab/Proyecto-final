<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recuperar contraseña - GestiónCash</title>

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
                    Recuperar contraseña
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    Ingresá tu correo electrónico y te enviaremos un enlace para crear una nueva contraseña.
                </p>

            </div>



            <!-- MENSAJE -->

            @if(session('status'))

                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">

                    {{ session('status') }}

                </div>

            @endif



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
                id="reset-link-form"
                method="POST"
                action="{{ route('password.email') }}"
                class="space-y-5"
            >

                @csrf


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
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="ejemplo@gmail.com"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                    >

                </div>



                <button
                    id="reset-link-button"
                    type="submit"
                    @disabled($resetWaitSeconds > 0)
                    class="w-full bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition disabled:cursor-not-allowed disabled:opacity-60"
                >
                    @if ($resetWaitSeconds > 0)
                        Espera {{ $resetWaitSeconds }}s
                    @else
                        Enviar enlace
                    @endif
                </button>


            </form>



            <!-- VOLVER -->

            <div class="text-center mt-6">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline bg-transparent border-0 cursor-pointer"
                    >
                        Volver al inicio de sesión
                    </button>
                </form>

            </div>


        </div>

    </div>

    <script>
        const resetLinkForm = document.getElementById('reset-link-form');
        const resetLinkButton = document.getElementById('reset-link-button');
        let resetWaitSeconds = Number(@json($resetWaitSeconds));

        if (resetWaitSeconds > 0) {
            resetLinkButton.disabled = true;

            const updateResetCountdown = () => {
                resetWaitSeconds -= 1;

                if (resetWaitSeconds <= 0) {
                    resetLinkButton.disabled = false;
                    resetLinkButton.textContent = 'Enviar enlace';
                    window.clearInterval(resetCountdown);

                    return;
                }

                resetLinkButton.textContent = `Espera ${resetWaitSeconds}s`;
            };

            resetLinkButton.textContent = `Espera ${resetWaitSeconds}s`;
            const resetCountdown = window.setInterval(updateResetCountdown, 1000);
        }

        resetLinkForm.addEventListener('submit', () => {
            resetLinkButton.disabled = true;
            resetLinkButton.textContent = 'Enviando...';
        });
    </script>


</body>

</html>
