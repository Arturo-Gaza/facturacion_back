<!DOCTYPE html>
<html>
<head>
    <title>Google Auth Callback</title>
</head>
<body>
    <script>

        // Extraer el origin de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const targetOrigin = urlParams.get('origin') || '*';
        
        const payload = {
            type: "google-auth-success",
            status: true,
            user: @json($user),
            token: "{{ $token }}",
            tokenGoogle: "{{ $tokenGoogle }}"
        };


        if (window.opener) {
            try {
                window.opener.postMessage(payload, targetOrigin);
            } catch (error) {
                console.error("Error enviando mensaje:", error);
                
                // Fallback: intentar con origen específico
                const fallbackOrigins = [
                    targetOrigin,
                    window.location.origin,
                    "http://localhost:5173",
                    "http://127.0.0.1:5173",
                    "*"
                ];
                
                for (const origin of fallbackOrigins) {
                    try {
                        window.opener.postMessage(payload, origin);
                        break;
                    } catch (e) {
                        console.warn("Fallback failed for origin:", origin);
                    }
                }
            }
        } else {
            console.error("No hay opener disponible");
            alert("Error: No se puede comunicar con la ventana principal. Por favor cierra esta ventana e intenta nuevamente.");
        }

        // Cerrar después de un tiempo
        setTimeout(() => {
            console.log("Cerrando ventana...");
            window.close();
        }, 1000);
    </script>
</body>
</html>