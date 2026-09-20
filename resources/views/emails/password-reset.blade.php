<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecé tu contraseña — GestiónCash</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f7f5;
    font-family: Arial, Helvetica, sans-serif;
    color: #333333;
">

    <div style="
        width: 100%;
        padding: 40px 0;
        background-color: #f4f7f5;
    ">

        <div style="
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        ">

            <!-- ENCABEZADO -->
            <div style="
                background-color: #000000;
                padding: 25px 30px;
                text-align: center;
            ">

                <img
                    src="{{ $message->embed(public_path('imagenes/logo listo 3.png')) }}"
                    alt="GestiónCash"
                    style="
                        max-width: 180px;
                        height: auto;
                        display: block;
                        margin: 0 auto 15px auto;
                    "
                >

                <div style="
                    color: #ffffff;
                    font-size: 24px;
                    font-weight: bold;
                ">
                    GestiónCash
                </div>

            </div>


            <!-- CONTENIDO -->
            <div style="
                padding: 35px 40px;
            ">

                <p style="
                    font-size: 16px;
                    line-height: 1.6;
                    margin: 0 0 20px 0;
                ">
                    Hola, {{ $user->name }} 👋
                </p>

                <p style="
                    font-size: 16px;
                    line-height: 1.6;
                    margin: 0 0 20px 0;
                ">
                    Recibimos una solicitud para restablecer la contraseña
                    de tu cuenta de <strong>GestiónCash</strong>.
                </p>

                <p style="
                    font-size: 16px;
                    line-height: 1.6;
                    margin: 0 0 25px 0;
                ">
                    Si realizaste esta solicitud, hacé clic en el siguiente
                    botón para crear una nueva contraseña:
                </p>


                <!-- BOTÓN -->
                <div style="
                    text-align: center;
                    margin: 30px 0;
                ">

                    <a
                        href="{{ $url }}"
                        style="
                            display: inline-block;
                            background-color: #198754;
                            color: #ffffff;
                            text-decoration: none;
                            padding: 14px 28px;
                            border-radius: 6px;
                            font-size: 16px;
                            font-weight: bold;
                        "
                    >
                        Restablecer mi contraseña
                    </a>

                </div>


                <p style="
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 0 0 20px 0;
                ">
                    Este enlace será válido durante
                    <strong>{{ $expire }} minutos</strong>.
                </p>

                <p style="
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 0;
                ">
                    Si no solicitaste este cambio de contraseña,
                    podés ignorar este correo. Tu contraseña actual
                    seguirá siendo la misma.
                </p>


                <!-- SEPARADOR -->
                <hr style="
                    border: 0;
                    border-top: 1px solid #dddddd;
                    margin: 30px 0;
                ">


                <!-- FIRMA -->
                <p style="
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 0;
                ">
                    Saludos,
                </p>

                <p style="
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 10px 0 0 0;
                ">
                    <strong>Equipo Control Z Squad</strong><br>
                    Soporte técnico de GestiónCash
                </p>

            </div>


            <!-- PIE -->
            <div style="
                background-color: #f1f3f2;
                padding: 20px 30px;
                text-align: center;
            ">

                <p style="
                    font-size: 12px;
                    color: #777777;
                    margin: 0 0 8px 0;
                ">
                    Este es un mensaje automático.
                    Por favor, no respondas a este correo.
                </p>

                <p style="
                    font-size: 12px;
                    color: #999999;
                    margin: 0;
                ">
                    © {{ date('Y') }} GestiónCash
                </p>

            </div>

        </div>

    </div>

</body>
</html>