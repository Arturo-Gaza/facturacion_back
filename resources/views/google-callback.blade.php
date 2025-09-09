<!DOCTYPE html>
<html>
<head>
    <title>Google Auth Callback</title>
</head>
<body>
<script>
    // Los datos se inyectan desde Laravel
    const data = {
        status: true,
        user: @json($user),
        token: "{{ $token }}",
        tokenGoogle: "{{ $tokenGoogle }}"
    };

    // Enviamos los datos a la ventana que abrió el popup
window.opener.postMessage(data, "http://127.0.0.1:5173");
window.close();
</script>
</body>
</html>
