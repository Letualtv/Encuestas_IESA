<body class="d-flex flex-column min-vh-100">
<?php
// Iniciar sesión
session_start();
session_unset();  // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión completamente

$pageTitle = "Inicio";
include __DIR__ . '/../../includes/navigation.php';

// Cargar datos desde el archivo JSON
$jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');

if ($jsonData === false) {
    die('Error: No se pudo cargar el archivo JSON.');
}

$content = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error: El archivo JSON no es válido. ' . json_last_error_msg());
}

// Acceder al primer elemento del array "inicio"
$inicio = $content['inicio'][0];

// Función para renderizar el contenido del inicio
function renderInicio($inicio)
{
    if (!isset($inicio['question']) || !isset($inicio['answer'])) {
        die('Error: Los datos de "inicio" no están completos en el archivo JSON.');
    }

    echo '<div class="container mb-3">';
    echo '<div class="card mx-auto">';
    echo '<div class="card-header px-md-4">';
    echo '<div class="py-2">' . $inicio['question'] . '</div>';
    echo '</div>';
    echo '<div class="card-body px-md-3">';
    echo '<div class="pb-1">' . $inicio['answer'] . '</div>';
    echo '<div class="text-center my-1">';
    echo '<p class="fw-bold text-center">Muchas gracias por su tiempo y colaboración.</p>';
    echo '<a href="encuesta"><button class="btn btn-primary my-1 mb-4">Comenzar encuesta</button></a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// Renderizar el contenido del inicio
renderInicio($inicio);

include __DIR__ . '/../../includes/footer.php';
?>
</body>