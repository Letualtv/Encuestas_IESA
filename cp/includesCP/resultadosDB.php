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

    // COnsulta para obtener la columna Terminada de la tabla Claves
    $queryClavesTerminadas = "SELECT COUNT(*) as completadas FROM muestra WHERE terminada = 1";
    $stmtClavesTerminadas = $pdo->prepare($queryClavesTerminadas);
    $stmtClavesTerminadas->execute();
    // Calcular estadísticas generales
    $promedioRespuestas = 0;
    $encuestasCompletadas = $stmtClavesTerminadas->fetch(PDO::FETCH_ASSOC)['completadas'];


    // Procesar los resultados para contar cuántas claves han respondido cada rX
    $respuestasPorPregunta = [];
    foreach ($resultados as $fila) {
        foreach ($fila as $pregunta => $valor) {
            if (strpos($pregunta, 'r') === 0 && !empty($valor)) { // Procesar solo las columnas que empiecen con "r"
                if (!isset($respuestasPorPregunta[$pregunta])) {
                    $respuestasPorPregunta[$pregunta] = [];
                }
                if (!isset($respuestasPorPregunta[$pregunta][$valor])) {
                    $respuestasPorPregunta[$pregunta][$valor] = 0;
                }
                $respuestasPorPregunta[$pregunta][$valor]++;
            }
        }
    }



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
