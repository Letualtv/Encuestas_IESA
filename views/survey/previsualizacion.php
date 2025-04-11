<div class="card">
    <?php
    if (isset($_GET['id'])) {
        $preguntaId = $_GET['id'];

        // Cargar el JSON de preguntas
        $jsonPath = __DIR__ . '../../../models/Preguntas.json';
        $preguntas = json_decode(file_get_contents($jsonPath), true);

        // Buscar la pregunta por ID
        $pregunta = null;
        foreach ($preguntas as $p) {
            if ($p['id'] == $preguntaId) {
                $pregunta = $p;
                break;
            }
        }

        if (!$pregunta) {
            echo "<div class='alert alert-danger'>Pregunta no encontrada.</div>";
            exit;
        }

        // Mostrar cabecera, título y subtítulo con estilos compactos
        echo "<div class='card-header p-3'>";
        echo "<h5 class='mb-1' style='font-size: 1.1em;'>{$pregunta['titulo']}</h5>";
        if (!empty($pregunta['subTitulo'])) {
            echo "<p class='mb-0 text-muted' style='font-size: 0.9em;'>{$pregunta['subTitulo']}</p>";
        }
        echo "</div>";

        // Incorporar la cabecera si existe
        if (isset($pregunta['cabecera'])) {
            $cabeceraContent = '';
            if (isset($pregunta['cabecera']['texto'])) {
            $cabeceraContent .= "{$pregunta['cabecera']['texto']}";
            }

            if (!empty($cabeceraContent)) {
            echo "<div class='p-3'>{$cabeceraContent}</div>";
            }
        }

        echo "<div class='card-body'>";
        // Renderizar la pregunta utilizando el tipo correspondiente
        switch ($pregunta['tipo']) {
            case 'radio':
                include_once __DIR__ . '/tipo_de_pregunta/radio.php';
                break;
            case 'checkbox':
                include_once __DIR__ . '/tipo_de_pregunta/checkbox.php';
                break;
            case 'numberInput':
                include_once __DIR__ . '/tipo_de_pregunta/numberInput.php';
                break;
            case 'formSelect':
                include_once __DIR__ . '/tipo_de_pregunta/formSelect.php';
                break;
            case 'matrix1':
                include_once __DIR__ . '/tipo_de_pregunta/matrix1.php';
                break;
            case 'matrix2':
                include_once __DIR__ . '/tipo_de_pregunta/matrix2.php';
                break;
            case 'matrix3':
                include_once __DIR__ . '/tipo_de_pregunta/matrix3.php';
                break;
            default:
                echo "<div class='alert alert-danger'>Tipo de pregunta no soportado para previsualización.</div>";
                break;
        }
        echo "</div>";
    }
    ?>

    <?php if (isset($pregunta['final'])): ?>
        <div class="card-body text-center">
            <p class="text-center fst-italic py-1 mb-0"><?= $pregunta['final'] ?></p>
        </div>
    <?php endif; ?>

    <div class="card-footer text-center">
        <div class="row p-1">
            <div class="col">
                <button class="btn btn-primary btn-sm">Anterior</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button class="btn btn-warning btn-sm" >Resetear</button>
            </div>
            <div class="col">
                <button  class="btn btn-primary btn-sm">Siguiente</button>
            </div>
        </div>
    </div>
</div>
