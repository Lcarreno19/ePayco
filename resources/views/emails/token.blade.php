<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>Hola {{ $name }}, solo falta un paso mas!</h2>
    <p>Por favor confirma tu compra realizada.</p>
    <p>Para ello simplemente debes hacer click en el siguiente enlace:</p>

    <a href="{{ url('/api/verificar/' . $code.'/'.$confirmation_code) }}">
        Clic para confirmar tu compra
    </a>
</body>
</html>
