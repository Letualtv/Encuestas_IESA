<?php
require '../../config/db.php'; // Archivo de conexión a la base de datos

try {
    // Consulta para contar las filas en la tabla claves (total de encuestas)
    $queryTotalEncuestados = "SELECT COUNT(*) as total FROM claves";
    $stmtTotalEncuestados = $pdo->prepare($queryTotalEncuestados);
    $stmtTotalEncuestados->execute();
    $totalEncuestados = $stmtTotalEncuestados->fetch(PDO::FETCH_ASSOC)['total'];

    // Consulta para obtener los resultados del cuestionario
    $query = "SELECT * FROM cuestionario";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los resultados para contar cuántas claves han respondido cada rX
    $respuestasPorPregunta = [];
    foreach ($resultados as $fila) {
        for ($i = 1; $i <= 16; $i++) { // Iterar sobre las columnas r1 a r16
            $pregunta = "r$i";
            if (!empty($fila[$pregunta])) {
                if (!isset($respuestasPorPregunta[$pregunta])) {
                    $respuestasPorPregunta[$pregunta] = [];
                }
                $respuesta = $fila[$pregunta];
                if (!isset($respuestasPorPregunta[$pregunta][$respuesta])) {
                    $respuestasPorPregunta[$pregunta][$respuesta] = 0;
                }
                $respuestasPorPregunta[$pregunta][$respuesta]++;
            }
        }
    }

    // Calcular estadísticas generales
    $encuestasCompletadas = array_reduce($resultados, fn($carry, $fila) => $carry + ($fila['terminada'] == 1 ? 1 : 0), 0);
    $promedioRespuestas = 0;

    foreach ($resultados as $fila) {
        $promedioRespuestas += count(array_filter($fila, fn($value) => !empty($value) && strpos($value, 'r') === 0));
    }
    $promedioRespuestas = $totalEncuestados > 0 ? round($promedioRespuestas / ($totalEncuestados * 16), 2) : 0;

    // Pasar datos al frontend
    echo json_encode([
        'respuestasPorPregunta' => $respuestasPorPregunta,
        'estadisticas' => [
            'totalEncuestas' => $totalEncuestados, // Usar el conteo de la tabla claves
            'encuestasCompletadas' => $encuestasCompletadas,
            'promedioRespuestas' => $promedioRespuestas
        ]
    ]);
} catch (Exception $e) {
    // Registrar el error para depuración
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error interno del servidor']);
}
?>