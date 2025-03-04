<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="vistasCP/style.css">
</head>

<body>
    <nav class="navbar navbar-expand navbar-light mb-4  shadow">
        <div class="container-fluid mx-2">

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="nav nav-pills gap-2 w-100">
                    <li class="nav-item">
                        <a class="nav-link" href="controlPanel.php">Modificar encuesta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parametrosGenerales.php">Parámetros generales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="textos.php">Textos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="poblacion.php">Población</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="exportar.php">Exportar</a>
                    </li>
                    <div class="ms-auto d-flex">

                    <div class="d-flex col align-items-center gap-2 justify-content-end">
                    <div class="d-flex align-items-center gap-3">
    <!-- Información del usuario -->
    <div class="d-flex flex-column text-end">
        <span id="nombreUsuario" class="fw-bold">Cargando...</span>
        <span id="rolUsuario" class="badge bg-primary">Cargando...</span>
    </div>

    <!-- Botón de cierre de sesión -->
    <button id="btnCerrarSesion" class="btn btn-danger ">
        <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
    </button>
</div>
</div>
                    </div>
                </ul>
            </div>


        </div>
    </nav>

    <!-- Scripts -->


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Obtener la URL actual
            const currentUrl = window.location.pathname.split("/").pop();

            // Obtener todos los enlaces de navegación
            const navLinks = document.querySelectorAll('.nav-link');

            // Recorrer los enlaces y añadir la clase "active" al enlace correspondiente
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentUrl) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/usuarioNavbar.js"></script>
</body>

</html>