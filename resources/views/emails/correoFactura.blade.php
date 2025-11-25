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
    <h3 style="color: #1f2937; font-size: 18px; font-weight: 600;">📋 Detalles del Ticket</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top:12px;">
        <div>
            <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Ticket</p>
            <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['ticket'] ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Establecimiento</p>
            <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['establecimiento'] ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Fecha Ticket</p>
            <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">{{ $datosMail['fecha_ticket'] ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="color: #6b7280; margin: 0 0 4px 0; font-size: 14px; font-weight: 500;">Monto Total</p>
            <p style="color: #1f2937; margin: 0; font-size: 14px; font-weight: 600;">${{ number_format($datosMail['total'] ?? 0, 2) }} MXN</p>
        </div>
    </div>
</div>

<!-- Receptor: Sección Receptor -->
<div style="background-color: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin: 0 0 24px 0;">
    <h3 style="color: #1f2937; font-size: 18px; font-weight: 600;">👤 Datos del Receptor</h3>
    <div style="margin-top:12px;">
        <p style="color:#6b7280; margin:0 0 4px 0; font-size:14px;">Nombre / Razón</p>
        <p style="color:#1f2937; font-weight:600;">{{ $datosMail['nombre_receptor'] ?? 'N/A' }}</p>

        <p style="color:#6b7280; margin:12px 0 4px 0; font-size:14px;">RFC</p>
        <p style="color:#1f2937; font-weight:600;">{{ $datosMail['rfc_receptor'] ?? 'N/A' }}</p>

    </div>
</div>

            <!-- Archivos Adjuntos -->


            <!-- Next Steps -->


            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 32px 0 0 0;">
                Si tienes alguna pregunta sobre esta factura o necesitas asistencia adicional, 
                no dudes en contactar a nuestro equipo de soporte.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">

            <p style="color: #6b7280; margin: 0 0 20px 0; font-size: 14px;">
                Saludos,<br>
                <strong>El equipo de Recupera Gastos</strong>
            </p>
            

            
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