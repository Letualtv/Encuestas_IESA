<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Cookies";
    include __DIR__ . '/../../includes/navigation.php';

    $jsonData = file_get_contents(__DIR__ . '/../../models/textos.json');
    $content = json_decode($jsonData, true);

    $cookies = $content['cookies'];

    function renderCookies($cookies)
    {
        echo '<div class="container">';
        echo '<h3 class="mb-4">Política de <i>Cookies</i></h3>';
        foreach ($cookies as $section) {
            echo '<div class="mb-4">';
            if (!empty($section['question'])) {
                echo '<h5>' . $section['question'] . '</h5>';
            }
            $answers = explode('<p>', $section['answer']);
            foreach ($answers as $answer) {
                echo '<p>' . $answer . '</p>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    renderCookies($cookies);

    include __DIR__ . '/../../includes/footer.php'; ?>

</body>
