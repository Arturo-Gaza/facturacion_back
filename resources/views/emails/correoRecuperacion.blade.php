<div style="font-family: Arial, sans-serif; line-height: 1.5;">
    <h2 style="color: #2c3e50;">Saludos {{ $datosMail['nombre'] }}</h2>


    <p>Has solicitado recuperar tu contraseña. Usa el siguiente código para continuar:</p>
    <h1 style="color: #2F855A;">{{ $datosMail['codigo'] }}</h1>
    <p>Este código es válido por 10 minutos.</p>
    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
    <br>
    <p>Saludos,<br>El equipo de soporte</p>

    <hr style="margin-top: 30px;">

    <p style="font-size: 12px; color: #888;">
        Este mensaje fue generado automáticamente. No respondas a este correo.
    </p>
</div>
