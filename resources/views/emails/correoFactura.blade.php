<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Recuperada - Recupera Gastos</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #059669; padding: 40px 30px; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Recupera Gastos</h1>
            </div>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px; font-weight: 500;">Factura recuperada exitosamente</p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <!-- Success Alert -->
            <div style="background-color: #ecfdf5; border: 1px solid #d1fae5; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center;">
                    <div style="background-color: #10b981; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: #065f46; margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">¡Factura recuperada!</h3>
                        <p style="color: #047857; margin: 0; font-size: 14px;">Hemos procesado exitosamente tu solicitud</p>
                    </div>
                </div>
            </div>

            <h2 style="color: #1f2937; margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">
                Hola {{ $datosMail['nombre'] ?? 'Usuario' }},
            </h2>
            
            <p style="color: #6b7280; margin: 0 0 24px 0; font-size: 16px;">
                Tu factura ha sido recuperada exitosamente
            </p>

            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px 0;">
                El sistema <strong>Recupera Gastos</strong> ha procesado tu solicitud y hemos recuperado los documentos fiscales de tu factura. 
                A continuación encontrarás los detalles y los archivos adjuntos.
            </p>

            <!-- Factura Details -->
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin: 24px 0;">
                <h3 style="color: #1f2937; margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">📋 Detalles de la Factura</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Folio Fiscal</p>
                        <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['uuid'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Fecha de Emisión</p>
                        <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['fecha'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">RFC Emisor</p>
                        <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['rfc_emisor'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Monto Total</p>
                        <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">${{ number_format($datosMail['total'] ?? 0, 2) }} MXN</p>
                    </div>
                </div>
            </div>

            <!-- Archivos Adjuntos -->
            <div style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 24px; margin: 24px 0;">
                <h3 style="color: #1e40af; margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">📎 Archivos Adjuntos</h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; padding: 12px; background-color: white; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <div style="background-color: #dc2626; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="white" stroke-width="2"/>
                                <path d="M14 2v6h6" stroke="white" stroke-width="2"/>
                                <path d="M16 13H8" stroke="white" stroke-width="2"/>
                                <path d="M16 17H8" stroke="white" stroke-width="2"/>
                                <path d="M10 9H8" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <p style="color: #1f2937; margin: 0 0 2px 0; font-size: 14px; font-weight: 600;">Factura PDF</p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">Documento fiscal en formato PDF</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; padding: 12px; background-color: white; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <div style="background-color: #059669; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="white" stroke-width="2"/>
                                <path d="M14 2v6h6" stroke="white" stroke-width="2"/>
                                <path d="M16 13H8" stroke="white" stroke-width="2"/>
                                <path d="M16 17H8" stroke="white" stroke-width="2"/>
                                <path d="M10 9H9" stroke="white" stroke-width="2"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <p style="color: #1f2937; margin: 0 0 2px 0; font-size: 14px; font-weight: 600;">XML de la Factura</p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">Archivo XML con los datos estructurados</p>
                        </div>
                    </div>
                </div>
                
                <p style="color: #374151; font-size: 14px; line-height: 1.5; margin: 16px 0 0 0;">
                    <strong>Nota:</strong> Ambos archivos están adjuntos a este correo electrónico. 
                    El PDF es para visualización e impresión, mientras que el XML contiene los datos estructurados 
                    para procesos fiscales y contables.
                </p>
            </div>

            <!-- Next Steps -->
            <div style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 20px; margin: 24px 0;">
                <h3 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px; font-weight: 600;">📝 Próximos pasos recomendados:</h3>
                <ol style="color: #92400e; font-size: 14px; margin: 0; padding-left: 16px; line-height: 1.6;">
                    <li style="margin-bottom: 6px;">Guarda los archivos en tu sistema de contabilidad</li>
                    <li style="margin-bottom: 6px;">Registra la factura en tu declaración mensual</li>
                    <li>Mantén los archivos por al menos 5 años como respaldo fiscal</li>
                </ol>
            </div>

            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si tienes alguna pregunta sobre esta factura o necesitas asistencia adicional, 
                no dudes en contactar a nuestro equipo de soporte.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #374151; margin: 0 0 16px 0; font-size: 16px; font-weight: 500;">
                Recuperación inteligente de gastos fiscales
            </p>
            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de Recupera Gastos</strong>
            </p>
            
            <div style="margin: 24px 0;">
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Centro de Ayuda</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Contactar Soporte</a>
                <span style="color: #d1d5db;">|</span>
                <a href="#" style="display: inline-block; margin: 0 12px; color: #059669; text-decoration: none; font-size: 14px; font-weight: 500;">Términos</a>
            </div>
            
            <hr style="border: none; height: 1px; background-color: #e5e7eb; margin: 24px 0;">
            
            <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
                Este es un correo automático del sistema de Recupera Gastos.<br>
                Por favor, no respondas a este mensaje.<br>
                <br>
                <span style="color: #d1d5db;">© {{ date('Y') }} Recupera Gastos. Todos los derechos reservados.</span>
            </p>
        </div>
    </div>
</body>
</html>