<section class="  d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
<div class="container-fluid">
    <div class="row d-flex">
        <!-- Lista de Preguntas -->

        <div class="col-12 col-lg-6 border-end">
            <div class=" listaPreguntas">
                <h5 class="mb-0"><i class="fa-solid fa-stream me-2"></i>Listado de preguntas</h5>
                <hr>
                <!-- Barra de Búsqueda -->
                <div class="input-group p-3">
                    <input
                        type="text"
                        class="form-control shadow-sm"
                        id="searchQuestions"
                        placeholder="Buscar pregunta por título, ID o subtítulo..."
                        oninput="buscarPregunta()">
                </div>
                <ul id="preguntasList" class="list-group list-group-flush"></ul>
            </div>

        </div>
        <div class="col-lg-6 col-12">

            <?php include 'vistasCP/modificarPregunta.php'; ?>

        </div>
    </div>

    <?php include_once 'vistasCP/modalBorrado.php'; ?>
    <?php include_once 'vistasCP/modalGuardado.php'; ?>
</div>
<?php include_once 'vistasCP/footerCP.php'; ?>
</section>



    <!-- Scripts -->
    <script src="js/utils.js"></script>
    <script src="js/editarPreguntas.js"></script>
    <script src="js/agregarDescripcion.js"></script>
    <script src="js/filtroPreguntas.js"></script>
    <script src="js/guardarPreguntas.js"></script>
    <script src="js/agregarEliminar.js"></script>
    <script src="js/listadoPreguntas.js"></script>
    <script>
        const tipoPregunta = document.getElementById("tipo").value;
        console.log("Tipo de pregunta:", tipoPregunta);
    </script>
    <!--     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="js/exportar.js"></script>  -->
