<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida al Sistema de Mesa de Ayuda</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #059669, #047857); padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="white"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Mesa de Ayuda</h1>
            </div>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px; font-weight: 500;">¡Tu cuenta ha sido creada!</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                ¡Bienvenido/a {{ $datosMail['nombre'] ?? $datosMail['email'] }}!
            </h2>
            
            <p style="color: #6b7280; margin: 0 0 24px 0; font-size: 16px;">
                Has sido registrado/a en el sistema de Mesa de Ayuda
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px 0;">
                Te damos la bienvenida al <strong>Sistema de Mesa de Ayuda</strong>. Para comenzar a utilizar la plataforma, necesitas completar tu registro siguiendo los pasos a continuación.
            </p>

            <!-- Credenciales de Acceso -->
            <div style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 12px; padding: 32px; text-align: center; margin: 32px 0;">
                <p style="color: #047857; margin: 0 0 8px 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Credenciales de Acceso Temporal
                </p>
                
                <div style="text-align: left; background-color: #ffffff; border-radius: 8px; padding: 20px; margin: 16px 0; border: 1px solid #d1fae5;">
                    <div style="margin-bottom: 16px;">
                        <p style="color: #065f46; margin: 0 0 4px 0; font-size: 14px; font-weight: 600;">Usuario / Email:</p>
                        <p style="color: #374151; margin: 0; font-size: 16px; font-weight: 500; padding: 8px 12px; background-color: #f8f9fa; border-radius: 4px;">
                            {{ $datosMail['email'] }}
                        </p>
                    </div>
                    <div>
                        <p style="color: #065f46; margin: 0 0 4px 0; font-size: 14px; font-weight: 600;">Contraseña Temporal:</p>
                        <div style="background: linear-gradient(135deg, #10b981, #047857); border-radius: 8px; padding: 16px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; font-family: 'Courier New', monospace; letter-spacing: 1px;">
                                {{ $datosMail['password_temporal'] }}
                            </h1>
                        </div>
                    </div>
                </div>
                
                <p style="color: #047857; margin: 8px 0 0 0; font-size: 14px;">
                    <strong>⚠️ Esta contraseña es temporal y debe ser cambiada en el primer acceso</strong>
                </p>
            </div>

            <!-- Proceso de Configuración -->
            <div style="background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 32px 0;">
                <h3 style="color: #1e293b; margin: 0 0 20px 0; font-size: 18px; font-weight: 600; text-align: center;">
                    📋 Proceso de Configuración de Cuenta
                </h3>
                
                <!-- Paso 1 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #10b981;">
                    <div style="background-color: #10b981; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        1
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Primer Ingreso</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Accede al sistema con tu email y la contraseña temporal proporcionada.
                        </p>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                    <div style="background-color: #0ea5e9; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        2
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Cambio de Contraseña</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Establece una nueva contraseña segura para tu cuenta.
                        </p>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                    <div style="background-color: #8b5cf6; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        3
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Completa tu Perfil</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Actualiza tu información personal y de contacto en el sistema.
                        </p>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div style="display: flex; align-items: flex-start; padding: 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="background-color: #f59e0b; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 12px; font-weight: bold;">
                        4
                    </div>
                    <div>
                        <h4 style="color: #1e293b; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Familiarízate con el Sistema</h4>
                        <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.5;">
                            Explora las funcionalidades y módulos disponibles según tu rol.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
                <div style="display: flex; align-items: flex-start;">
                    <div style="background-color: #3b82f6; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="color: #1e40af; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Información Importante</h4>
                        <p style="color: #1e40af; font-size: 14px; margin: 0; line-height: 1.5;">
                            • Rol asignado: <strong>{{ $datosMail['rol'] ?? 'Usuario' }}</strong><br>
                            • Departamento: <strong>{{ $datosMail['departamento'] ?? 'Por definir' }}</strong><br>
                            • Accesos: <strong>{{ $datosMail['permisos'] ?? 'Básicos' }}</strong>
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
                        <h4 style="color: #92400e; margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Seguridad y Confidencialidad</h4>
                        <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.5;">
                            • La contraseña temporal es de un solo uso<br>
                            • Debes cambiarla inmediatamente después del primer ingreso<br>
                            • No compartas tus credenciales con nadie<br>
                            • Mantén la confidencialidad de la información del sistema
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ $datosMail['url_login'] ?? '#' }}" style="display: inline-block; background: linear-gradient(135deg, #059669, #047857); color: white; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2);">
                    🚀 Acceder al Sistema
                </a>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si encuentras problemas con el acceso o necesitas asistencia técnica, contacta al administrador del sistema o al departamento de TI.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Sistema de Mesa de Ayuda - Gestión de Tickets y Soporte
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Para asistencia inmediata,<br>
                <strong>El equipo de Sistemas y TI</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Soporte Técnico</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Manual de Usuario</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Contacto TI</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este es un correo automático de activación de cuenta.<br>
                Por seguridad, no respondas a este mensaje.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Sistema de Mesa de Ayuda. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>