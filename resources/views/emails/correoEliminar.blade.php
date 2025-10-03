<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Eliminación Permanente de Cuenta</title>
</head>
<body style="margin: 0; padding: 0; background-color: #fef2f2; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="white"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Recupera Gastos</h1>
            </div>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px; font-weight: 500;">ELIMINACIÓN PERMANENTE</p>
        </div>

        <!-- Warning Banner -->
        <div style="background-color: #fef2f2; border: 2px solid #fecaca; padding: 20px; text-align: center;">
            <div style="display: inline-flex; align-items: center; background-color: #dc2626; color: white; padding: 8px 16px; border-radius: 20px; margin-bottom: 12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="white"/>
                </svg>
                <span style="font-size: 14px; font-weight: 600;">ACCIÓN IRREVERSIBLE</span>
            </div>
            <p style="color: #dc2626; margin: 0; font-size: 14px; font-weight: 500;">
                ⚠️ Esta acción no se puede deshacer
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                Estimado/a {{ $datosMail['nombre'] }}
            </h2>
            
            <p style="color: #dc2626; margin: 0 0 24px 0; font-size: 16px; font-weight: 500;">
                Confirmación de solicitud de ELIMINACIÓN PERMANENTE
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                Has solicitado <strong style="color: #dc2626;">eliminar permanentemente</strong> tu cuenta y todos los datos asociados. Para confirmar esta acción irreversible, utiliza el siguiente código:
            </p>

            <!-- Verification Code Box -->
            <div style="background-color: #fef2f2; border: 2px solid #dc2626; border-radius: 12px; padding: 32px; text-align: center; margin: 32px 0;">
                <p style="color: #991b1b; margin: 0 0 8px 0; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Código de Confirmación
                </p>
                <div style="background: linear-gradient(135deg, #dc2626, #b91c1c); border-radius: 8px; padding: 20px; margin: 16px 0;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 36px; font-weight: 700; letter-spacing: 4px; font-family: 'Courier New', monospace;">
                        {{ $datosMail['codigo'] }}
                    </h1>
                </div>
                <p style="color: #991b1b; margin: 8px 0 0 0; font-size: 14px;">
                    Este código es válido por <strong style="color: #7f1d1d;">10 minutos</strong>
                </p>
            </div>

            <!-- Critical Warning -->
            <div style="background-color: #fef2f2; border: 2px solid #dc2626; border-radius: 8px; padding: 24px; margin: 32px 0;">
                <div style="display: flex; align-items: flex-start;">
                    <div style="background-color: #dc2626; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: #dc2626; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">¡ADVERTENCIA CRÍTICA!</h3>
                        <p style="color: #991b1b; font-size: 14px; margin: 0; line-height: 1.6;">
                            Una vez confirmada esta acción, <strong>todos tus datos serán eliminados permanentemente</strong> y no podrán recuperarse. Esto incluye:
                        </p>
                        <ul style="color: #991b1b; font-size: 14px; margin: 12px 0 0 0; padding-left: 16px; line-height: 1.6;">
                            <li style="margin-bottom: 4px;">Historial completo de gastos y solicitudes</li>
                            <li style="margin-bottom: 4px;">Datos fiscales y documentos</li>
                            <li style="margin-bottom: 4px;">Información personal y de contacto</li>
                            <li>Configuraciones y preferencias de cuenta</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- What happens next -->
            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 20px; margin: 32px 0; border-radius: 0 8px 8px 0;">
                <h3 style="color: #991b1b; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">📋 ¿Qué sucede después de la eliminación?</h3>
                <ol style="color: #991b1b; font-size: 14px; margin: 0; padding-left: 16px; line-height: 1.6;">
                    <li style="margin-bottom: 4px;">Todos tus datos serán eliminados permanentemente</li>
                    <li style="margin-bottom: 4px;">No podrás recuperar tu cuenta ni la información</li>
                    <li style="margin-bottom: 4px;">Se perderá el acceso a todos los servicios</li>
                    <li>No será posible reactivar la cuenta</li>
                </ol>
            </div>

            <!-- Final Chance -->
            <div style="background-color: #fffbeb; border: 2px solid #f59e0b; border-radius: 8px; padding: 24px; margin: 32px 0;">
                <h3 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">💡 ¿Estás completamente seguro?</h3>
                <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.6;">
                    Si tienes alguna duda, considera <strong>inhabilitar temporalmente</strong> tu cuenta en lugar de eliminarla permanentemente. 
                    La inhabilitación te permite reactivar tu cuenta cuando lo desees, manteniendo todos tus datos seguros.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Si no solicitaste esta acción, contacta inmediatamente a soporte
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de soporte</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 500;">Soporte Urgente</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 500;">Centro de Ayuda</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 500;">Seguridad</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este es un mensaje de seguridad crítico. No compartas este código con nadie.<br>
                Si no reconoces esta solicitud, ignora este correo y contacta a soporte inmediatamente.<br>
                <br>
                <span style="color: #d1d5db;">© 2024 Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>