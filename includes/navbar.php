<div class="my-4">
    <!-- Logos -->
    <div class="row justify-content-around col-lg-8 offset-lg-2  ">


        <?php
        // Definir la ruta absoluta al directorio de imágenes
        $dir = 'assets/img/';

        // Verificar si el directorio existe y abrirlo
        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                // Ignorar archivos ocultos como '.' y '..'
                if ($file === '.' || $file === '..') {
                    continue;
                }

                // Filtrar por extensiones válidas
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
                    // Generar la URL de la imagen dinámicamente
                    $imagePath = 'assets/img/' . $file; // Ajusta esto si el prefijo cambia
                    echo '<img class="mx-1 my-2 my-lg-0 img-fluid img-responsive-60 " src="' . $imagePath . '" alt="' . pathinfo($file, PATHINFO_FILENAME) . '" title="' . pathinfo($file, PATHINFO_FILENAME) . '">';
                }
            }
            closedir($handle);
        } else {
            echo "No se pudo abrir el directorio de imágenes.";
        }

        ?>
        <style>
            .img-responsive-60 {
                max-height: 45px;
                width: auto;
            }

            @media (max-width: 767px) {
                .img-responsive-60 {
                    max-height: 35px;
                    width: auto;
                }
            }
        </style>
    </div>

    <!-- Título -->
    <div class="text-center my-4 mx-2">
        <h3><?php
            echo $variables['$textoCabecera'];

            ?></h3>
    </div>

    <!-- Menú -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand d-lg-none" href="inicio">
                <img src="assets/img/2-IESA.png" alt="Logo" height="40">
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php foreach ($menuItems as $url => $title): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link <?= ($currentUrl === $url || ($currentUrl === '' && $url === 'inicio')) ? 'active' : '' ?>" href="<?= $url ?>"><?= $title ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <style>
        .navbar-nav .nav-link.active {
            font-weight: bold;
            color: #003366;
        }

        .navbar-light.bg-light {
            background-color: #f7f7f7 !important;
        }
    </style>

    <!-- Contenido dinámico -->
    <div class="container mt-3">
        <?php
        switch ($currentUrl) {
            case "":
            case "inicio":
                echo "Bienvenido al estudio INNOQUAL, una evaluación de las características de las instituciones que contribuyen a un mejor desempeño de sus funciones.";
                break;
            case "informacion":
                echo "Aquí encontrará información detallada sobre el estudio";
                break;
            case "encuesta":
                echo "Bienvenido a la encuesta. Por favor, complete las preguntas a continuación";
                break;
            case "faq":
                echo "Preguntas frecuentes relacionadas con el estudio INNOQUAL.";
                break;
            case "privacidad":
                echo "Política de privacidad del estudio INNOQUAL.";
                break;
            case "contactar":
                echo "Información de contacto para consultas relacionadas con el estudio";
                break;
            default:
                echo "";
        }
        ?>
    </div>
</div>