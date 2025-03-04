<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
    <div class="container-fluid py-4">
        <div class="row gy-4">

            <!-- Sección: Visualizar Claves Existentes -->
            <div class="col-12 col-md-8 order-2 order-md-1">
                <h5 class="mb-3"><i class="fa-solid fa-table-list me-2"></i>Claves existentes</h5>
                <hr>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="searchClaves" class="form-control" placeholder="Buscar por ID o clave...">
                </div>

                <!-- Tabla de claves -->
                <table id="clavesTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="selectAllCheckboxes" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Clave</th>
                            <th>Terminada</th>
                        </tr>
                    </thead>
                    <tbody id="clavesList">
                        <!-- Las filas de claves se cargarán aquí dinámicamente -->
                    </tbody>
                </table>

                <!-- Botones de paginación y eliminación -->
                <div class="d-flex justify-content-start mt-2 gap-2">
                    <button id="selectAllRowsButton" class="btn btn-warning btn-sm"><i class="fa-solid fa-check-double me-2"></i>Seleccionar todas las filas</button>
                    <button id="deleteSelectedButton" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-alt me-2"></i>Eliminar seleccionadas</button>
                    <button id="editSelectedButton" class="btn btn-primary btn-sm"><i class="fa-solid fa-edit me-2"></i>Marcar / desmarcar terminada</button>
                    <!-- Botón para seleccionar todas las filas -->

                    <!-- Modal de confirmación para seleccionar todas las filas -->
                    <div class="modal fade" id="confirmSelectAllModal" tabindex="-1" aria-labelledby="confirmSelectAllModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="confirmSelectAllModalLabel"><i class="fa-solid fa-exclamation-triangle me-2"></i>Confirmación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de realizar esta acción?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                                    <button type="button" class="btn btn-warning" id="confirmSelectAllButton"><i class="fa-solid fa-check me-2"></i>Continuar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="loadMoreButton" class="btn btn-outline-secondary ms-auto btn-sm"><i class="fa-solid fa-arrow-down me-2"></i>Cargar más</button>
                </div>
            </div>

            <!-- Sección: Agregar Clave Personalizada -->
            <div class="col-12 col-md-4 order-1 order-md-2">
                <h5 class="mb-3"><i class="fa-solid fa-plus me-2"></i>Añadir nuevas claves</h5>
                <hr>

                <!-- Formulario para agregar clave personalizada -->
                <form id="customKeyForm" class="mb-4">
                    <h6 class="mb-2">Agregar clave personalizada</h6>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="customKey" maxlength="5" required placeholder="ABCDE">
                        <label for="customKey">Clave (exactamente 5 caracteres alfanuméricos)</label>
                    </div>
                    <button type="submit" class="btn btn-primary "><i class="fa-solid fa-plus me-2"></i>Agregar clave</button>
                </form>

                <hr>

                <!-- Formulario para generar claves aleatorias -->
                <form id="randomKeyForm" class="mb-4">
                    <h6 class="mb-2">Generar claves aleatorias</h6>
                    <div class="mb-3 form-floating">
                        <input type="number" class="form-control" id="randomKeyCount" min="1" max="10000" required placeholder="">
                        <label for="randomKeyCount" class="form-label">Cantidad de claves (máximo 10,000):</label>
                    </div>
                    <button type="submit" class="btn btn-primary "><i class="fa-solid fa-random me-2"></i>Generar claves</button>
                </form>

                <!-- Modal de confirmación para generar claves -->
                <div class="modal fade" id="confirmGenerateModal" tabindex="-1" aria-labelledby="confirmGenerateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="confirmGenerateModalLabel"><i class="fa-solid fa-exclamation-circle me-2"></i>Confirmación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de realizar esta acción?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                                <button type="button" class="btn btn-primary" id="confirmGenerateButton"><i class="fa-solid fa-check me-2"></i>Continuar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmación final para grandes cantidades -->
                <div class="modal fade" id="finalConfirmModal" tabindex="-1" aria-labelledby="finalConfirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="finalConfirmModalLabel"><i class="fa-solid fa-exclamation-triangle me-2"></i>Confirmación adicional</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás completamente seguro de realizar esta acción?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                                <button type="button" class="btn btn-danger" id="finalConfirmButton"><i class="fa-solid fa-check me-2"></i>Confirmar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor para mensajes -->
        <div id="message" class="mt-3"></div>
    </div>

    <!-- Modal de confirmación para edición -->
    <?php include 'vistasCP/modalGuardado.php'; ?>


    <!-- Toast para notificaciones -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <!-- Modal de confirmación final para eliminar más de 20 claves -->
    <div class="modal fade" id="finalConfirmModal" tabindex="-1" aria-labelledby="finalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="finalConfirmModalLabel"><i class="fa-solid fa-exclamation-triangle me-2"></i>Confirmación Final</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás completamente seguro de realizar esta acción?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                    <button type="button" class="btn btn-danger" id="finalConfirmButton"><i class="fa-solid fa-check me-2"></i>Confirmar</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'vistasCP/footerCP.php'; ?>
</section>

<!-- Modal de confirmación para edición -->
<div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmEditModalLabel"><i class="fa-solid fa-edit me-2"></i>Confirmación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Este es el elemento que debe estar presente -->
                ¿Estás seguro de realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmEditActionButton"><i class="fa-solid fa-check me-2"></i>Continuar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fa-solid fa-trash-alt me-2"></i>Confirmación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Este es el elemento que debe estar presente -->
                ¿Estás seguro de realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times me-2"></i>Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton"><i class="fa-solid fa-check me-2"></i>Continuar</button>
            </div>
        </div>
    </div>
</div>

<script src="js/utils.js"></script>
<script src="js/clavesPoblacion.js"></script>