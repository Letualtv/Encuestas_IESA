<?php
require '../../config/db.php'; // Archivo de conexión a la base de datos

// Obtener claves paginadas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtenerClaves') {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 30;
    $offset = ($page - 1) * $limit;

    try {
        $sql = "SELECT id, clave, terminada FROM claves ORDER BY id LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $claves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($claves);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Eliminar una clave individual
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'eliminarClave') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de clave no proporcionado."]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Obtener la clave correspondiente al ID
        $sqlGetClave = "SELECT clave FROM claves WHERE id = ?";
        $stmtGetClave = $pdo->prepare($sqlGetClave);
        $stmtGetClave->execute([$id]);
        $clave = $stmtGetClave->fetchColumn();

        if (!$clave) {
            echo json_encode(["success" => false, "message" => "No se encontró la clave especificada."]);
            exit;
        }

        // Eliminar registros relacionados en la tabla muestra
        $sqlDeleteMuestra = "DELETE FROM muestra WHERE clave = ?";
        $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
        $stmtDeleteMuestra->execute([$clave]);

        // Eliminar la clave de la tabla claves
        $sqlDeleteClave = "DELETE FROM claves WHERE id = ?";
        $stmtDeleteClave = $pdo->prepare($sqlDeleteClave);
        $stmtDeleteClave->execute([$id]);

        $pdo->commit();

        if ($stmtDeleteClave->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Clave eliminada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró la clave especificada."]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar la clave: " . $e->getMessage()]);
    }
}

// Agregar una nueva clave manualmente
// Agregar una nueva clave manualmente
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'agregarClave') {
    $data = json_decode(file_get_contents('php://input'), true);
    $clave = trim($data['clave'] ?? '');

    // Validar que la clave tenga exactamente 5 caracteres y sea alfanumérica
    if (!preg_match('/^[a-zA-Z0-9]{5}$/', $clave)) {
        echo json_encode(["success" => false, "message" => "La clave debe tener exactamente 5 caracteres alfanuméricos."]);
        exit;
    }

    try {
        // Verificar si la clave ya existe
        $sqlCheck = "SELECT COUNT(*) FROM claves WHERE clave = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$clave]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists) {
            echo json_encode(["success" => false, "message" => "La clave ya existe."]);
            exit;
        }

        // Insertar la nueva clave con terminada = 0
        $sqlInsert = "INSERT INTO claves (clave, terminada) VALUES (?, 0)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$clave]);

        echo json_encode(["success" => true, "message" => "Clave agregada correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al agregar la clave: " . $e->getMessage()]);
    }
}
// Generar claves aleatorias
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'generarClavesAleatorias') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cantidad = intval($data['cantidad'] ?? 0);

    // Validar que la cantidad sea válida
    if ($cantidad <= 0 || $cantidad > 10000) {
        echo json_encode(["success" => false, "message" => "La cantidad debe estar entre 1 y 10,000."]);
        exit;
    }

    try {
        $clavesGeneradas = [];
        for ($i = 0; $i < $cantidad; $i++) {
            // Generar una clave aleatoria de 5 caracteres alfanuméricos
            $clave = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5);

            // Verificar si la clave ya existe
            $sqlCheck = "SELECT COUNT(*) FROM claves WHERE clave = ?";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([$clave]);
            $exists = $stmtCheck->fetchColumn();

            if (!$exists) {
                // Insertar la clave con terminada = 0
                $sqlInsert = "INSERT INTO claves (clave, terminada) VALUES (?, 0)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([$clave]);

                $clavesGeneradas[] = $clave;
            } else {
                // Si la clave ya existe, intentar generar otra
                $i--;
            }
        }

        echo json_encode([
            "success" => true,
            "message" => "Se generaron " . count($clavesGeneradas) . " claves correctamente.",
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al generar las claves: " . $e->getMessage()]);
    }
}

// Obtener todas las claves (sin paginación)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtenerTodasClaves') {
    try {
        $sql = "SELECT id, clave, terminada FROM claves ORDER BY id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $claves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($claves);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Eliminar varias claves seleccionadas
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'eliminarClavesSeleccionadas') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(["success" => false, "message" => "No se proporcionaron IDs de claves."]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Obtener las claves correspondientes a los IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sqlGetClaves = "SELECT clave FROM claves WHERE id IN ($placeholders)";
        $stmtGetClaves = $pdo->prepare($sqlGetClaves);
        $stmtGetClaves->execute($ids);
        $claves = $stmtGetClaves->fetchAll(PDO::FETCH_COLUMN);

        if (empty($claves)) {
            echo json_encode(["success" => false, "message" => "No se encontraron las claves especificadas."]);
            exit;
        }

        // Eliminar registros relacionados en la tabla muestra
        $placeholdersMuestra = implode(',', array_fill(0, count($claves), '?'));
        $sqlDeleteMuestra = "DELETE FROM muestra WHERE clave IN ($placeholdersMuestra)";
        $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
        $stmtDeleteMuestra->execute($claves);

        // Eliminar las claves de la tabla claves
        $sqlDeleteClaves = "DELETE FROM claves WHERE id IN ($placeholders)";
        $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
        $stmtDeleteClaves->execute($ids);

        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Claves eliminadas correctamente."]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
    }
}

// Editar el estado "terminada" de varias claves seleccionadas
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'editarClave') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];
    $terminada = $data['terminada'] ?? null;

    // Validar que los datos sean correctos
    if (empty($ids) || !in_array($terminada, [0, 1])) {
        echo json_encode(["success" => false, "message" => "Datos incompletos o inválidos."]);
        exit;
    }

    try {
        // Crear placeholders para los IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE claves SET terminada = ? WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);

        // Agregar el valor de "terminada" al inicio del array de parámetros
        $params = array_merge([$terminada], $ids);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Estado de claves actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontraron claves para actualizar."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar las claves: " . $e->getMessage()]);
    }
}

