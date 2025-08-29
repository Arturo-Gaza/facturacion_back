<div style="font-family: Arial, sans-serif; line-height: 1.5;">
    <h2 style="color: #2c3e50;">Saludos {{ $datosUsr['nombre'] }}</h2>

    <p>
        <p>Queremos informarte que la solicitud con el <strong>ticket #{{ $datosSol['ticket'] }}</strong> ha cambiado de estado.</p>
    </p>

    <ul>
        <li><strong>Nuevo estatus :</strong> {{ $datosSol['estatus'] }}</li>
        <li><strong>Prioridad:</strong> {{ $datosSol['prioridad'] ?? 'N/A' }}</li>
        <li><strong>Departamento:</strong> {{ $datosSol['departamento'] }}</li>
    </ul>

    <p>
        Puedes consultar más detalles en el sistema.
    </p>

    <hr style="margin-top: 30px;">

    <p style="font-size: 12px; color: #888;">
        Este mensaje fue generado automáticamente. No respondas a este correo.
    </p>
</div>
