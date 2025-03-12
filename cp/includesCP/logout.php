<?php
session_start();

// Destruir la sesión
session_destroy();

// Devolver una respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "message" => "Sesión cerrada correctamente."
]);
?>