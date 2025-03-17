<?php
require '../../config/db.php'; // Archivo de conexión a la base de datos

// Obtener claves paginadas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtenerClaves') {
// Validar y sanitizar las variables de ordenación
$allowedColumns = ['id', 'clave', 'terminada', 'n_login'];
$orderBy = in_array($_GET['orderBy'], $allowedColumns) ? $_GET['orderBy'] : 'id';
$orderDir = strtoupper($_GET['orderDir']) === 'DESC' ? 'DESC' : 'ASC';

// Validar y sanitizar los valores de paginación
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 30;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    // Construir la consulta SQL
    $sql = "SELECT 
            claves.id, 
            claves.clave, 
            muestra.terminada, 
            muestra.n_login 
        FROM 
            claves 
        LEFT JOIN 
            muestra 
        ON 
            claves.clave = muestra.clave 
        ORDER BY 
            $orderBy $orderDir 
        LIMIT 
            ? OFFSET ?
    ";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los resultados
    $claves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados como JSON
    echo json_encode($claves);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
}



// Eliminar TODAS las claves
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'eliminarTodasLasClaves') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? null;

    if ($ids === "all") {
        try {
            $pdo->beginTransaction();

            // Eliminar registros relacionados en la tabla muestra
            $sqlDeleteMuestra = "DELETE FROM muestra";
            $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
            $stmtDeleteMuestra->execute();

            // Eliminar todas las claves de la tabla claves
            $sqlDeleteClaves = "DELETE FROM claves";
            $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
            $stmtDeleteClaves->execute();

            $pdo->commit();

            echo json_encode(["success" => true, "message" => "TODAS las claves han sido eliminadas correctamente."]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID inválido."]);
    }
}

// Marcar TODAS las claves como terminadas/no terminadas
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'editarTodasLasClaves') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? null;
    $terminada = $data['terminada'] ?? null;

    if ($ids === "all" && in_array($terminada, [0, 1])) {
        try {
            $sql = "UPDATE muestra SET terminada = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$terminada]);

            echo json_encode(["success" => true, "message" => "TODAS las claves han sido actualizadas correctamente."]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error al actualizar las claves: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Datos inválidos."]);
    }
}

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
        $sqlInsert = "INSERT INTO claves (clave) VALUES (?)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$clave]);

        echo json_encode(["success" => true, "message" => "Clave agregada correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al agregar la clave: " . $e->getMessage()]);
    }
}
// Agregar claves aleatorias automáticamente
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'generarClavesAleatorias') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cantidad = intval($data['cantidad'] ?? 0);

    // Validar que la cantidad sea válida
    if ($cantidad <= 0 || $cantidad > 10000) {
        echo json_encode(["success" => false, "message" => "La cantidad debe estar entre 1 y 10,000."]);
        exit;
    }

    // Lista de patrones prohibidos (palabras o secuencias indeseadas)
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

    try {
        $clavesGeneradas = [];
        $maxAttempts = 100; // Límite de intentos por clave

        for ($i = 0; $i < $cantidad;) {
            $attempts = 0;

            while ($attempts < $maxAttempts) {
                // Generar una clave aleatoria de 5 caracteres
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
                    // Insertar la clave
                    $sqlInsert = "INSERT INTO claves (clave) VALUES (?)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->execute([$clave]);

                    $clavesGeneradas[] = $clave;
                    $i++;
                    break;
                }

                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                echo json_encode(["success" => false, "message" => "No se pudieron generar todas las claves debido a demasiados intentos fallidos."]);
                exit;
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

// Obtener todas las claves (sin paginación)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtenerTodasClaves') {
    try {
        $sql = "SELECT id, clave FROM claves ORDER BY id";
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


// Eliminar una clave individual
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'eliminarClavesSeleccionadas') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(["success" => false, "message" => "No se proporcionaron IDs válidos."]);
        exit;
    }

    try {
        // Validar que los IDs sean números enteros
        $ids = array_map('intval', $ids);

        // Crear placeholders para los IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $pdo->beginTransaction();

        // Eliminar registros relacionados en la tabla muestra (solo si existen)
        $sqlDeleteMuestra = "DELETE FROM muestra WHERE clave IN ($placeholders)";
        $stmtDeleteMuestra = $pdo->prepare($sqlDeleteMuestra);
        $stmtDeleteMuestra->execute($ids);

        // Eliminar las claves de la tabla claves
        $sqlDeleteClaves = "DELETE FROM claves WHERE id IN ($placeholders)";
        $stmtDeleteClaves = $pdo->prepare($sqlDeleteClaves);
        $stmtDeleteClaves->execute($ids);

        // Verificar si se eliminaron registros
        $rowCountClaves = $stmtDeleteClaves->rowCount();
        if ($rowCountClaves > 0) {
            $pdo->commit();
            echo json_encode(["success" => true, "message" => "Claves eliminadas correctamente."]);
        } else {
            $pdo->rollBack();
            echo json_encode(["success" => false, "message" => "No se encontraron claves para eliminar."]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
    }
}

// Editar el estado "terminada" de varias claves seleccionadas
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'editarClave') {
    $data = json_decode(file_get_contents('php://input'), true);
    $claves = $data['ids'] ?? []; // Cambio: ahora son claves, no IDs
    $terminada = $data['terminada'] ?? null;

    // Validar datos
    if (empty($claves) || !in_array($terminada, [0, 1])) {
        echo json_encode(["success" => false, "message" => "Datos incompletos o inválidos."]);
        exit;
    }

    try {
        // Crear placeholders dinámicos para las claves
        $placeholders = implode(',', array_fill(0, count($claves), '?'));

        // Actualizar el estado "terminada" en la tabla muestra
        $sqlUpdate = "UPDATE muestra SET terminada = ? WHERE clave IN ($placeholders)";
        $stmtUpdate = $pdo->prepare($sqlUpdate);

        // Agregar el valor de "terminada" al inicio del array de parámetros
        $params = array_merge([$terminada], $claves);
        $stmtUpdate->execute($params);

        // Verificar si se actualizaron registros
        if ($stmtUpdate->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Estado de claves actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontraron claves para actualizar."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar las claves: " . $e->getMessage()]);
    }
}