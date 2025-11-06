<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Correo Electrónico</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #1e3a8a; padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="white"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Recupera Gastos</h1>
            </div>
            <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 16px;">Confirmación de correo electrónico</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                ¡Hola {{ $datosMail['nombre'] }}!
            </h2>
            
            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 16px 0 24px 0;">
                Para continuar, necesitamos confirmar tu dirección de correo electrónico. Por favor, ingresa el siguiente código donde se te solicite.
            </p>

            <!-- Verification Code Box -->
            <div style="background-color: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 32px; text-align: center; margin: 32px 0;">
                <p style="color: #6b7280; margin: 0 0 8px 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Código de Verificación
                </p>
                <div style="background-color: #1e3a8a; border-radius: 8px; padding: 20px; margin: 16px 0;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 36px; font-weight: 700; letter-spacing: 4px; font-family: 'Courier New', monospace;">
                        {{ $datosMail['codigo'] }}
                    </h1>
                </div>
                <p style="color: #6b7280; margin: 8px 0 0 0; font-size: 14px;">
                    Este código es válido por <strong style="color: #374151;">10 minutos</strong>
                </p>
            </div>

            <!-- Instructions -->
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 32px 0; border-radius: 0 8px 8px 0;">
                <h3 style="color: #1e40af; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">📧 Instrucciones:</h3>
                <p style="color: #1e40af; font-size: 14px; margin: 0; line-height: 1.6;">
                    Ingresa el código mostrado arriba en el formulario donde se solicita la verificación de tu correo.
                </p>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si no solicitaste esta verificación, puedes ignorar este correo de forma segura.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Verifica tu correo para continuar con tus gestiones en Recupera Gastos
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de Recupera Gastos</strong>
            </p>

            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este correo fue enviado para confirmar tu dirección de correo electrónico.<br>
                Por favor, no respondas a este mensaje.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>
