<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
    <div class="container-fluid py-4">
        <!-- Contenedor principal -->
        <div class="row justify-content-center g-3">

            <!-- Columna 1: Diseño Bento (2-1) -->
            <div class="col-12 col-md-4">
                <div class="row g-3 h-100">
                    <!-- Fila Superior (2 partes iguales) -->
                    <div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <h6 class="card-title">Total de claves distribuidas</h6>
                                <p class="mb-0 display-4 fw-bold align-middle" id="totalSurveys">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card shadow-sm ">
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <h6 class="card-title mb-1 small">Total de encuestas completadas</h6>
                                <p class="mb-0 display-4 fw-bold align-middle" id="completedSurveys">0</p>
                            </div>
                        </div>
                    </div>




                    <!-- 
                    
                    Error al eliminar las claves: SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: 
                    a foreign key constraint fails (`10796594_encuestas_iesa`.`cuestionario`, CONSTRAINT `fk_reg_m` FOREIGN KEY (`reg_m`) REFERENCES 
                    `muestra` (`reg_m`))

                    -->
                    <!-- Fila Inferior (1 parte grande) -->
                    <div class="col-12">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title text-center mb-3 text-muted">Buscador de Claves</h6>
                                <div class="mb-2">
                                    <input type="text" id="searchClave" class="form-control form-control-sm" placeholder="Buscar clave...">
                                </div>
                                <div id="claveResponses" class="small flex-grow-1">
                                    <!-- Respuestas de la clave seleccionada se cargarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Resultados del Cuestionario -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3 text-muted">Resultados del Cuestionario</h6>
                        <div class="mb-2">
                            <input type="text" id="searchResults" class="form-control form-control-sm" placeholder="Buscar por pregunta...">
                        </div>
                        <table id="resultsTable" class="table table-hover table-bordered align-middle table-sm flex-grow-1">
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

            <!-- Columna 3: Gráfico de Distribución de Respuestas -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-center mb-3 text-muted">Distribución de Respuestas</h6>
                        <canvas id="summaryChart" class="flex-grow-1" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Indicador de carga -->
            <div id="loadingSpinner" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

        </div>
    </div>

    <?php include 'vistasCP/footerCP.php'; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script src="js/utils.js" defer></script>
<script src="js/resultados.js" defer></script>