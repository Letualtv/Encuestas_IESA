<?php
// Configuración inicial
header("Content-Type: application/json");

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en la salida
ini_set('log_errors', 1);     // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/php-error.log'); // Ruta del archivo de logs

// Carpeta donde se almacenan las imágenes
$directorioImagenes = "../../assets/img/";

// Verificar si el directorio existe
if (!is_dir($directorioImagenes)) {
    echo json_encode(["error" => "El directorio de imágenes no existe."]);
    exit;
}

// Determinar la acción solicitada
$action = $_SERVER['REQUEST_METHOD'];
switch ($action) {
    case 'GET':
        obtenerImagenes();
        break;

    case 'POST':
        if (isset($_FILES['imagen'])) {
            subirImagen();
        } elseif (isset($_POST['id']) && isset($_POST['nuevoNombre'])) {
            editarNombreImagen();
        }
        break;

    case 'DELETE':
        borrarImagen();
        break;

    default:
        echo json_encode(["error" => "Método HTTP no permitido."]);
        break;
}

// Función para obtener la lista de imágenes
function obtenerImagenes() {
    global $directorioImagenes;

    // Obtener la lista de archivos en el directorio
    $archivos = scandir($directorioImagenes);

    // Filtrar solo los archivos válidos (ignorar "." y "..") y asegurarse de que sean imágenes
    $extensionesValidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imagenes = array_filter($archivos, function ($archivo) use ($directorioImagenes, $extensionesValidas) {
        $rutaCompleta = $directorioImagenes . DIRECTORY_SEPARATOR . $archivo;
        if (!is_file($rutaCompleta)) {
            return false;
        }
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        return in_array($extension, $extensionesValidas);
    });

    // Formatear la información de las imágenes
    $listaImagenes = [];
    foreach ($imagenes as $imagen) {
        $rutaCompleta = $directorioImagenes . DIRECTORY_SEPARATOR . $imagen;
        $listaImagenes[] = [
            "id" => uniqid(), // Generar un ID único para cada imagen
            "nombre" => $imagen,
            "tamaño" => round(filesize($rutaCompleta) / 1024, 2), // Tamaño en KB
        ];
    }

    // Devolver la lista de imágenes en formato JSON
    echo json_encode($listaImagenes);
}

// Función para subir una nueva imagen
function subirImagen() {
    global $directorioImagenes;

    // Validar que se haya enviado un archivo
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "Error al subir la imagen."]);
        return;
    }

    $archivo = $_FILES['imagen'];
    $nombreArchivo = basename($archivo['name']);
    $rutaDestino = $directorioImagenes . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Mover el archivo al directorio de imágenes
    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo json_encode(["success" => true, "message" => "Imagen subida correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar la imagen."]);
    }
}

// Función para borrar una imagen
function borrarImagen() {
    global $directorioImagenes;

    // Obtener el ID de la imagen desde la URL
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de imagen no proporcionado."]);
        return;
    }

    // Buscar la imagen por su nombre (ID)
    $nombreArchivo = urldecode($id);
    $rutaCompleta = $directorioImagenes . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Verificar si el archivo existe y eliminarlo
    if (file_exists($rutaCompleta)) {
        if (unlink($rutaCompleta)) {
            echo json_encode(["success" => true, "message" => "Imagen eliminada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar la imagen."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "La imagen no existe."]);
    }
}

// Función para editar el nombre de una imagen
function editarNombreImagen() {
    global $directorioImagenes;

    // Obtener los datos enviados por POST
    $id = $_POST['id'] ?? null;
    $nuevoNombre = $_POST['nuevoNombre'] ?? null;

    if (!$id || !$nuevoNombre) {
        echo json_encode(["success" => false, "message" => "Datos incompletos."]);
        return;
    }

    // Validar el nuevo nombre
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $nuevoNombre)) {
        echo json_encode(["success" => false, "message" => "El nombre contiene caracteres inválidos."]);
        return;
    }

    // Construir las rutas
    $nombreAntiguo = urldecode($id);
    $rutaAntigua = $directorioImagenes . DIRECTORY_SEPARATOR . $nombreAntiguo;
    $rutaNueva = $directorioImagenes . DIRECTORY_SEPARATOR . $nuevoNombre;

    // Renombrar el archivo
    if (file_exists($rutaAntigua)) {
        if (rename($rutaAntigua, $rutaNueva)) {
            echo json_encode(["success" => true, "message" => "Nombre de la imagen actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al renombrar la imagen."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "La imagen no existe."]);
    }
}