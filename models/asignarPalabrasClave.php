<?php
include_once 'config/db.php';

try {
    // Obtener los IDGrupo de la tabla claves
    $query = "SELECT IDGrupo FROM claves";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Asignar palabras clave y textos diferentes
    $palabrasClave = [
        1 => 'hola',
        2 => 'adios',
        // Agregar más asignaciones según sea necesario
    ];

    // Generar el resultado
    $resultado = [];
    foreach ($grupos as $grupo) {
        $idGrupo = $grupo['IDGrupo'];
        $resultado[$idGrupo] = $palabrasClave[$idGrupo] ?? 'Texto no asignado';
    }

    // Leer textos.json y Preguntas.json
    $textosPath =  'textos.json';
    $preguntasPath =  'Preguntas.json';

    $textos = file_exists($textosPath) ? json_decode(file_get_contents($textosPath), true) : [];
    $preguntas = file_exists($preguntasPath) ? json_decode(file_get_contents($preguntasPath), true) : [];

    // Asignar palabras clave a textos.json
    foreach ($textos as &$texto) {
        if (isset($texto['IDGrupo']) && isset($resultado[$texto['IDGrupo']])) {
            $texto['texto'] = str_replace('$grupo', $resultado[$texto['IDGrupo']], $texto['texto']);
        }
    }

    // Asignar palabras clave a Preguntas.json
    foreach ($preguntas as &$pregunta) {
        if (isset($pregunta['IDGrupo']) && isset($resultado[$pregunta['IDGrupo']])) {
            $pregunta['titulo'] = str_replace('$grupo', $resultado[$pregunta['IDGrupo']], $pregunta['titulo']);
        }
    }

    // Guardar los cambios en los archivos JSON
    file_put_contents($textosPath, json_encode($textos, JSON_PRETTY_PRINT));
    file_put_contents($preguntasPath, json_encode($preguntas, JSON_PRETTY_PRINT));

    // Mostrar el resultado
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'resultado' => $resultado]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al procesar los archivos JSON: ' . $e->getMessage()]);
}
?>