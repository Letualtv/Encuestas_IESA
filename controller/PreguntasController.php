<?php
session_start();
include_once __DIR__ . '/../config/db.php';

class PreguntasController
{
   public function mostrarPreguntasPorPagina(int $n_pag): array
{
    // Usar reg_m en lugar de clave_id
    $reg_m = $_SESSION['reg_m'];
    if ($this->verificarEncuestaFinalizada($reg_m)) {
        header('Location: encuestafinalizada');
        exit;
    }
    // Recuperar respuestas de la base de datos y cargarlas en la sesión
    $respuestas = $this->recuperarRespuestasDeBD($reg_m);
    // Redirigir al usuario a la última página completada si no se especifica una página
    if (!isset($_GET['n_pag'])) {
        $currentPag = $this->calcularPaginaActual($respuestas);
        header("Location: ?n_pag=$currentPag");
        exit;
    }
    // Obtener las preguntas y filtrar por página actual
    $preguntas = $this->obtenerPreguntas();
    $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag);
    // Si no hay preguntas para esta página, devolver un error
    if (empty($preguntasEnPagina)) {
        return [
            'error' => true,
            'view' => __DIR__ . '/../views/errors/errorPregunta.php',
        ];
    }
    // Procesar respuestas si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->guardarRespuestas($_POST);
        $this->guardarRespuestasEnBD($reg_m);
        // Calcular la paginación
        $paginacion = $this->calcularPaginacion($preguntas, $n_pag);
        // Si no hay más páginas, marcar la encuesta como finalizada
        if (is_null($paginacion['nextPag'])) {
            $this->marcarEncuestaComoFinalizada($reg_m);
            header('Location: gracias');
            exit;
        }
        // Redirigir al usuario a la siguiente página
        header("Location: ?n_pag={$paginacion['nextPag']}");
        exit;
    }
    // Calcular el progreso
    $totalPaginas = max(array_column($preguntas, 'n_pag'));
    $progreso = round(($n_pag / $totalPaginas) * 100);
    $_SESSION['current_page'] = $n_pag;
    // Calcular la paginación
    $paginacion = $this->calcularPaginacion($preguntas, $n_pag);
    return [
        'error' => false,
        'data' => [
            'preguntasEnPagina' => $preguntasEnPagina,
            'prevPag' => $paginacion['prevPag'],
            'nextPag' => $paginacion['nextPag'],
            'progreso' => $progreso,
        ],
        'view' => __DIR__ . '/../views/survey/cuestionario.php',
    ];
}

private function verificarEncuestaFinalizada(int $reg_m): bool
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT terminada FROM muestra WHERE reg_m = ?");
    $stmt->bindParam(1, $reg_m, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['terminada'] == 1;
}

private function marcarEncuestaComoFinalizada(int $reg_m): void
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE muestra SET terminada = 1 WHERE reg_m = ?");
    $stmt->bindParam(1, $reg_m, PDO::PARAM_INT);
    $stmt->execute();
}

   public function obtenerPreguntas(): array
{
    // Ruta al archivo de preguntas
    $archivo = __DIR__ . '/../models/Preguntas.json';

    // Verificar si el archivo existe
    if (!file_exists($archivo)) {
        error_log("El archivo de preguntas no existe.");
        return [];
    }

    // Leer el contenido del archivo JSON
    $json = file_get_contents($archivo);

    // Cargar las variables globales desde variables.php
    $variablesFile = __DIR__ . '/../models/variables.php';
    if (!file_exists($variablesFile)) {
        throw new Exception("El archivo de variables no existe.");
    }
    $variables = include $variablesFile;

    // Validar que las variables sean un array
    if (!is_array($variables)) {
        throw new Exception("Las variables no están definidas correctamente.");
    }

    // Reemplazar las variables globales en el contenido del JSON
    foreach ($variables as $key => $value) {
        $json = str_replace('$' . $key, $value, $json);
    }

    // Decodificar el JSON a un array asociativo
    $preguntas = json_decode($json, true);

    // Validar que el JSON sea válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar el archivo JSON: " . json_last_error_msg());
    }

    return $preguntas;
}

    
    private function guardarRespuestas(array $respuestas): void
    {
        foreach ($respuestas as $key => $respuesta) {
            $_SESSION['respuestas'][$key] = is_array($respuesta) ? implode(', ', $respuesta) : $respuesta;
        }
    }

    private function calcularPaginacion(array $preguntas, int $n_pag): array
    {
        // Obtener las respuestas actuales
        $respuestas = $_SESSION['respuestas'] ?? [];
    
        // Buscar la pregunta actual
        $preguntaActual = null;
        foreach ($preguntas as $pregunta) {
            if ($pregunta['n_pag'] === $n_pag) {
                $preguntaActual = $pregunta;
                break;
            }
        }
    
        // Calcular la página siguiente (nextPag)
        $nextPag = null;
    
        // Verificar reglas de visibilidad (filtro) para la siguiente página
        $siguientesPreguntas = array_filter($preguntas, fn($p) => $p['n_pag'] > $n_pag);
        foreach ($siguientesPreguntas as $pregunta) {
            if ($this->esPaginaVisible($pregunta, $respuestas)) {
                $nextPag = $pregunta['n_pag'];
                error_log("La página {$nextPag} cumple con las reglas de visibilidad.");
                break;
            }
        }
    
        // Si no hay página siguiente visible, calcular normalmente
        if (!$nextPag) {
            $nextPag = count(array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag + 1)) > 0 ? $n_pag + 1 : null;
            error_log("No hay página siguiente visible. Siguiente página calculada: {$nextPag}");
        }
    
        // Calcular la página anterior (prevPag)
        $prevPag = null;
        if ($n_pag > 1) {
            // Buscar la última página respondida antes de la página actual
            $ultimaPaginaRespondida = 1;
            foreach ($respuestas as $preguntaId => $valor) {
                foreach ($preguntas as $pregunta) {
                    if ($pregunta['id'] == $preguntaId && $pregunta['n_pag'] < $n_pag) {
                        if ($this->esPaginaVisible($pregunta, $respuestas)) {
                            if ($pregunta['n_pag'] > $ultimaPaginaRespondida) {
                                $ultimaPaginaRespondida = $pregunta['n_pag'];
                            }
                        }
                    }
                }
            }
            $prevPag = $ultimaPaginaRespondida;
            error_log("Página anterior calculada: {$prevPag}");
        }
    
        return [
            'prevPag' => $prevPag,
            'nextPag' => $nextPag,
        ];
    }
    
    /**
     * Función para verificar si una página es visible según sus reglas de filtro
     */
    private function esPaginaVisible(array $pregunta, array $respuestas): bool
    {
        if (!isset($pregunta['filtro'])) {
            return true; // Si no hay filtro, la página siempre es visible
        }
    
        foreach ($pregunta['filtro'] as $preguntaId => $respuestaRequerida) {
            if (!isset($respuestas[$preguntaId]) || !$this->cumpleCondicion($respuestas[$preguntaId], $respuestaRequerida)) {
                error_log("La página {$pregunta['n_pag']} no cumple con la condición '{$respuestaRequerida}' para la pregunta {$preguntaId}.");
                return false;
            }
        }
    
        return true;
    }
    
    /**
     * Función auxiliar para evaluar condiciones
     */
    private function cumpleCondicion(string $respuestaSeleccionada, string $condicion): bool
    {
        if (strpos($condicion, '-') !== false && strpos($condicion, '+') === false) {
            // Rango del tipo "X-Y"
            list($min, $max) = array_map('intval', explode('-', $condicion));
            $respuestaSeleccionada = (int)$respuestaSeleccionada;
            return $respuestaSeleccionada >= $min && $respuestaSeleccionada <= $max;
        } elseif (strpos($condicion, '+') !== false) {
            // Rango del tipo "X+"
            $min = (int)rtrim($condicion, '+');
            $respuestaSeleccionada = (int)$respuestaSeleccionada;
            return $respuestaSeleccionada >= $min;
        } elseif (strpos($condicion, '-') !== false && strpos($condicion, '+') === false) {
            // Rango del tipo "X-"
            $max = (int)rtrim($condicion, '-');
            $respuestaSeleccionada = (int)$respuestaSeleccionada;
            return $respuestaSeleccionada <= $max;
        } elseif (strpos($condicion, '!=') !== false) {
            // Condición del tipo "!= X"
            $valor = (int)trim(str_replace('!=', '', $condicion));
            $respuestaSeleccionada = (int)$respuestaSeleccionada;
            return $respuestaSeleccionada !== $valor;
        } else {
            // Valor único
            $valorUnico = (int)$condicion;
            $respuestaSeleccionada = (int)$respuestaSeleccionada;
            return $respuestaSeleccionada === $valorUnico;
        }
    }



    public function recuperarRespuestasDeBD($reg_m): array
    {
        global $pdo;

        // Consulta para obtener las respuestas del usuario basándose en la clave
        $stmt = $pdo->prepare("SELECT * FROM cuestionario WHERE reg_m = ?");
        $stmt->bindParam(1, $reg_m, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $respuestas = [];
        if ($result) {
            foreach ($result as $columna => $valor) {
                if (strpos($columna, 'r') === 0) { // Solo columnas rX
                    $preguntaId = substr($columna, 1); // Quitar el prefijo 'r'
                    $respuestas[$preguntaId] = $valor;
                    error_log("Respuesta recuperada: Pregunta $preguntaId, Valor $valor");
                }
            }
        } else {
            error_log("No se encontraron respuestas en la base de datos para la clave $reg_m");
        }

        return $respuestas;
    }


 

    public function calcularPaginaActual(array $respuestas): int
    {
        $preguntas = $this->obtenerPreguntas();
    
        if (empty($respuestas)) {
            error_log("No hay respuestas, redirigiendo a la página 1.");
            return 1; // Si no hay respuestas, redirige a la primera página
        }
    
        // Inicializar la página por defecto
        $ultimaPagina = 1;
    
        // Buscar la última pregunta respondida y su página
        foreach ($respuestas as $preguntaId => $valor) {
            foreach ($preguntas as $pregunta) {
                if ($pregunta['id'] == $preguntaId) {
                    if ($pregunta['n_pag'] > $ultimaPagina) {
                        $ultimaPagina = $pregunta['n_pag'];
                    }
                    error_log("Pregunta encontrada. ID: {$pregunta['id']}, Página: {$pregunta['n_pag']}");
                }
            }
        }
    
        error_log("Última página calculada: {$ultimaPagina}");
        return $ultimaPagina; // Retornar la página correspondiente a la última pregunta respondida
    }
    public function guardarRespuestasEnBD(): void
{
    global $pdo;
    if (empty($_SESSION['respuestas'])) {
        return;
    }
    try {
        // Obtener reg_m y clave de la sesión
        $reg_m = $_SESSION['reg_m'] ?? null;
        $clave = $_SESSION['clave'] ?? null;
        if (!$reg_m || !$clave) {
            throw new Exception("Error: El identificador de usuario (reg_m o clave) no está definido.");
        }
        $fecha = date('Y-m-d H:i:s');
        // Construir la consulta SQL dinámicamente
        $columns = ['reg_m', 'clave', 'date_logout'];
        $values = [':reg_m' => $reg_m, ':clave' => $clave, ':date_logout' => $fecha];
        $updates = ['date_logout = VALUES(date_logout)', 'clave = VALUES(clave)'];
        foreach ($_SESSION['respuestas'] as $preguntaId => $respuesta) {
            $columna = "r$preguntaId";
            $columns[] = $columna;
            $values[":$columna"] = $respuesta;
            $updates[] = "$columna = VALUES($columna)";
        }
        $columnsSQL = implode(', ', $columns);
        $placeholdersSQL = implode(', ', array_keys($values));
        $updatesSQL = implode(', ', $updates);
        $query = "
            INSERT INTO cuestionario ($columnsSQL)
            VALUES ($placeholdersSQL)
            ON DUPLICATE KEY UPDATE $updatesSQL
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);
        error_log("Respuestas guardadas correctamente en la base de datos.");
    } catch (Exception $e) {
        error_log("Error al guardar las respuestas en la base de datos: " . $e->getMessage());
    }
}




}
?>
