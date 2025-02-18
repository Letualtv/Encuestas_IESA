<?php include 'vistasCP/navbarCP.php'; ?>

<section class="container-fluid d-flex flex-column min-vh-100">

    <div class="d-flex row  pb-3 gy-2 align-items-stretch">

        <!-- Tarjeta: Visualizar Claves Existentes -->
        <div class="col-12 col-md-6 order-2 order-md-1">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Claves existentes</h5>
        </div>
        <div class="card-body">
            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" id="searchClaves" class="form-control" placeholder="Buscar por ID o clave...">
            </div>

            <!-- Tabla de claves -->
            <table id="clavesTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Terminada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="clavesList">
                    <!-- Las filas de claves se cargarán aquí dinámicamente -->
                </tbody>
            </table>

            <!-- Botones de paginación y eliminación -->
            <div class="d-flex justify-content-between mt-3">
                <button id="viewAllButton" class="btn btn-outline-warning">Ver todas las claves</button>
                <button id="loadMoreButton" class="btn btn-outline-primary">Cargar más</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar una clave -->
<div class="modal fade" id="editClaveModal" tabindex="-1" aria-labelledby="editClaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClaveModalLabel">Editar clave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editClaveForm">
                    <div class="mb-3">
                        <label for="editClaveId" class="form-label">ID</label>
                        <input type="text" class="form-control" id="editClaveId" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editClaveValue" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="editClaveValue" maxlength="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="editClaveTerminada" class="form-label">Terminada</label>
                        <select class="form-select" id="editClaveTerminada">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEditClaveButton">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
        <div class="col-12 col-md-6 order-1 order-md-2 ">

            <!-- Tarjeta: Agregar Clave Personalizada -->
            <div class="card ">
                <div class="card-header ">
                    <h5 class="mb-0">Claves</h5>
                </div>
                <div class="card-body">
                    <form id="customKeyForm" class="mb-4">
                        <h6 class="mb-0">Agregar clave personalizada</h6>
                        <div class="mb-3">
                            <label for="customKey" class="form-label">Clave (máximo 5 dígitos):</label>
                            <input type="text" class="form-control" id="customKey" maxlength="5" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar clave</button>
                    </form>
                    <hr>
                    <!-- Generar Claves Aleatorias -->
                    <h6 class="mb-0">Generar claves aleatorias</h6>
                    <form id="randomKeyForm" class="mb-4">
                        <div class="mb-3">
                            <label for="randomKeyCount" class="form-label">Cantidad de claves (máximo 10,000):</label>
                            <input type="number" class="form-control" id="randomKeyCount" min="1" max="10000" required>
                        </div>
                        <button type="submit" class="btn btn-success">Generar claves</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor para mensajes -->
    <div id="message" class="mt-3"></div>

</section>

<?php include 'vistasCP/footerCP.php'; ?>

<script src="js/utils.js"></script>
<script src="js/clavesPoblacion.js"></script>