<section class="  d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>
<div class="container-fluid">
    <div class="row d-flex">
        <!-- Lista de Preguntas -->

        <div class="col-12 col-lg-7 border-end">
            <div class=" listaPreguntas">

                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fa-solid fa-stream me-2"></i>Listado de preguntas
                    <button class="btn btn-sm btn-outline-primary ms-auto" onclick="reordenarIds()">
                        <i class="fa-solid fa-sort-numeric-down"></i> Reordenar IDs y claves
                    </button>
                </h5>
                <hr>
                <!-- Barra de Búsqueda -->
                <div class="input-group p-3 pt-2">
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
        <div class="col-lg-5 col-12">
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
    <script src="js/reordenarPreguntas.js"></script>
    <script>
        const tipoPregunta = document.getElementById("tipo").value;
        console.log("Tipo de pregunta:", tipoPregunta);

        function mostrarFormulario(id = null) {
            if (id) {
                // Cargar datos de la pregunta para editar
                fetch(`includesCP/obtenerPregunta.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('preguntaId').value = data.pregunta.id;
                            document.getElementById('titulo').value = data.pregunta.titulo;
                            document.getElementById('n_pag').value = data.pregunta.n_pag;
                            document.getElementById('tipo').value = data.pregunta.tipo;
                            document.getElementById('subTitulo').value = data.pregunta.subTitulo;
                            // ...cargar otros campos según sea necesario...
                        } else {
                            console.error('Error al cargar la pregunta:', data.message);
                        }
                    })
                    .catch(error => console.error('Error al cargar la pregunta:', error));
            } else {
                reiniciarFormulario();
            }
        }

        function reiniciarFormulario() {
            fetch('includesCP/obtenerSiguienteId.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('preguntaId').value = data.siguienteId;
                        document.getElementById('titulo').value = '';
                        document.getElementById('n_pag').value = '';
                        document.getElementById('tipo').value = '';
                        document.getElementById('subTitulo').value = '';
                        // ...limpiar otros campos según sea necesario...
                    } else {
                        console.error('Error al obtener el siguiente ID:', data.message);
                    }
                })
                .catch(error => console.error('Error al obtener el siguiente ID:', error));
        }
    </script>
