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
    const targetOrigin = {!! json_encode(request()->query('origin', '*')) !!};

    // Enviamos los datos a la ventana que abrió el popup
window.opener.postMessage(data, targetOrigin);
window.close();
</script>
</body>
</html>
