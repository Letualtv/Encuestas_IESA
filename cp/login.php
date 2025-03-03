<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/login.js" defer></script>

</head>

<body class="bg-secondary">

    <div class="container d-flex justify-content-center align-items-center " style="height: 100vh;">
        <div class="login-container col-12 col-md-6 col-lg-5 card p-4">
            <h3 class="text-center ">Panel de control UTEA</h3>
            <hr>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
            <div id="mensaje" class="mt-3 text-center"></div>
            <div class="text-center small text-muted">Si necesita ayuda contacte con <a class="fw-bold text-decoration-none" href="mailto:ejemplo@ejemplo.com">ejemplo@ejemplo.com</a></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>