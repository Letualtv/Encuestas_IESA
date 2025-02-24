<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Privacidad";
    include __DIR__ . '/../../includes/navigation.php';

    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
    $content = json_decode($jsonData, true);
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
