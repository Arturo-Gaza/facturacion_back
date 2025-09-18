<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Email</title>
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
            <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 16px;">Confirma tu correo electrónico</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                ¡Bienvenido/a {{ $datosMail['nombre'] }}!
            </h2>
            
            <p style="color: #6b7280; margin: 0 0 24px 0; font-size: 16px;">
                Ingresa tus credenciales para acceder
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px 0;">
                Gracias por registrarte en Recupera Gastos. Para completar tu registro y activar tu cuenta, necesitamos verificar tu dirección de correo electrónico.
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                Usa el siguiente código de verificación para confirmar tu email:
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
                <h3 style="color: #1e40af; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">📋 Cómo verificar tu cuenta:</h3>
                <ol style="color: #1e40af; font-size: 14px; margin: 0; padding-left: 16px; line-height: 1.6;">
                    <li style="margin-bottom: 4px;">Regresa a la página de verificación</li>
                    <li style="margin-bottom: 4px;">Ingresa el código mostrado arriba</li>
                    <li>¡Listo! Ya podrás gestionar y recuperar tus gastos</li>
                </ol>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si no creaste una cuenta en Recupera Gastos, puedes ignorar este correo de forma segura.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Gestiona y recupera tus gastos de manera inteligente y eficiente
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de Recupera Gastos</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px;">Centro de Ayuda</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px;">Contacto</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px;">Términos</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este correo de verificación fue enviado para confirmar tu registro.<br>
                Por favor, no respondas a este correo electrónico.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>