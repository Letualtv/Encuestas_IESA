
<body class="container-fluid d-flex flex-column min-vh-100">
<?php include 'vistasCP/navbarCP.php'; ?>
<h5 class="mb-4"><i class="fa-solid fa-cogs me-2"></i>Modificación de encuesta</h5>


<div class="row d-flex">
    <!-- Lista de Preguntas -->
     
    <div class="col-12 col-lg-6">
        <div class="card listaPreguntas">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-stream me-2"></i>Listado de preguntas</h5>
            </div>
            <!-- Barra de Búsqueda -->
            <div class="input-group p-3">
                <input type="text" class="form-control shadow-sm" id="searchQuestions" placeholder="Buscar pregunta por título, ID o subtítulo...">
                <button class="btn btn-outline-secondary"  onclick="buscarPregunta()">Buscar</button>
            </div>
            <ul id="preguntasList" class="list-group list-group-flush"></ul>
            <div class="bg-body py-2 justify-content-end d-flex">
                <!-- Botón Exportar -->
                <button type="button" class="btn btn-success m-2 dropdown-toggle ms-auto" data-bs-toggle="dropdown">
                    Exportar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportarJSON()">Exportar JSON</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportarCSV()">Exportar CSV</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportarExcel()">Exportar Excel</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportarPDF()">Exportar PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
    
        <div class="col-lg-6 col-12">

            <?php include_once 'vistasCP/modificarPregunta.php'; ?>

        </div>
    </div>

    <?php include_once 'vistasControlPanel/modal_borrado.php'; ?>

    <?php include_once 'vistasCP/footerCP.php'; ?>



    <!-- Scripts -->
    <script src="js/filtroPreguntas.js"></script>
    <script src="js/editarPreguntas.js"></script> 
    <script src="js/listadoPreguntas.js"></script>
    <script src="js/agregarEliminar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="js/exportar.js"></script> 

</body>