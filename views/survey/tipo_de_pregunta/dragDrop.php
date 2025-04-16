<?php
// Suponemos que $pregunta ya está cargada desde un JSON externo
if (isset($_GET['id'])) {
    $preguntaId = $_GET['id'];
    $pregunta = null;

    // Buscar la pregunta correspondiente al ID
    foreach ($preguntas as $p) {
        if ($p['id'] == $preguntaId) {
            $pregunta = $p;
            break;
        }
    }

    if (!$pregunta) {
        die("Pregunta no encontrada.");
    }
}
?>

<div class="container mt-5">
    <?php if ($pregunta) : ?>
        <div class="row mb-5 justify-content-center">

            <!-- Filas con Imágenes Arrastrables, Encabezados y Casillas -->
            <div class="col-md-12">
                <?php foreach ($pregunta['opciones'] as $numero => $animal) : ?>
                    <div class="mb-4 d-flex align-items-center justify-content-evenly gap-3">
                        <!-- Imagen Arrastrable y Encabezado -->
                        <div class="text-center mt-auto">
                            <div
                                class="border rounded imagen-draggable"
                                draggable="true"
                                data-numero="<?php echo $numero; ?>.jpg"
                                data-fila="fila-<?php echo $numero; ?>"
                                aria-label="<?php echo $animal; ?>"
                                style="width: 90px; height: 90px;">
                                <?php
                                $imagenPath = "views/survey/pregunta_img/{$numero}.jpg";
                                if (file_exists($imagenPath)) {
                                    echo '<img src="' . $imagenPath . '" alt="' . $animal . '" class="img-fluid rounded">';
                                } else {
                                    echo '<div class="text-muted">Imagen no disponible</div>';
                                }
                                ?>
                            </div>
                            <div class="mt-1 small fw-bold"><?php echo $animal; ?></div>
                        </div>

                        <!-- Casillas de Drop (Horizontales) -->
                        <div class="d-flex gap-2 flex-wrap mb-auto">
                            <?php for ($i = 1; $i <= 7; $i++) : ?>
                                <div
                                    class="zona-drop casilla-drop text-center"
                                    data-casilla="<?php echo $i; ?>"
                                    data-fila="fila-<?php echo $numero; ?>"
                                    aria-label="Casilla <?php echo $i; ?> para <?php echo $animal; ?>"
                                    style="width: 70px;">
                                    <div class="small fw-bold"><?php echo $i; ?></div>
                                    <div
                                        class="border rounded p-2 bg-light respuesta-casilla"
                                        data-numero="<?php echo $numero; ?>.jpg"
                                        data-casilla="<?php echo $i; ?>"
                                        style="height: 50px;">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
            </div>

            <!-- Toast para Mensajes de Error -->
            <div
                id="toast-error"
                class="toast position-fixed bottom-0 end-0 m-3 bg-danger text-white"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
                style="display: none;">
                <div class="toast-body" id="toast-message">
                    No puedes arrastrar esta imagen a otra fila.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const imagenesDraggable = document.querySelectorAll('.imagen-draggable');
        const zonasDrop = document.querySelectorAll('.zona-drop');
        const toastError = document.getElementById('toast-error');
        const toastMessage = document.getElementById('toast-message');

        // Función para mostrar el toast
        function showErrorToast(message) {
            toastMessage.textContent = message || "No puedes arrastrar esta imagen a otra fila.";
            toastError.style.display = 'block';
            new bootstrap.Toast(toastError).show();
            setTimeout(() => {
                toastError.style.display = 'none';
            }, 3000); // Ocultar automáticamente después de 3 segundos
        }

        // Agregar eventos a las imágenes
        imagenesDraggable.forEach(imagen => {
            imagen.addEventListener('dragstart', event => {
                const numeroImagen = imagen.dataset.numero; // Ahora contiene el nombre de la imagen
                const filaImagen = imagen.dataset.fila; // Captura la fila de la imagen
                event.dataTransfer.setData('text/plain', JSON.stringify({
                    numero: numeroImagen,
                    fila: filaImagen
                }));
            });
        });

        // Agregar eventos a las zonas de drop
        zonasDrop.forEach(zona => {
            zona.addEventListener('dragover', event => {
                event.preventDefault();
            });

            zona.addEventListener('drop', event => {
                event.preventDefault();
                const datosArrastrados = JSON.parse(event.dataTransfer.getData('text/plain'));
                const filaZona = zona.dataset.fila; // Captura la fila de la zona de drop

                // Verificar si la fila de la imagen coincide con la fila de la zona
                if (datosArrastrados.fila === filaZona) {
                    const casilla = zona.querySelector('.bg-light');

                    // Mostrar la imagen en la casilla
                    casilla.innerHTML = `<img src="views/survey/pregunta_img/${datosArrastrados.numero}" alt="Imagen arrastrada" class="img-fluid rounded">`;
                    casilla.classList.add('bg-primary-subtle', 'text-white');
                    casilla.classList.remove('bg-light');
                } else {
                    // Mostrar solo el toast sin cambiar el color de la casilla
                    showErrorToast(`No puedes arrastrar esta imagen a otra fila.`);
                }
            });
        });
    });
</script>