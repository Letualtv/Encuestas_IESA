<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Contactar";
    include __DIR__ . '/../../includes/navigation.php';

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

    // Verificar si 'contactar' existe y no está vacío
    if (!empty($content['contactar'])) {
        $contactar = $content['contactar'];

        function renderContact($contactar)
        {
            if (!empty($contactar)) {
                echo '<div class="container">';
                echo '<h3 class="mb-4">Contactar</h3>';
                foreach ($contactar as $section) {
                    // Verificar si 'question' o 'answer' tienen contenido
                    if (!empty($section['question']) || !empty($section['answer'])) {
                        echo '<div class="mb-4">';
                        if (!empty($section['question'])) {
                            echo '<div>' . $section['question'] . '</div>';
                        }
                        if (!empty($section['answer'])) {
                            echo '<div>' . $section['answer'] . '</div>';
                        }
                        echo '</div>'; // Cierre del div 'mb-4'
                    }
                }
                echo '</div>'; // Cierre del div 'container'
            }
            // Si $contactar está vacío, no se renderiza nada
        }

        renderContact($contactar);
    }
    // Si 'contactar' no existe o está vacío, no se llama a renderContact y no se muestra nada

    include __DIR__ . '/../../includes/footer.php';
    ?>

</body>
