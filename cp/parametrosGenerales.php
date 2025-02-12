<?php include 'vistasCP/navbarCP.php'; ?>

<section class="container-fluid d-flex flex-column min-vh-100">

    <h5 class="mb-4"><i class="fa-solid fa-cogs me-2"></i>Parámetros Generales</h5>

    <!-- Formulario para agregar variables -->
    <div class="col-12 card mb-2">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-globe me-2"></i>Parámetros Globales</h5>
        </div>
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

    <!-- Modal de Confirmación de Borrado -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Borrado</h5>
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

    <!-- Modal de Edición -->
    <div class="modal fade" id="editVariableModal" tabindex="-1" aria-labelledby="editVariableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVariableModalLabel">Editar Variable</h5>
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


    <?php include 'vistasCP/footerCP.php'; ?>

</section>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script src="js/variablesGenerales.js"></script>