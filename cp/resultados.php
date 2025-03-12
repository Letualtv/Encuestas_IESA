<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
    <div class="container-fluid py-4">
        <!-- Contenedor principal con diseño Bento -->
        <div class="row justify-content-center gy-4">

            <!-- Bloque 1: Total Encuestados -->
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card shadow-sm h-50">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h6 class="card-title">Total de claves distribuidas</h6>
                        <p class="mb-0 display-4 fw-bold align-middle" id="totalSurveys">0</p>
                    </div>
                </div>
            </div>

            <!-- Bloque 2: Encuestas Completadas -->
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card shadow-sm h-50">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h6 class="card-title mb-1 small">Total de encuestas completadas</h6>
                        <p class="mb-0 display-4 fw-bold align-middle" id="completedSurveys">0</p>
                    </div>
                </div>
            </div>

            <!-- Bloque 3: Promedio de Respuestas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <h6 class="card-title mb-1 small">Promedio de Respuestas</h6>
                        <p class="mb-0" id="averageResponses">0</p>
                    </div>
                </div>
            </div>

            <!-- Bloque 4: Gráfico de Distribución de Respuestas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3">Distribución de Respuestas</h6>
                        <canvas id="summaryChart" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bloque 5: Buscador de Claves -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3">Buscador de Claves</h6>
                        <div class="mb-2">
                            <input type="text" id="searchClave" class="form-control form-control-sm" placeholder="Buscar clave...">
                        </div>
                        <div id="claveResponses" class="small overflow-auto" style="max-height: 150px;">
                            <!-- Respuestas de la clave seleccionada se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloque 6: Tabla de Resultados -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3">Resultados del Cuestionario</h6>
                        <div class="mb-2">
                            <input type="text" id="searchResults" class="form-control form-control-sm" placeholder="Buscar por pregunta...">
                        </div>
                        <table id="resultsTable" class="table table-hover table-bordered align-middle table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>Pregunta (rX)</th>
                                    <th>Número de Respuestas</th>
                                </tr>
                            </thead>
                            <tbody id="resultsList">
                                <!-- Las filas de resultados se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bloque 7: Filtros Avanzados -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3">Filtros</h6>
                        <div class="mb-2">
                            <label for="filterCompleted" class="form-label">Estado:</label>
                            <select id="filterCompleted" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="1">Completadas</option>
                                <option value="0">No Completadas</option>
                            </select>
                        </div>
                        <button id="applyFiltersButton" class="btn btn-primary btn-sm mt-auto">Aplicar Filtros</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'vistasCP/footerCP.php'; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/utils.js"></script>
<script src="js/resultados.js"></script>