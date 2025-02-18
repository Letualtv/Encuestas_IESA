<?php
session_start();

// Intentar recuperar la sesión si existe una cookie válida
if (isset($_COOKIE['PHPSESSID'])) {
    session_id($_COOKIE['PHPSESSID']);
    session_start();
    if (isset($_SESSION['user_id'])) {
        // Si la sesión es válida, redirigir al usuario a la página anterior
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'cuestionario'));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión expirada</title>
    <!-- Incluir el CSS de Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container d-flex flex-column justify-content-center align-items-center flex-grow-1">
        <div class="card text-center shadow-lg p-2">
            <div class="card-body">
                <h1>Tu sesión ha expirado</h1>
                <p class="py-2">Haz clic en el botón de abajo para volver a la página anterior y reiniciar la sesión.</p>
                <a href="encuesta"><button class="btn btn-primary">Reiniciar sesión</button></a>
            </div>
        </div>
    </div>
    <!-- Incluir el JavaScript de Bootstrap 5.3.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>

    </script>
</body>
</html>