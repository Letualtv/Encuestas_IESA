

<section class=" d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
<div class="container-fluid">
    <div class="d-flex row  pb-3 gy-2 align-items-stretch ">
        <div class="col-12 col-md-6 order-2 order-md-1 border-end">
            <!-- Formulario para agregar variables -->
            <div class=" mb-2 h-100">

                <h5 class="mb-0"><i class="fa-solid fa-globe me-2"></i>Parámetros Globales</h5>
                <hr>

                <div class="bg-body p-2">
                    <form id="globalParamsForm">
                        <div class="row justify-content-between align-items-end">
                            <div class="col">
                                <label for="clave" class="form-label">Clave</label>
                                <input type="text" class="form-control shadow-sm" id="clave" placeholder="Ej: $institucion" required>
                            </div>
                            <div class="col-8">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control shadow-sm" id="valor" placeholder="Ej: Universidad de Madrid" required>
                            </div>
                            <div class="col d-flex align-items-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-check me-2"></i>Insertar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Lista de variables disponibles -->
                    <div class="mt-4">
                        <h6 class="mb-3">Variables disponibles:</h6>
                        <div id="variablesList" class="list-group"></div>
                    </div>
                </div>
            </div>
        </div>
   
        <div class="col-12 col-md-6 order-1 order-md-2 ">
            <div class=" mb-2 h-100">
                <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Información</h5>
                <hr>
                <div class="bg-body p-2">
                    <h6>Existen una serie de variables pre-definidas que se pueden modificar:</h6>
                    <span class="muted span">(Están incluidas en los Parámetros globales)</span>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nombre:</th>
                                <th scope="col">Función:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">$textoCabecera</td>
                                <td>Modifica el texto presente en la cabecera de la página principal</td>
                            </tr>
                            <tr>
                                <td scope="row">$textoCabeceraEncuesta</td>
                                <td>Modifica el texto presente en la cabecera de la encuesta</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<hr>
    <!-- Imágenes de la encuesta -->
    <div class="row">
        <div class="col-12">
            <div class=" mb-2">
                <div class=" d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-image me-2"></i>Imágenes de la encuesta</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="cargarImagenes()">
                        <i class="fa-solid fa-sync me-2"></i>Recargar imágenes
                    </button>
                </div>

                <div class="bg-body row p-2">
                    <!-- Columna izquierda: Lista de imágenes -->
                    <div class="col-12 col-md-6">
                        <h6 class="mb-3">Imágenes disponibles:</h6>
                        <div id="encuestaImagesList" class="list-group mt-3">
                            <!-- Las imágenes se cargarán dinámicamente aquí -->
                        </div>
                    </div>

                    <!-- Columna derecha: Formulario para subir imágenes -->
                    <div class="col-12 col-md-6">
                        <h6 class="mb-3">Subir nueva imagen:</h6>
                        <form id="imageUploadForm" class="input-group" enctype="multipart/form-data">
                            <div class="input-group">

                                <input type="file" class="form-control" id="encuestaImage" accept="image/*" required>

                                <button type="submit" class="btn btn-primary ">
                                    <i class="fa-solid fa-upload me-2"></i>Subir imagen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include 'vistasCP/footerCP.php'; ?>
</section>
<!-- Modal de Confirmación de Borrado -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white modal-dialog-centered">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar borrado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas borrar esta variable?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Borrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para previsualizar imágenes -->
<div class="modal fade" id="previewImageModal" tabindex="-1" aria-labelledby="previewImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-dialog-centered">
                <h5 class="modal-title" id="previewImageModalLabel">Previsualización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Aquí se cargará la imagen -->
            </div>
        </div>
    </div>
</div>
<!-- Modal para editar el nombre de la imagen -->
<div class="modal fade" id="editImageNameModal" tabindex="-1" aria-labelledby="editImageNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-dialog-centered">
                <h5 class="modal-title" id="editImageNameModalLabel">Editar nombre de la imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newImageName" class="form-label">Nuevo nombre:</label>
                    <input type="text" class="form-control" id="newImageName" placeholder="Introduce el nuevo nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveImageNameButton">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Edición -->
<div class="modal fade" id="editVariableModal" tabindex="-1" aria-labelledby="editVariableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-dialog-centered">
                <h5 class="modal-title" id="editVariableModalLabel">Editar variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editVariableForm">
                    <div class="mb-3">
                        <label for="editClave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="editClave" placeholder="Clave (ej. $nombre)" required>
                    </div>
                    <div class="mb-3">
                        <label for="editValor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="editValor" placeholder="Valor" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEditButton">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<!-- Contenedor de Notificaciones -->
<div id="notificationContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>



<script src="js/imagenesGenerales.js"></script>
<script src="js/variablesGenerales.js"></script>
<script src="js/utils.js"></script>