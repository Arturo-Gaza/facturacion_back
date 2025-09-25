<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Inhabilitación de Cuenta</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #f59e0b; padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" fill="white"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Recupera Gastos</h1>
            </div>
            <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 16px;">Confirmación de inhabilitación</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                Estimado/a {{ $datosMail['nombre'] }}
            </h2>
            
            <p style="color: #6b7280; margin: 0 0 24px 0; font-size: 16px;">
                Confirmación de solicitud de inhabilitación
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                Has solicitado inhabilitar temporalmente tu cuenta. Para confirmar esta acción por seguridad, utiliza el siguiente código:
            </p>

            <!-- Verification Code Box -->
            <div style="background-color: #fef9c3; border: 2px solid #fbbf24; border-radius: 12px; padding: 32px; text-align: center; margin: 32px 0;">
                <p style="color: #92400e; margin: 0 0 8px 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Código de Confirmación
                </p>
                <div style="background-color: #f59e0b; border-radius: 8px; padding: 20px; margin: 16px 0;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 36px; font-weight: 700; letter-spacing: 4px; font-family: 'Courier New', monospace;">
                        {{ $datosMail['codigo'] }}
                    </h1>
                </div>
                <p style="color: #92400e; margin: 8px 0 0 0; font-size: 14px;">
                    Este código es válido por <strong style="color: #7c2d12;">10 minutos</strong>
                </p>
            </div>

            <!-- Security Notice -->
            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; margin: 32px 0; border-radius: 0 8px 8px 0;">
                <h3 style="color: #92400e; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">🔒 Medida de Seguridad:</h3>
                <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.6;">
                    Solicitamos este código para confirmar que realmente deseas inhabilitar tu cuenta. Si no hiciste esta solicitud, puedes ignorar este correo.
                </p>
            </div>

            <!-- What happens next -->
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 32px 0; border-radius: 0 8px 8px 0;">
                <h3 style="color: #1e40af; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">📋 ¿Qué sucede después?</h3>
                <ol style="color: #1e40af; font-size: 14px; margin: 0; padding-left: 16px; line-height: 1.6;">
                    <li style="margin-bottom: 4px;">Ingresa el código en la página de confirmación</li>
                    <li style="margin-bottom: 4px;">Tu cuenta será inhabilitada temporalmente</li>
                    <li style="margin-bottom: 4px;">No podrás acceder al sistema</li>
                    <li>Podrás reactivarla contactando a soporte cuando desees</li>
                </ol>
            </div>

            <!-- Reactivation Info -->
            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 20px; margin: 32px 0; border-radius: 0 8px 8px 0;">
                <h3 style="color: #166534; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">🔄 ¿Cambias de opinión?</h3>
                <p style="color: #166534; font-size: 14px; margin: 0; line-height: 1.6;">
                    Si después deseas reactivar tu cuenta, simplemente contacta a nuestro equipo de soporte. Podremos ayudarte a restaurar el acceso cuando lo necesites.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Gestiona y recupera tus gastos de manera inteligente y eficiente
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de soporte</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #f59e0b; text-decoration: none; font-size: 14px;">Centro de Ayuda</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #f59e0b; text-decoration: none; font-size: 14px;">Contacto</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #f59e0b; text-decoration: none; font-size: 14px;">Seguridad</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este mensaje fue generado automáticamente. No respondas a este correo.<br>
                Para consultas relacionadas con tu cuenta, utiliza nuestros canales oficiales de soporte.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>