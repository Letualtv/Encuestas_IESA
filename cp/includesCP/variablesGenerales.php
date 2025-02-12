<?php
// Ruta al archivo de variables
$variablesFile = '../../models/variables.php';


// Cargar las variables actuales
if (!file_exists($variablesFile)) {
    file_put_contents($variablesFile, "<?php\nreturn [];");
}
$variables = include $variablesFile;

// Función para validar el formato de la clave
function validarClave($clave) {
    return preg_match('/^\$[a-zA-Z0-9_]+$/', $clave);
}

// Acción para listar las variables actuales
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'listar') {
    // Devolver las variables como JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'variables' => $variables]);
    exit;
}

// Acción para agregar o actualizar una variable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Guardar una nueva variable o actualizar una existente
    if ($accion === 'guardar') {
        $clave = trim($_POST['clave']);
        $valor = trim($_POST['valor']);

        if (!empty($clave) && !empty($valor)) {
            // Validar que la clave tenga el formato correcto
            if (validarClave($clave)) {
                $variables[$clave] = $valor;

                // Guardar las variables actualizadas en el archivo PHP
                $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
                file_put_contents($variablesFile, $contenido);

                echo json_encode(['success' => true, 'message' => 'Variable guardada correctamente.']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'La clave debe tener el formato $nombre.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Clave o valor no pueden estar vacíos.']);
            exit;
        }
    }

    // Actualizar una variable existente
    if ($accion === 'actualizar') {
        $claveOriginal = htmlspecialchars(trim($_POST['clave']));
        $nuevaClave = htmlspecialchars(trim($_POST['nuevaClave']));
        $nuevoValor = htmlspecialchars(trim($_POST['valor']));

        if (!empty($claveOriginal) && array_key_exists($claveOriginal, $variables)) {
            // Validar que la nueva clave tenga el formato correcto
            if (!validarClave($nuevaClave)) {
                echo json_encode(['success' => false, 'message' => 'La nueva clave debe tener el formato $nombre.']);
                exit;
            }

            // Eliminar la clave original y agregar la nueva clave con el nuevo valor
            unset($variables[$claveOriginal]);
            $variables[$nuevaClave] = $nuevoValor;

            // Guardar las variables actualizadas en el archivo PHP
            $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
            file_put_contents($variablesFile, $contenido);

            echo json_encode(['success' => true, 'message' => 'Variable actualizada correctamente.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'La clave original no existe.']);
            exit;
        }
    }

    // Borrar una variable
    if ($accion === 'borrar') {
        $clave = htmlspecialchars(trim($_POST['clave']));

        if (array_key_exists($clave, $variables)) {
            unset($variables[$clave]);

            // Guardar las variables actualizadas en el archivo PHP
            $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
            file_put_contents($variablesFile, $contenido);

            echo json_encode(['success' => true, 'message' => 'Variable eliminada correctamente.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'La clave no existe.']);
            exit;
        }
    }
}
?>