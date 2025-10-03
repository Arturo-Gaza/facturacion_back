<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida y Credenciales Temporales</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #1e3a8a, #3730a3); padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="white"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Recupera Gastos</h1>
            </div>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px; font-weight: 500;">¡Bienvenido al sistema!</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                ¡Hola {{ $datosMail['email'] }}!
            </h2>
            
            <p style="color: #6b7280; margin: 0 0 24px 0; font-size: 16px;">
                Tu cuenta ha sido creada exitosamente
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px 0;">
                Te damos la bienvenida a <strong>Recupera Gastos</strong>. Para completar tu registro y acceder al sistema, necesitas seguir unos simples pasos de configuración.
            </p>

            <!-- Password Temporal Box -->
            <div style="background-color: #f0f9ff; border: 2px solid #0ea5e9; border-radius: 12px; padding: 32px; text-align: center; margin: 32px 0;">
                <p style="color: #0369a1; margin: 0 0 8px 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Credenciales de Acceso Temporal
                </p>
                <div style="background: linear-gradient(135deg, #0ea5e9, #0369a1); border-radius: 8px; padding: 20px; margin: 16px 0;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; font-family: 'Courier New', monospace;">
                        {{ $datosMail['password_temporal'] }}
                    </h1>
                </div>
                <p style="color: #0369a1; margin: 8px 0 0 0; font-size: 14px;">
                    <strong>⚠️ Este password es temporal y debe ser cambiado</strong>
                </p>
            </div>

            <!-- Proceso de Registro Completo -->
            <div style="background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 32px 0;">
                <h3 style="color: #1e293b; margin: 0 0 20px 0; font-size: 18px; font-weight: 600; text-align: center;">
                    📋 Proceso de Configuración de Cuenta
                </h3>
                
                <!-- Paso 1 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                    <div style="background-color: #0ea5e9; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        1
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Primer Ingreso</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Ingresa al sistema con tu email y el password temporal proporcionado.
                        </p>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                    <div style="background-color: #8b5cf6; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        2
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Verificación de Teléfono</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Ingresa tu número de teléfono. Te enviaremos un código de verificación por SMS.
                        </p>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #10b981;">
                    <div style="background-color: #10b981; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        3
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Nuevo Password</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Crea un nuevo password seguro y personalizado para tu cuenta.
                        </p>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div style="display: flex; align-items: flex-start; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="background-color: #f59e0b; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        4
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Datos Personales</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Completa tu perfil con tus datos personales y de contacto.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div style="background-color: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 24px 0;">
                <div style="display: flex; align-items: flex-start;">
                    <div style="background-color: #f59e0b; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="color: #92400e; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Importante de Seguridad</h4>
                        <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.5;">
                            • El password temporal es de un solo uso<br>
                            • Debes cambiarlo inmediatamente después del primer ingreso<br>
                            • No compartas tus credenciales con nadie<br>
                            • Completa todos los pasos para activar tu cuenta completamente
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ $datosMail['url_login'] ?? '#' }}" style="display: inline-block; background: linear-gradient(135deg, #1e3a8a, #3730a3); color: white; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(30, 58, 138, 0.2);">
                    🚀 Comenzar Configuración
                </a>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si tienes problemas con el acceso o necesitas ayuda, contacta a nuestro equipo de soporte.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Gestiona y recupera tus gastos de manera inteligente
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Estamos aquí para ayudarte,<br>
                <strong>El equipo de Recupera Gastos</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px; font-weight: 500;">Soporte</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px; font-weight: 500;">Centro de Ayuda</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #1e3a8a; text-decoration: none; font-size: 14px; font-weight: 500;">Contacto</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este es un correo automático de bienvenida.<br>
                Por seguridad, no respondas a este mensaje.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>