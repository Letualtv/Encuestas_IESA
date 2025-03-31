<?php
header("Content-Type: application/json");
require '../../config/db.php'; // Archivo de conexión a la base de datos



// Obtener la acción solicitada
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'obtenerClaves':
        obtenerClaves($pdo);
        break;

    case 'agregarClave':
        agregarClave($pdo);
        break;

    case 'eliminarTodasLasClaves':
        eliminarTodasLasClaves($pdo);
        break;

    case 'editarTodasLasClaves':
        editarTodasLasClaves($pdo);
        break;
    case 'eliminarClavesSeleccionadas':
        eliminarClavesSeleccionadas($pdo);
        break;

    case 'generarClavesAleatorias':
        generarClavesAleatorias($pdo);
        break;
    case 'marcarClavesSeleccionadas':
        marcarClavesSeleccionadas($pdo);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
        break;
}





    // Función para marcar claves seleccionadas como terminadas o no terminadas
    function marcarClavesSeleccionadas($pdo)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $claves = $data['ids'] ?? [];
        $terminada = intval($data['terminada'] ?? null);

        if (empty($claves)) {
            echo json_encode(["success" => false, "message" => "No se proporcionaron claves para modificar."]);
            return;
        }

        if (!in_array($terminada, [0, 1])) {
            echo json_encode(["success" => false, "message" => "Estado inválido."]);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($claves), '?'));
            $sqlUpdate = "UPDATE muestra SET terminada = ? WHERE clave IN ($placeholders)";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute(array_merge([$terminada], $claves));

            echo json_encode(["success" => true, "message" => "Las claves seleccionadas han sido actualizadas correctamente."]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error al modificar las claves seleccionadas: " . $e->getMessage()]);
        }
    }


// Función para obtener claves
function obtenerClaves($pdo)
{
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 20);
    $orderBy = $_GET['orderBy'] ?? 'IDGrupo';
    $orderDir = ($_GET['orderDir'] ?? 'asc') === 'asc' ? 'ASC' : 'DESC';

    try {
        $sql = "SELECT claves.IDGrupo, claves.clave, muestra.terminada, muestra.n_login 
            FROM claves 
            LEFT JOIN muestra ON claves.clave = muestra.clave 
            ORDER BY $orderBy $orderDir 
            LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
        $stmt->execute();
        $claves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($claves); // Devuelve directamente el array
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al obtener las claves: " . $e->getMessage()]);
    }
}

// Función para eliminar claves seleccionadas
function eliminarClavesSeleccionadas($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $claves = $data['ids'] ?? [];

    if (empty($claves)) {
        echo json_encode(["success" => false, "message" => "No se proporcionaron claves para eliminar."]);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Paso 1: Eliminar registros relacionados en la tabla cuestionario
        $placeholders = implode(',', array_fill(0, count($claves), '?'));
        $sqlDeleteCuestionario = "DELETE FROM cuestionario WHERE clave IN ($placeholders)";
        $stmtDeleteCuestionario = $pdo->prepare($sqlDeleteCuestionario);
        $stmtDeleteCuestionario->execute($claves);

        // Paso 2: Eliminar registros relacionados en la tabla muestra
        $sqlDeleteMuestra = "DELETE FROM muestra WHERE clave IN ($placeholders)";
        $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
        $stmtDeleteMuestra->execute($claves);

        // Paso 3: Eliminar las claves seleccionadas de la tabla claves
        $sqlDeleteClaves = "DELETE FROM claves WHERE clave IN ($placeholders)";
        $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
        $stmtDeleteClaves->execute($claves);

        $pdo->commit();
        echo json_encode(["success" => true, "message" => "Las claves seleccionadas han sido eliminadas correctamente."]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves seleccionadas: " . $e->getMessage()]);
    }
}
// Función para agregar una clave
function agregarClave($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $clave = trim($data['clave'] ?? '');
    $idBase = intval($data['idBase'] ?? 0);

    // Validar que la clave tenga exactamente 5 caracteres alfanuméricos
    if (!preg_match('/^[a-zA-Z0-9]{5}$/', $clave)) {
        echo json_encode(["success" => false, "message" => "La clave debe tener exactamente 5 caracteres alfanuméricos."]);
        return;
    }

    try {
        // Verificar si la clave ya existe
        $sqlCheck = "SELECT COUNT(*) FROM claves WHERE clave = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$clave]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists) {
            echo json_encode(["success" => false, "message" => "La clave ya existe."]);
            return;
        }

        // Calcular el siguiente IDGrupo
        $sqlGetLastId = "SELECT MAX(IDGrupo) FROM claves";
        $stmtGetLastId = $pdo->prepare($sqlGetLastId);
        $stmtGetLastId->execute();
        $lastId = $stmtGetLastId->fetchColumn();
        $nextId = ($idBase > 0) ? $idBase : (($lastId === null) ? 1 : $lastId + 1);

        // Insertar la nueva clave
        $sqlInsert = "INSERT INTO claves (IDGrupo, clave) VALUES (?, ?)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$nextId, $clave]);

        echo json_encode(["success" => true, "message" => "Clave agregada correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al agregar la clave: " . $e->getMessage()]);
    }
}

// Función para eliminar TODAS las claves
function eliminarTodasLasClaves($pdo)
{
    try {
        $pdo->beginTransaction();

        // Paso 1: Eliminar registros de la tabla cuestionario
        $sqlDeleteCuestionario = "DELETE FROM cuestionario";
        $stmtDeleteCuestionario = $pdo->prepare($sqlDeleteCuestionario);
        $stmtDeleteCuestionario->execute();

        // Paso 2: Eliminar registros de la tabla muestra
        $sqlDeleteMuestra = "DELETE FROM muestra";
        $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
        $stmtDeleteMuestra->execute();

        // Paso 3: Eliminar todas las claves de la tabla claves
        $sqlDeleteClaves = "DELETE FROM claves";
        $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
        $stmtDeleteClaves->execute();

        $pdo->commit();
        echo json_encode(["success" => true, "message" => "TODAS las claves han sido eliminadas correctamente."]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
    }
}

// Función para marcar TODAS las claves como terminadas/no terminadas
function editarTodasLasClaves($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $terminada = intval($data['terminada'] ?? null);

    if (!in_array($terminada, [0, 1])) {
        echo json_encode(["success" => false, "message" => "Estado inválido."]);
        return;
    }

    try {
        // Actualizar el estado "terminada" en la tabla muestra
        $sqlUpdateMuestra = "UPDATE muestra SET terminada = ?";
        $stmtUpdateMuestra = $pdo->prepare($sqlUpdateMuestra);
        $stmtUpdateMuestra->execute([$terminada]);

        // Actualizar el estado "terminada" en la tabla claves
        $sqlUpdateClaves = "UPDATE claves SET terminada = ?";
        $stmtUpdateClaves = $pdo->prepare($sqlUpdateClaves);
        $stmtUpdateClaves->execute([$terminada]);

        echo json_encode(["success" => true, "message" => "Todas las claves han sido marcadas correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar las claves: " . $e->getMessage()]);
    }
}

// Función para generar claves aleatorias
function generarClavesAleatorias($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $cantidad = intval($data['cantidad'] ?? 0);
    $idBase = intval($data['idBase'] ?? 0);

    // Validar que la cantidad sea válida
    if ($cantidad <= 0 || $cantidad > 10000) {
        echo json_encode(["success" => false, "message" => "La cantidad debe estar entre 1 y 10,000."]);
        return;
    }

    try {
        $clavesGeneradas = [];
        $blacklistPatterns = [
            '/pipi/i',
            '/caca/i',
            '/kaka/i',
            '/nazi/i',
            '/vox/i',
            '/tonto/i',
            '/malo/i',
            '/psoe/i',
            '/sumar/i',
            '/milf/i',
        ];

        // Obtener el último IDGrupo si no se especifica un idBase
        $lastId = null;
        if ($idBase <= 0) {
            $sqlGetLastId = "SELECT MAX(IDGrupo) FROM claves";
            $stmtGetLastId = $pdo->prepare($sqlGetLastId);
            $stmtGetLastId->execute();
            $lastId = $stmtGetLastId->fetchColumn();
        }

        for ($i = 0; $i < $cantidad; $i++) {
            $attempts = 0;
            $maxAttempts = 100;

            while ($attempts < $maxAttempts) {
                // Generar una clave aleatoria de 5 caracteres alfanuméricos
                $clave = substr(str_shuffle("abcdefghjklmnpqrstuvwxyz"), 0, 5);

                // Verificar si la clave ya existe
                $sqlCheck = "SELECT COUNT(*) FROM claves WHERE clave = ?";
                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->execute([$clave]);
                $exists = $stmtCheck->fetchColumn();

                // Verificar si la clave coincide con alguno de los patrones prohibidos
                $isBlacklisted = false;
                foreach ($blacklistPatterns as $pattern) {
                    if (preg_match($pattern, $clave)) {
                        $isBlacklisted = true;
                        break;
                    }
                }

                if (!$exists && !$isBlacklisted) {
                    // Determinar el IDGrupo a usar
                    $idGrupo = ($idBase > 0) ? $idBase : (($lastId === null) ? 1 : $lastId + 1);

                    // Insertar la nueva clave con el IDGrupo determinado
                    $sqlInsert = "INSERT INTO claves (IDGrupo, clave) VALUES (?, ?)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->execute([$idGrupo, $clave]);

                    // Incrementar el IDGrupo si no se especificó un idBase
                    if ($idBase <= 0) {
                        $lastId++;
                    }

                    $clavesGeneradas[] = $clave;
                    break;
                }

                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudieron generar todas las claves debido a demasiados intentos fallidos."
                ]);
                return;
            }
        }

        echo json_encode([
            "success" => true,
            "message" => "Se generaron " . count($clavesGeneradas) . " claves correctamente.",
            "claves" => $clavesGeneradas,
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al generar las claves: " . $e->getMessage()]);
    }
}
