<?php
require '../../config/db.php'; // Archivo de conexión a la base de datos

// Obtener claves paginadas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtenerClaves') {
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

// Eliminar una clave específica
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['accion']) && $_GET['accion'] === 'eliminarClave') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de clave no proporcionado."]);
        exit;
    }

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Paso 1: Eliminar las filas relacionadas en la tabla cuestionario
        $sqlDeleteCuestionario = "DELETE FROM cuestionario WHERE clave = (SELECT clave FROM claves WHERE id = ?)";
        $stmtDeleteCuestionario = $pdo->prepare($sqlDeleteCuestionario);
        $stmtDeleteCuestionario->execute([$id]);

        // Paso 2: Eliminar la clave de la tabla claves
        $sqlDeleteClave = "DELETE FROM claves WHERE id = ?";
        $stmtDeleteClave = $pdo->prepare($sqlDeleteClave);
        $stmtDeleteClave->execute([$id]);

        // Confirmar transacción
        $pdo->commit();

        if ($stmtDeleteClave->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Clave eliminada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró la clave especificada."]);
        }
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error al eliminar la clave: " . $e->getMessage()]);
    }
}

// Eliminar todas las claves
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['accion']) && $_GET['accion'] === 'eliminarTodasLasClaves') {
    try {
        $sql = "DELETE FROM claves";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Todas las claves han sido eliminadas correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al eliminar las claves: " . $e->getMessage()]);
    }
}

// Eliminar una clave específica
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['accion']) && $_GET['accion'] === 'eliminarClave') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de clave no proporcionado."]);
        exit;
    }

    try {
        // Llamar a la función SQL para eliminar la clave
        $sql = "SELECT actualizar_o_eliminar_clave(:id, NULL, 'eliminar') AS resultado";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "message" => $resultado['resultado']]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al eliminar la clave: " . $e->getMessage()]);
    }

}

// Buscar claves por ID o clave
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'buscarClaves') {
    $searchTerm = isset($_GET['termino']) ? trim($_GET['termino']) : '';

    if (empty($searchTerm)) {
        echo json_encode(["error" => "El término de búsqueda no puede estar vacío."]);
        exit;
    }

    try {
        $sql = "SELECT id, clave, terminada FROM claves WHERE id = :id OR clave LIKE :clave";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', intval($searchTerm), PDO::PARAM_INT);
        $stmt->bindValue(':clave', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $claves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($claves);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    }
}
?>