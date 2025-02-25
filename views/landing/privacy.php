<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Privacidad";
    include __DIR__ . '/../../includes/navigation.php';

    // Incluir las variables globales desde variables.php
    $variables = include __DIR__ . '/../../models/variables.php';

    // Leer el contenido del archivo textos.json
    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');

    // Reemplazar las variables globales en el contenido del JSON
    foreach ($variables as $key => $value) {
        $jsonData = str_replace($key, $value, $jsonData);
    }

    // Decodificar el JSON a un array asociativo
    $content = json_decode($jsonData, true);

    // Verificar si hubo errores en la decodificación
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar el archivo JSON: " . json_last_error_msg());
    }

    $privacidad = $content['privacidad'];

    function renderPrivacidad($privacidad)
    {
        echo '<div class="container">';
        echo '<h3 class="mb-4">Política de privacidad</h3>';
        foreach ($privacidad as $section) {
            echo '<div class="mb-4">';
            echo '<div>' . $section['question'] . '</div>';
            echo '<div>' . $section['answer'] . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    renderPrivacidad($privacidad);

    include __DIR__ . '/../../includes/footer.php';
    ?>
</body>
