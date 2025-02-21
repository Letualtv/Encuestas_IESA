<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "FAQ";
    include __DIR__ . '/../../includes/navigation.php';

    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
    $content = json_decode($jsonData, true);

    $faqs = $content['faqs'];

    function renderFaq($faqs)
    {
        echo '<div class="container">';
        echo '<h3 class="mb-4">Preguntas frecuentes - FAQs</h3>';
        foreach ($faqs as $faq) {
            echo '<div class="mb-4">';
            echo '<h5>' . $faq['question'] . '</h5>';
            echo '<p>' . $faq['answer'] . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }

    renderFaq($faqs);


    include __DIR__ . '/../../includes/footer.php'; ?>

</body>