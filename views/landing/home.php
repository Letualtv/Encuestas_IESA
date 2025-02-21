<body class="d-flex flex-column min-vh-100">

<?php
// Iniciar sesión
session_start();
session_unset();  // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión completamente

$pageTitle = "Inicio";
include __DIR__ . '/../../includes/navigation.php';
require_once __DIR__ . '/../landing/home.php';

$jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
$content = json_decode($jsonData, true);

$inicio = $content['inicio'];
?>

<div class="container mb-3">
    <div class="card mx-auto">
        <div class="card-header px-md-4 ">
            <h5 class=" py-2">
                <?php echo $inicio[0]['answer']; ?>
            </h5>
        </div>
        <div class="card-body px-md-3">
            <?php for ($i = 1; $i < count($inicio); $i++): ?>
                <p class="pb-1">
                    <?php echo $inicio[$i]['answer']; ?>
                </p>
            <?php endfor; ?>
            <div class="text-center my-1">
                <p class="fw-bold text-center">Muchas gracias por su tiempo y colaboración.</p>
                <a href="encuesta"><button class="btn btn-primary my-1 mb-4">Comenzar encuesta</button></a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
