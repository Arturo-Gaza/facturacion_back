<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Google Auth Callback</title>
</head>
<body>
  <script>
    // Los datos se inyectan desde Laravel
    const data = {
      type: "google-auth-success", // 👈 importante para identificar el mensaje
      status: true,
      user: @json($user),
      token: "{{ $token }}",
      tokenGoogle: "{{ $tokenGoogle }}"
    };

    // El frontend pasó ?origin=http://localhost:5173 o similar
    const targetOrigin = {!! json_encode(request()->query('origin', 'http://localhost:5173')) !!};

    if (window.opener) {
      window.opener.postMessage(data, targetOrigin);
      window.close();
    } else {
      document.write("No se pudo comunicar con la aplicación principal.");
    }
  </script>
</body>
</html>