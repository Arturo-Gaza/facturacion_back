<!DOCTYPE html>
<html>
<head>
    <title>Google Auth Callback</title>
</head>
<body>
    <script>
        // 1. Extraer la clave de transacción
        debugger
        const urlParams = new URLSearchParams(window.location.search);
        const transactionKey = "{{ $transactionKey ?? null }}";
        
        // El payload sigue siendo el mismo
        const payload = {
            type: "google-auth-success",
            status: true,
            user: @json($user),
            token: "{{ $token }}",
            tokenGoogle: "{{ $tokenGoogle }}"
        };
   
        if (transactionKey && window.localStorage) {
            try {
                // 2. Guardar el payload en localStorage usando la clave
                window.localStorage.setItem(transactionKey, JSON.stringify(payload));
                
                // 3. Cerrar la ventana para activar el listener del opener
                window.close();

            } catch (error) {
                console.error("Error guardando payload en localStorage:", error);
                alert("Error de comunicación segura. Por favor intenta nuevamente.");
            }
        } else {
            console.error("No se encontró clave de transacción o localStorage no disponible");
            console.error(transactionKey);
            alert("Error: Fallo en la configuración de seguridad. Por favor cierra esta ventana e intenta nuevamente.");
        }

    </script>
</body>
</html>