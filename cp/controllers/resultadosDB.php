<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../config/db.php'; // Archivo de conexión a la base de datos

try {
    // Validar conexión a la base de datos
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Manejar búsqueda por clave específica
    if (isset($_GET['clave'])) {
        $clave = trim($_GET['clave']);
        if (empty($clave)) {
            http_response_code(400);
            echo json_encode(['error' => 'El parámetro clave no puede estar vacío']);
            exit;
        }

        // Consulta para obtener las respuestas de la clave específica
        $queryClaveEspecifica = "SELECT * FROM cuestionario WHERE clave = :clave";
        $stmtClaveEspecifica = $pdo->prepare($queryClaveEspecifica);
        $stmtClaveEspecifica->bindParam(':clave', $clave, PDO::PARAM_STR);
        $stmtClaveEspecifica->execute();
        $respuestasClave = $stmtClaveEspecifica->fetchAll(PDO::FETCH_ASSOC);

        // Filtrar valores NULL y renombrar columnas
        $respuestasFiltradas = [];
        foreach ($respuestasClave as $fila) {
            $filaFiltrada = [];
            foreach ($fila as $columna => $valor) {
                if ($columna === 'reg_m') {
                    $filaFiltrada['Registro Muestra'] = $valor; // Renombrar reg_m
                } elseif (strpos($columna, 'r') === 0 && $valor !== null) { // Filtrar valores NULL
                    $filaFiltrada['Pregunta ' . substr($columna, 1)] = $valor; // Renombrar rX a Pregunta X
                }
            }
            $respuestasFiltradas[] = $filaFiltrada;
        }

        echo json_encode(['respuestasClave' => $respuestasFiltradas ?: []]); // Devolver array vacío si no hay resultados
        exit;
    }

    // Consulta para contar las filas en la tabla claves (total de encuestas)
    $queryTotalEncuestados = "SELECT COUNT(*) as total FROM claves";
    $stmtTotalEncuestados = $pdo->prepare($queryTotalEncuestados);
    $stmtTotalEncuestados->execute();
    $totalEncuestados = $stmtTotalEncuestados->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Consulta para obtener las encuestas completadas (columna terminada = 1)
    $queryClavesTerminadas = "SELECT COUNT(*) as completadas FROM muestra WHERE terminada = 1";
    $stmtClavesTerminadas = $pdo->prepare($queryClavesTerminadas);
    $stmtClavesTerminadas->execute();
    $encuestasCompletadas = $stmtClavesTerminadas->fetch(PDO::FETCH_ASSOC)['completadas'] ?? 0;

    // Consulta dinámica para obtener los resultados del cuestionario
    $queryColumnas = "SHOW COLUMNS FROM cuestionario"; // Obtener todas las columnas de la tabla
    $stmtColumnas = $pdo->prepare($queryColumnas);
    $stmtColumnas->execute();
    $columnas = $stmtColumnas->fetchAll(PDO::FETCH_COLUMN);

    // Filtrar columnas que comiencen con "r" o sean relevantes (excluyendo reg_m)
    $columnasSeleccionadas = array_filter($columnas, function ($columna) {
        return $columna === 'clave' || $columna === 'date' || strpos($columna, 'r') === 0;
    });

    // Construir la consulta dinámica
    $query = "SELECT " . implode(", ", $columnasSeleccionadas) . " FROM cuestionario";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los resultados para contar cuántas claves han respondido cada rX
    $respuestasPorPregunta = [];
    foreach ($resultados as $fila) {
        // Excluir explícitamente la columna reg_m
        unset($fila['reg_m']);

        foreach ($fila as $pregunta => $valor) {
            if (strpos($pregunta, 'r') === 0 && !empty($valor)) { // Procesar solo las columnas que empiecen con "r"
                $nombrePregunta = 'Pregunta ' . substr($pregunta, 1); // Convertir r1 -> Pregunta 1
                if (!isset($respuestasPorPregunta[$nombrePregunta])) {
                    $respuestasPorPregunta[$nombrePregunta] = [];
                }
                if (!isset($respuestasPorPregunta[$nombrePregunta][$valor])) {
                    $respuestasPorPregunta[$nombrePregunta][$valor] = 0;
                }
                $respuestasPorPregunta[$nombrePregunta][$valor]++;
            }
        }
    }

    // Calcular el promedio dinámico de respuestas
    $promedioRespuestas = 0;
    $numeroPreguntas = 0;
    foreach ($resultados as $fila) {
        // Excluir explícitamente la columna reg_m
        unset($fila['reg_m']);

        $numeroPreguntas = max($numeroPreguntas, count(array_filter(array_keys($fila), fn($key) => strpos($key, 'r') === 0)));
        $promedioRespuestas += count(array_filter($fila, fn($value) => !empty($value) && strpos($value, 'r') === 0));
    }
    $promedioRespuestas = $totalEncuestados > 0 ? round($promedioRespuestas / ($totalEncuestados * $numeroPreguntas), 2) : 0;

    // Devolver datos al frontend
    echo json_encode([
        'respuestasPorPregunta' => $respuestasPorPregunta ?: [], // Devolver array vacío si no hay resultados
        'estadisticas' => [
            'totalEncuestas' => $totalEncuestados,
            'encuestasCompletadas' => $encuestasCompletadas,
            'promedioRespuestas' => $promedioRespuestas
        ]
    ]);
} catch (Exception $e) {
    // Registrar el error para depuración
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error interno del servidor: ' . $e->getMessage()]);
}