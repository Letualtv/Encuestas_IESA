<?php
$archivo = '../../models/Preguntas.json';

try {
    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al leer el archivo JSON: ' . json_last_error_msg());
        }

        $ultimoId = 0;

        foreach ($preguntas as $pregunta) {
            $ultimoId = max($ultimoId, $pregunta['id']);

            if (in_array($pregunta['tipo'], ['matrix1', 'matrix2']) && isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
                $ultimoId = max($ultimoId, max(array_keys($pregunta['opciones'])));
            }

            if ($pregunta['tipo'] === 'matrix3' && isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
                foreach ($pregunta['opciones'] as $opcion) {
                    if (isset($opcion['subLabel']) && is_array($opcion['subLabel'])) {
                        $ultimoId = max($ultimoId, max(array_keys($opcion['subLabel'])));
                    }
                }
            }
        }

        $siguienteId = $ultimoId + 1;
        echo json_encode(['success' => true, 'siguienteId' => $siguienteId]);
    } else {
        // Si el archivo no existe, el primer ID será 1
        echo json_encode(['success' => true, 'siguienteId' => 1]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

// Garantizar que siempre se devuelva una respuesta JSON válida
if (!isset($siguienteId)) {
    echo json_encode(['success' => false, 'message' => 'Error desconocido al calcular el siguiente ID.']);
}