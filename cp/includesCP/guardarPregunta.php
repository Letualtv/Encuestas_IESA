<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar campos obligatorios
        if (!isset($data['titulo'], $data['n_pag'], $data['tipo'])) {
            throw new Exception('Faltan campos obligatorios.');
        }

        // Asignar valores
        $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : time();
        $titulo = $data['titulo'];
        $n_pag = $data['n_pag'];
        $tipo = $data['tipo'];
        $subTitulo = $data['subTitulo'] ?? '';
        $opciones = isset($data['opciones']) ? (object)$data['opciones'] : (object)[];
        $valores = $data['valores'] ?? [];
        $filtro = isset($data['filtro']) && is_array($data['filtro']) ? (object)$data['filtro'] : (object)[];
        $descripcion = $data['cabecera'] ?? null;

        // Procesar cabecera
        $cabecera = [
            'texto' => $descripcion && isset($descripcion['texto']) && !empty($descripcion['texto']) ? $descripcion['texto'] : '',
        ];

        // Procesar encabezado para matrix2 y matrix3
        $encabezado = [];
        if (($tipo === 'matrix2' || $tipo === 'matrix3') && isset($data['encabezado'])) {
            $encabezado = [
                'label' => $data['encabezado']['label'] ?? '',
                'uno' => isset($data['encabezado']['uno']) ? (object)$data['encabezado']['uno'] : (object)[],
                'dos' => isset($data['encabezado']['dos']) ? (object)$data['encabezado']['dos'] : (object)[],
                'tres' => $data['encabezado']['tres'] ?? '',
            ];
        }

        // Crear la nueva pregunta
        $nuevaPregunta = [
            'id' => $id,
            'n_pag' => (int)$n_pag,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'subTitulo' => $subTitulo,
            'opciones' => $opciones,
            'filtro' => $filtro,
            'cabecera' => $cabecera,
        ];

        // Agregar encabezado si existe
        if (!empty($encabezado)) {
            $nuevaPregunta['encabezado'] = $encabezado;
        }

        // Manejar valores específicos para numberInput
        if ($tipo === 'numberInput') {
            $nuevaPregunta['valores'] = [
                'min' => isset($valores['min']) ? (int)$valores['min'] : 1950,
                'max' => isset($valores['max']) ? (int)$valores['max'] : 2025,
                'placeholder' => isset($valores['placeholder']) ? $valores['placeholder'] : 'AAAA',
            ];
        }

        // Manejar placeholder para cajaTexto
        if ($tipo === 'cajaTexto') {
            $nuevaPregunta['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
        }

        // Guardar en el archivo JSON
        $archivo = '../../models/Preguntas.json';
        if (file_exists($archivo)) {
            $preguntas = json_decode(file_get_contents($archivo), true);

            // Buscar y actualizar la pregunta si ya existe
            $found = false;
            foreach ($preguntas as &$pregunta) {
                if ($pregunta['id'] == $id) {
                    $pregunta = $nuevaPregunta;
                    $found = true;
                    break;
                }
            }

            // Si no se encontró, añadir la nueva pregunta
            if (!$found) {
                $preguntas[] = $nuevaPregunta;
            }

            file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
        } else {
            // Crear un nuevo archivo JSON con la primera pregunta
            file_put_contents($archivo, json_encode([$nuevaPregunta], JSON_PRETTY_PRINT));
        }

        // Respuesta exitosa
        echo json_encode(['success' => true, 'message' => 'Pregunta guardada correctamente.']);
    } catch (Exception $e) {
        // Respuesta en caso de error
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>