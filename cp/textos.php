<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.bubble.css" rel="stylesheet" />
<!-- Incluir modales externos -->
<?php include 'vistasCP/modalGuardado.php'; ?>

<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
    <div class="container-fluid">

        <div class="row flex-grow-1 border-end">
            <!-- Barra lateral izquierda -->
            <div id="sidebar" class="col-md-2 position-sticky top-0 h-100 overflow-auto">
                <div class="p-3 text-center">
                    <h5 class="mb-0">Menú de páginas</h5>
                </div>
                <hr class="my-2">
                <div class="list-group list-group-flush px-2">
                    <?php
                    $menu = [
                        "inicio" => ["icon" => "fas fa-home", "text" => "Inicio"],
                        "faqs" => ["icon" => "fas fa-question-circle", "text" => "Preguntas frecuentes"],
                        "info" => ["icon" => "fas fa-info-circle", "text" => "Información"],
                        "privacidad" => ["icon" => "fas fa-lock", "text" => "Privacidad"],
                        "contactar" => ["icon" => "fas fa-envelope", "text" => "Contacto"],
                        "cookies" => ["icon" => "fas fa-cookie", "text" => "Cookies"],
                    ];
                    foreach ($menu as $section => $item):
                    ?>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex align-items-center py-3" onclick="loadSection('<?php echo $section; ?>')">
                            <i class="<?php echo $item['icon']; ?> me-2"></i>
                            <span><?php echo $item['text']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Panel de preguntas -->
            <div class="col-md-10">
                <div class="shadow-sm p-4">

                    <form method="POST" action="">
                        <!-- Contenedor para las preguntas -->
                        <div id="questionsContainer" class="row"></div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Toast Container -->
        <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11;"></div>
    </div>

    <?php include 'vistasCP/footerCP.php'; ?>

</section>

<script src="js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="js/textos.js"></script>
<?php include 'vistasCP/modalGuardado.php'; ?>
<script>
    // Cargar la primera sección al iniciar
    document.addEventListener('DOMContentLoaded', () => {
        const firstSection = document.querySelector('.list-group-item');
        if (firstSection) {
            firstSection.click(); // Simular clic en la primera sección
        }
    });
</script>