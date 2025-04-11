<!-- Incluir los estilos y scripts de la última versión de Quill -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.bubble.css" rel="stylesheet" />

<div class=" mb-2">
    <h5 class="mb-0"><i class="fa-solid fa-list-ul me-2"></i>Agregar / modificar pregunta</h5>
    <hr>
    <div class="bg-body p-2">
        <form id="preguntaForm">
            <div class="row g-3">
                <div class="col">
                    <label for="tipo" class="form-label">Tipo de pregunta</label>
                    <select class="form-select shadow-sm" name="tipo" id="tipo" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="checkbox" data-bs-toggle="tooltip" title="Pregunta con multirespuesta">Multirespuesta</option>
                        <option value="formSelect" data-bs-toggle="tooltip" title="Pregunta de respuesta única con desplegable de opciones">Respuesta única desplegable</option>
                        <option value="numberInput" data-bs-toggle="tooltip" title="Pregunta con entrada numérica con rango personalizable">Entrada numérica</option>
                        <option value="radio" data-bs-toggle="tooltip" title="Pregunta de respuesta única">Respuesta única</option>
                        <option value="cajaTexto" data-bs-toggle="tooltip" title="Caja de texto de entrada personalizada">Caja de texto</option>
                        <option value="matrix1" data-bs-toggle="tooltip" title="Matriz de respuestas con rango">Matriz con rango</option>
                        <option value="matrix2" data-bs-toggle="tooltip" title="Matriz de respuestas simple">Matriz simple</option>
                        <option value="matrix3" data-bs-toggle="tooltip" title="Matriz doble de respuestas simples ">Matriz doble</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="n_pag" class="form-label">Nº de página:</label>
                    <input type="number" class="form-control shadow-sm" id="n_pag" required>
                </div>
                <div class="col-md-2">
                    <label for="preguntaId" class="form-label">ID:</label>
                    <input class="form-control shadow-sm text-center" id="preguntaId" name="preguntaId" disabled>
                </div>
            </div>
            <div class="form-check form-switch mx-1 mt-2">
                <input class="form-check-input" type="checkbox" id="mostrar-filtro" name="mostrar-filtro" onchange="mostrarFiltro()">
                <label class="form-check-label align-top" for="mostrar-filtro">
                    Filtro para visualizar ésta pregunta
                </label>
            </div>
            <div id="filtro-container" class="mx-1 my-2" style="display: none;">
                <span class="muted small">
                    <ol class="alert alert-info">
                        <li class="mx-2">Indica en qué pregunta quieres añadirlo (ID)</li>
                        <li class="mx-2">Selecciona el tipo de filtro</li>
                        <li class="mx-2">Introduce el valor que ha de tener para que aparezca ésta pregunta</li>
                    </ol>
                </span>
                <div id="filtroRulesContainer"></div>
                <div class="add-jump-rule-container my-2">
                    <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarFiltro()">
                        <i class="fa-solid fa-plus"></i> Agregar regla
                    </a>
                </div>
            </div>

            <div class="card mt-3">

                <div class=" card-header">
                    <input type="text" class="form-control shadow-sm fw-bold mb-1" id="titulo" placeholder="Titulo de la pregunta" required>

                    <input type="text" class="form-control shadow-sm" placeholder="Subtitulo de la pregunta" id="subTitulo">
                </div>

                <!-- Contenedor para la descripción -->
                <div class="form-check form-switch ms-3 mt-1 rounded ">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="mostrar-descripcion"
                        onchange="toggleDescripcion()" />
                    <label class="form-check-label align-top " for="mostrar-descripcion">
                        Añadir descripción de pregunta
                    </label>
                </div>

                <!-- Contenedor de la descripción (inicialmente oculto) -->
                <div id="descripcionContainer" class="mt-1 mx-3 " style="display: none;">
                    <div id="descripcionRule" class="descripcion-rule mb-3 border rounded">
                    </div>
                </div>


                <div id="encabezadoFields" style="display: none;" class="mt-1 bg-light p-3 rounded">

                    <h6 class="text-muted">Configuración encabezado de matrices</h6>
                    <div class="mb-2">
                        <label for="label">Texto descriptivo en la izquierda:</label>
                        <input type="text" id="label" name="label" class="form-control form-control-sm" placeholder="Ej: La importancia de la institución...">
                    </div>
                    <div class="row mb-2">
                        <h6 class="text-muted pt-1">Valores de la matriz:</h6>
                        <div class="col-6">
                            <label>Extremo izquierdo</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="unoClave" name="unoClave" value="1" placeholder="Nº inicio" class="form-control ">
                                <input type="text" id="unoValor" name="unoValor" placeholder="Texto" class="form-control w-75" placeholder="Ej: Muy en desacuerdo">
                            </div>
                        </div>
                        <div class="col-6">
                            <label >Extremo derecho </label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="dosClave" name="dosClave" value="7" placeholder="Nº final" class="form-control ">
                                <input type="text" id="dosValor" name="dosValor" placeholder="Texto" class="form-control w-75" placeholder="Ej: Muy de acuerdo">
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="tres" >Campo adicional a la derecha:</label>
                        <input type="text" id="tres" name="tres" class="form-control form-control-sm" placeholder="Información adicional">
                    </div>

                </div>

                <div id="cajaTextoFields" class="mx-3 mt-3" style="display: none;">
                    <div class="input-group mb-3">
                        <input type="text" id="placeholder" name="placeholder" class="form-control" placeholder="Escribe la descripción dentro de la caja de texto">

                    </div>
                </div>

                <div id="valores" class="mt-3">
                    <div id="numberInputFields" class="bg-light p-2 rounded" style="display: none;">
                        <h6 class="text-muted">Valores para la entrada numérica:</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="min" class="form-label">Valor mínimo</label>
                                <input type="number" class="form-control form-control-sm" id="min" name="valores[min]">
                            </div>
                            <div class="col-md-4">
                                <label for="max" class="form-label">Valor máximo</label>
                                <input type="number" class="form-control form-control-sm" id="max" name="valores[max]">
                            </div>
                            <div class="col-md-4">
                                <label for="placeholder" class="form-label">Placeholder</label>
                                <input type="text" class="form-control form-control-sm" id="placeholder" name="valores[placeholder]">
                            </div>
                        </div>
                    </div>

                </div>

                <div id="formSelectFields" style="display: none;">
                    <label for="defaultOption">Opción predeterminada:</label>
                    <input type="text" id="defaultOption" name="defaultOption" placeholder="Clave de la opción predeterminada">
                </div>
                <div class="bg-light p-2">
                    <div id="opciones">
                        <div id="opcionesContainer"></div>

                    </div>

                </div>
                <div class="add-option-container my-2 ">
                    <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarOpcion()">
                        <i class="fa-solid fa-plus"></i> Agregar opción
                    </a>
                </div>
            </div>






            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Guardar pregunta
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
  const quill = new Quill('#descripcionRule', {
    theme: 'bubble',
  });


    
    document.addEventListener("DOMContentLoaded", function() {
        fetch('includesCP/obtenerSiguienteId.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preguntaId').value = data.siguienteId;
                } else {
                    console.error('Error al obtener el siguiente ID:', data.message);
                }
            })
            .catch(error => console.error('Error al obtener el siguiente ID:', error));
    });
</script>