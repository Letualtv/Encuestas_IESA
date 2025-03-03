<?php
require '../../config/db.php'; // Archivo de conexión a la base de datos

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Verificar conexión
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error en la conexión: " . $e->getMessage()]);
    exit;
}

// Obtener todos los usuarios paginados
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    try {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 30;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT ID, Nombre, Email, Pass, Rol FROM usuariosCP ORDER BY ID LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($usuarios)) {
            echo json_encode(["success" => false, "message" => "No se encontraron usuarios."]);
        } else {
            echo json_encode(["success" => true, "data" => $usuarios]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Obtener un usuario específico por ID
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtener') {
    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            echo json_encode(["success" => false, "message" => "ID de usuario no proporcionado."]);
            exit;
        }

        $sql = "SELECT ID, Nombre, Email, Pass, Rol FROM usuariosCP WHERE ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo json_encode(["success" => true, "data" => $usuario]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró el usuario especificado."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Editar un usuario
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar') {
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';

        if (!$id || !$nombre || !$email) {
            echo json_encode(["success" => false, "message" => "Datos incompletos."]);
            exit;
        }

        // Actualizar solo los campos proporcionados
        $sql = "UPDATE usuariosCP SET Nombre = ?, Email = ?";
        $params = [$nombre, $email];

        if (!empty($pass)) {
            $sql .= ", Pass = ?";
            $params[] = $pass; // Contraseña en texto plano
        }

        $sql .= " WHERE ID = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo actualizar el usuario."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Eliminar un usuario
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            echo json_encode(["success" => false, "message" => "ID de usuario no proporcionado."]);
            exit;
        }

        $sql = "DELETE FROM usuariosCP WHERE ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo eliminar el usuario."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $e->getMessage()]);
    }
}

// Acción no válida
else {
    error_log("Acción no válida: Método: " . $_SERVER['REQUEST_METHOD'] . " Acción: " . (isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : "N/A")));
    echo json_encode(["success" => false, "message" => "Acción no válida."]);
}
?>
