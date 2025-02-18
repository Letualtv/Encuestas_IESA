<?php
$archivo = '../../models/Preguntas.json'; // Asegúrate de que la ruta sea correcta

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

    // Obtener el ID de la pregunta desde la solicitud GET
    $id = $_GET['id'] ?? null;

    if ($id) {
        // Buscar la pregunta por ID
        $pregunta = null;
        foreach ($preguntas as $p) {
            if ($p['id'] == $id) {
                $pregunta = $p;
                break;
            }
        }

        if (!$pregunta) {
            error_log("Pregunta no encontrada para el ID: " . $id);
            echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada']);
            exit;
        }

        // Devolver la pregunta como JSON
        echo json_encode($pregunta);
        exit;
    } else {
        // Devolver todas las preguntas
        echo json_encode($preguntas);
        exit;
    }
} else {
    error_log("Archivo no encontrado: " . $archivo);
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
    exit;
}
?>