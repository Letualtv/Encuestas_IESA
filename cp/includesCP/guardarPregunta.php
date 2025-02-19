<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : time();
    $titulo = $data['titulo'];
    $n_pag = $data['n_pag'];
    $tipo = $data['tipo'];
    $subTitulo = $data['subTitulo'];
    $opciones = $data['opciones']; // Opciones ya deben ser un objeto clave-valor
    $valores = isset($data['valores']) ? $data['valores'] : [];
    $filtro = isset($data['filtro']) ? (object)$data['filtro'] : (object)[]; // Filtro como objeto
    $descripcion = $data['cabecera'] ?? null;
    $texto1 = $descripcion && isset($descripcion['texto1']) ? $descripcion['texto1'] : '';
    $lista = $descripcion && isset($descripcion['lista']) ? $descripcion['lista'] : '';
    $texto2 = $descripcion && isset($descripcion['texto2']) ? $descripcion['texto2'] : '';

// Procesar el encabezado si es matrix2 o matrix3
$encabezado = [];
if (($data['tipo'] === 'matrix2' || $data['tipo'] === 'matrix3') && isset($data['encabezado'])) {
    $encabezado = [
        'label' => $data['encabezado']['label'] ?? '',
        'uno' => isset($data['encabezado']['uno']) ? (object)$data['encabezado']['uno'] : (object)[],
        'dos' => isset($data['encabezado']['dos']) ? (object)$data['encabezado']['dos'] : (object)[],
        'tres' => $data['encabezado']['tres'] ?? '',
    ];
}



    $nuevaPregunta = [
        'id' => $id,
        'n_pag' => (int)$n_pag,
        'tipo' => $tipo,
        'titulo' => $titulo,
        'subTitulo' => $subTitulo,
        'opciones' => $opciones,
        'filtro' => $filtro,
        'cabecera' => [
            'texto1' => $texto1,
            'lista' => $lista,
            'texto2' => $texto2,
        ],

    ];

// Agregar el encabezado solo si contiene datos válidos
if (!empty($encabezado)) {
    $nuevaPregunta['encabezado'] = $encabezado;
}
    if ($tipo === 'numberInput') {
        $nuevaPregunta['valores'] = [
            'min' => isset($valores['min']) ? (int)$valores['min'] : 1950,
            'max' => isset($valores['max']) ? (int)$valores['max'] : 2025,
            'placeholder' => isset($valores['placeholder']) ? $valores['placeholder'] : 'AAAA',
        ];
    }

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
        $preguntas = [$nuevaPregunta];
        file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
