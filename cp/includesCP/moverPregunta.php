<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? null;
    $direccion = $_GET['direccion'] ?? null;

    if (!$id || !$direccion) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
        exit;
    }

    $archivo = '../../models/Preguntas.json';
    if (!file_exists($archivo)) {
        echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
        exit;
    }

    $preguntas = json_decode(file_get_contents($archivo), true);
    $index = array_search($id, array_column($preguntas, 'id'));

    if ($index === false) {
        echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada.']);
        exit;
    }

    if ($direccion === 'arriba' && $index > 0) {
        $temp = $preguntas[$index];
        $preguntas[$index] = $preguntas[$index - 1];
        $preguntas[$index - 1] = $temp;
    } elseif ($direccion === 'abajo' && $index < count($preguntas) - 1) {
        $temp = $preguntas[$index];
        $preguntas[$index] = $preguntas[$index + 1];
        $preguntas[$index + 1] = $temp;
    }

    // Reordenar IDs y actualizar claves de opciones para matrices
    foreach ($preguntas as $i => &$pregunta) {
        $pregunta['id'] = $i + 1;

        // Si es una matriz, actualizar las claves de las opciones
        if (in_array($pregunta['tipo'], ['matrix1', 'matrix2', 'matrix3']) && isset($pregunta['opciones'])) {
            $nuevaClave = $pregunta['id']; // La nueva clave inicial será el ID de la matriz
            $opcionesActualizadas = [];
            foreach ($pregunta['opciones'] as $key => $valor) {
                $opcionesActualizadas[$nuevaClave] = $valor; // Asignar la nueva clave
                $nuevaClave++;
            }
            $pregunta['opciones'] = $opcionesActualizadas; // Actualizar las opciones con las nuevas claves
        }
    }

    file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
