<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Información";
    include __DIR__ . '/../../includes/navigation.php';

    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
    $content = json_decode($jsonData, true);

    $informacion = $content['info'];

    function renderInfo($informacion)
    {
        echo '<div class="container">';
        echo '<h3 class="mb-4">Información del Estudio</h3>';
        foreach ($informacion as $info) {
            echo '<div class="mb-4">';
            echo '<h5>' . $info['question'] . '</h5>';
            echo '<p>' . $info['answer'] . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }

    renderInfo($informacion);

    include __DIR__ . '/../../includes/footer.php'; ?>

</body>
