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

// Procesar solicitud de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($email) || empty($password)) {
            echo json_encode(["success" => false, "message" => "Email y contraseña son obligatorios."]);
            exit;
        }

        // Consultar el usuario en la base de datos
        $sql = "SELECT ID, Nombre, Email, Pass, Rol FROM usuariosCP WHERE Email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo json_encode(["success" => false, "message" => "Credenciales incorrectas."]);
            exit;
        }

        // Comparar la contraseña en texto plano
        if ($password !== $usuario['Pass']) {
            echo json_encode(["success" => false, "message" => "Credenciales incorrectas."]);
            exit;
        }

        // Inicio de sesión exitoso
        session_start();
        $_SESSION['usuario'] = [
            'id' => $usuario['ID'],
            'nombre' => $usuario['Nombre'],
            'email' => $usuario['Email'],
            'rol' => $usuario['Rol']
        ];

        echo json_encode([
            "success" => true,
            "message" => "Inicio de sesión exitoso.",
            "data" => [
                "id" => $usuario['ID'],
                "nombre" => $usuario['Nombre'],
                "email" => $usuario['Email'],
                "rol" => $usuario['Rol']
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la consulta: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método HTTP no válido."]);
}
?>