<?php
header("Content-Type: application/json");
require '../../config/db.php'; // Archivo de conexión a la base de datos


// Función para validar una clave
function validarClave($clave) {
    return preg_match('/^[a-zA-Z0-9]{5}$/', $clave);
}

// Obtener la acción solicitada
$action = $_GET['action'] ?? null;

// Manejar las acciones
switch ($action) {
    case 'obtenerClaves':
        obtenerClaves($pdo);
        break;

    case 'agregarClave':
        agregarClave($pdo);
        break;

    case 'editarClave':
        editarClave($pdo);
        break;

    case 'eliminarClavesSeleccionadas':
        eliminarClavesSeleccionadas($pdo);
        break;

    case 'generarClavesAleatorias':
        generarClavesAleatorias($pdo);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
        break;
}

// Función para obtener claves
function obtenerClaves($pdo) {
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 20);
    $orderBy = $_GET['orderBy'] ?? 'IDGrupo';
    $orderDir = ($_GET['orderDir'] ?? 'asc') === 'asc' ? 'ASC' : 'DESC';

    try {
        $sql = "SELECT IDGrupo, clave FROM claves ORDER BY $orderBy $orderDir LIMIT :limit OFFSET :offset";
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

// Función para agregar una clave

// Función para agregar una clave
function agregarClave($pdo) {
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

// Función para editar claves
function editarClave($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(["success" => false, "message" => "No se proporcionaron claves para editar."]);
        return;
    }

    try {
        if ($ids === 'all') {
            // Editar todas las claves
            $sqlUpdate = "UPDATE claves SET terminada = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([1]); // Marcar todas como completadas
        } else {
            // Editar claves específicas
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sqlUpdate = "UPDATE claves SET terminada = ? WHERE IDGrupo IN ($placeholders)";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $params = array_merge([1], $ids); // Marcar como completadas
            $stmtUpdate->execute($params);
        }

        echo json_encode(["success" => true, "message" => "Estado de claves actualizado correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar las claves: " . $e->getMessage()]);
    }
}

// Función para eliminar claves seleccionadas
function eliminarClavesSeleccionadas($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(["success" => false, "message" => "No se proporcionaron claves para eliminar."]);
        return;
    }

    try {
        if ($ids === 'all') {
            // Eliminar todas las claves
            $sqlDeleteClaves = "DELETE FROM claves";
            $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
            $stmtDeleteClaves->execute();
        } else {
            // Eliminar claves específicas
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sqlDeleteClaves = "DELETE FROM claves WHERE IDGrupo IN ($placeholders)";
            $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
            $stmtDeleteClaves->execute($ids);
        }

        echo json_encode(["success" => true, "message" => "Claves eliminadas correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
    }
}

// Función para generar claves aleatorias
function generarClavesAleatorias($pdo) {
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

        for ($i = 0; $i < $cantidad; $i++) {
            $attempts = 0;
            $maxAttempts = 100;

            while ($attempts < $maxAttempts) {
                $clave = substr(str_shuffle("abcdefghjklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"), 0, 5);

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
                    // Calcular el siguiente IDGrupo
                    $sqlGetLastId = "SELECT MAX(IDGrupo) FROM claves";
                    $stmtGetLastId = $pdo->prepare($sqlGetLastId);
                    $stmtGetLastId->execute();
                    $lastId = $stmtGetLastId->fetchColumn();
                    $nextId = ($idBase > 0) ? $idBase + $i : (($lastId === null) ? 1 : $lastId + 1);

                    // Insertar la nueva clave
                    $sqlInsert = "INSERT INTO claves (IDGrupo, clave) VALUES (?, ?)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->execute([$nextId, $clave]);

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
