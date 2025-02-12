<?php
$archivo = '../models/Preguntas.json'; // Asegúrate de que la ruta sea correcta

// Mensaje de depuración: verificar la ruta del archivo
error_log("Ruta del archivo JSON: " . $archivo);

if (file_exists($archivo)) {
    $preguntas = json_decode(file_get_contents($archivo), true);

    // Mensaje de depuración: verificar si la decodificación JSON fue exitosa
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error en la decodificación JSON: " . json_last_error_msg());
        echo json_encode(['success' => false, 'message' => 'Error al decodificar JSON.']);
        exit;
    }

    // Mensaje de depuración: verificar el contenido decodificado
    error_log("Contenido del archivo JSON: " . json_encode($preguntas, JSON_PRETTY_PRINT));
    
    // Devolver todas las preguntas
    echo json_encode($preguntas);
    exit;
} else {
    error_log("Archivo no encontrado: " . $archivo);
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
    exit;
}

echo json_encode(null);
?>
