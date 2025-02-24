<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Contactar";
    include __DIR__ . '/../../includes/navigation.php';

    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
    $content = json_decode($jsonData, true);

    $contactar = $content['contactar'];

    function renderContact($contactar)
    {
        echo '<div class="container">';
        echo '<h3 class="mb-4">Contactar</h3>';
        foreach ($contactar as $section) {
            echo '<div class="mb-4">';
            if (!empty($section['question'])) {
                echo '<div>' . $section['question'] . '</div>';
            }
            echo '<div>' . $section['answer'] . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    renderContact($contactar);

    include __DIR__ . '/../../includes/footer.php'; ?>

</body>
