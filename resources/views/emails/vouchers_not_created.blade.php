<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobantes no subidos</title>
</head>
<body>
<h1>Estimado {{ $user->name }},</h1>
<p>Tu solicitud de procesamiento de comprobantes ha sido completada.</p>
<p>Lamentablemente, no se pudieron registrar los siguientes comprobantes:</p>

@foreach ($comprobantes as $comprobante)
    <ul>
        <li>Serie: {{ $comprobante['serie'] }}</li>
        <li>Número: {{ $comprobante['number'] }}</li>
        <li>Error: {{$comprobante['error_reason']}}</li>
    </ul>
@endforeach

<p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
