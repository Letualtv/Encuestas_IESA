<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivo = '../../models/Preguntas.json';

    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Error al leer el archivo JSON.']);
            exit;
        }

        $siguienteId = 1;

        foreach ($preguntas as &$pregunta) {
            if (in_array($pregunta['tipo'], ['matrix1', 'matrix2']) && isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
                $nuevaClave = $siguienteId;
                $opcionesActualizadas = [];

                foreach ($pregunta['opciones'] as $key => $valor) {
                    $opcionesActualizadas[$nuevaClave] = $valor;
                    $nuevaClave++;
                }

                $pregunta['opciones'] = $opcionesActualizadas;
                $pregunta['id'] = $siguienteId; // Asignar el ID actual
                $siguienteId = $nuevaClave; // Actualizar el siguiente ID basado en el último key de opciones
            } elseif ($pregunta['tipo'] === 'matrix3' && isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
                $ultimoKeySubLabel = 0;

                foreach ($pregunta['opciones'] as $opcion) {
                    if (isset($opcion['subLabel']) && is_array($opcion['subLabel'])) {
                        $ultimoKeySubLabel = max($ultimoKeySubLabel, ...array_map('intval', array_keys($opcion['subLabel'])));
                    }
                }

                $pregunta['id'] = $siguienteId; // Asignar el ID actual
                $siguienteId = $ultimoKeySubLabel + 1; // Actualizar el siguiente ID basado únicamente en los keys de subLabel
            } else {
                $pregunta['id'] = $siguienteId; // Asignar el siguiente ID para preguntas normales
                $siguienteId++;
            }
        }

        // Guardar las preguntas reordenadas en el archivo JSON
        file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));

        echo json_encode(['success' => true, 'message' => 'Preguntas reordenadas correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
