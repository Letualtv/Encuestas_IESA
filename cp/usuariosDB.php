<?php
// Conexión a la base de datos
$host = 'PMYSQL187.dns-servicio.com:3306'; // o la IP del servidor MySQL
$dbname = '10796594_encuestas_IESA'; // Nombre de tu base de datos
$username = 'AP_admin'; // Tu usuario de base de datos
$password = 'L37u4l*11'; // Tu contraseña de base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener todos los usuarios
$sql = "SELECT ID, Nombre, Email, Pass, Rol FROM usuariosCP";
$result = $conn->query($sql);

// Separar administradores y usuarios
$administradores = [];
$usuarios = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Rol'] === 'administrador') {
            $administradores[] = $row;
        } else {
            $usuarios[] = $row;
        }
    }
} else {
    echo "No hay usuarios registrados.";
}

$conn->close();
?>