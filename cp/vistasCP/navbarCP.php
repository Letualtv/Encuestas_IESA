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
<nav class="navbar navbar-expand navbar-light mb-3  shadow">
  <div class="container-fluid mx-3">

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="nav nav-pills  gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="controlPanel.php">Encuesta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="parametrosGenerales.php">Parámetros Generales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="textos.php">Textos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php">Usuarios</a>
                </li>
            </ul>
        </div>
      
        <img src="../assets/img/2.png" alt="Logo" width="180" class="d-inline-block align-text-top ms-auto">
</div>
</nav>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
</body>
</html>
