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
                            <th>Número de Login</th> <!-- Nueva columna -->
                            
                            <th>Terminada</th>
                        </tr>
                    </thead>
                    <tbody id="clavesList">
                        <!-- Las filas de claves se cargarán aquí dinámicamente -->
                    </tbody>
                </table>

                <!-- Botones de paginación y eliminación -->
                <div class="d-flex justify-content-start mt-2 gap-2">
                    <button id="deleteSelectedButton" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-alt me-2"></i>Eliminar seleccionadas</button>
                    <button id="editSelectedButton" class="btn btn-primary btn-sm"><i class="fa-solid fa-edit me-2"></i>Marcar / desmarcar terminada</button>
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

                <hr>

                <!-- Zona de Peligro -->
                <div class="card mt-4 border border-danger p-4" id="dangerZone">
                    <h5 class="mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation me-2 fa-lg"></i>Zona de peligro</h5>
                    <p>Estas acciones afectan a todas las claves en la base de datos.
                    <p>¡Úsalas con precaución!</p>
                    </p>
                    <div class=" d-flex justify-content-between">
                        <!-- Botón para eliminar todas las filas -->
                        <button id="deleteAllRowsButton" class="btn btn-outline-danger fw-bold">
                            Eliminar todas las filas
                        </button>

                        <!-- Botón para marcar todas las claves como terminadas/no terminadas -->
                        <button id="markAllAsCompletedButton" class="btn btn-outline-danger fw-bold">
                            Marcar todas como <span id="markAllStatus">terminadas</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Contenedor para mensajes -->
        <div id="message" class="mt-3"></div>
    </div>



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


    <?php include 'vistasCP/footerCP.php'; ?>
</section>


<!-- Modal Genérico -->
<div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="customModalHeader">
                <h5 class="modal-title" id="customModalTitle">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="customModalBody">
                <!-- Contenido dinámico -->
                <p id="modalMessage">Generando claves aleatorias...</p>
                <!-- Spinner -->
                <div class="text-center" id="loadingSpinner" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="customModalCancelButton">Cancelar</button>
                <button type="button" class="btn btn-primary" id="customModalConfirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<script src="js/utils.js"></script>
<script src="js/clavesPoblacion.js"></script>