<?php
session_start();

// Verificar si hay una cookie de sesión disponible
if (isset($_COOKIE['reg_m'])) {
    session_id($_COOKIE['reg_m']);
    session_start();
    $_SESSION['LAST_ACTIVITY'] = time(); // Actualizar la hora de la última actividad

    // Redirigir al usuario a la última página completada o a la página principal
    $lastPage = $_SESSION['current_page'] ?? 'indice';
    header("Location: $lastPage");
    exit;
} else {
    // Redirigir al usuario a la página de inicio de sesión si no hay cookie de sesión disponible
    header('Location: login');
    exit;
}
?>
