<div class=" mb-2">
        <h5 class="mb-0"><i class="fa-solid fa-list-ul me-2"></i>Agregar / modificar pregunta</h5>
<hr>
    <div class="bg-body p-2">
        <form id="preguntaForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="preguntaId" class="form-label">ID de la pregunta</label>
                    <input type="number" class="form-control shadow-sm" id="preguntaId" name="preguntaId" required>
                </div>
                <div class="col-md-4">
                    <label for="n_pag" class="form-label">Número de página</label>
                    <input type="number" class="form-control shadow-sm" id="n_pag" required>
                </div>
                <div class="col-md-4">
                    <label for="tipo" class="form-label">Tipo de pregunta</label>
                    <select class="form-select shadow-sm" name="tipo" id="tipo" required >
                        <option value="checkbox" data-bs-toggle="tooltip" title="Pregunta con multirespuesta">Checkbox</option>
                        <option value="formSelect" data-bs-toggle="tooltip" title="Pregunta de respuesta única con desplegable de opciones">Radio desplegable</option>
                        <option value="numberInput" data-bs-toggle="tooltip" title="Pregunta con entrada numérica con rango personalizable">Entrada numérica</option>
                        <option value="radio" data-bs-toggle="tooltip" title="Pregunta de respuesta única">Radio</option>
                        <option value="cajaTexto" data-bs-toggle="tooltip" title="Caja de texto de entrada personalizada">Caja de texto</option>
                        <option value="matrix1" data-bs-toggle="tooltip" title="Matriz de respuestas con rango">Matriz con rango</option>
                        <option value="matrix2" data-bs-toggle="tooltip" title="Matriz de respuestas simple">Matriz simple</option>
                        <option value="matrix3" data-bs-toggle="tooltip" title="Matriz doble de respuestas simples ">Matriz doble</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label for="titulo" class="form-label">Título de la pregunta</label>
                    <input type="text" class="form-control shadow-sm" id="titulo" required>
                </div>
                <div class="col-md-6">
                    <label for="subTitulo" class="form-label">Subtítulo de la pregunta</label>
                    <input type="text" class="form-control shadow-sm" id="subTitulo">
                </div>
            </div>

            <!-- Contenedor para la descripción -->
            <div class="form-check form-switch my-2 rounded ">
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
            <div id="descripcionContainer" class="mt-2" style="display: none;">
                <div id="descripcionRule" class="descripcion-rule ">
                    <div class="input-group input-group-sm mb-1 ">
                        <span class="input-group-text col-md-2">Texto cabecera</span>
                        <input type="text" class="form-control shadow-sm texto1" placeholder="Cabecera">
                    </div>
                    <div class="input-group input-group-sm mb-1 ">
                        <span class="input-group-text col-md-2">Cajón de texto</span>
                        <textarea class="form-control form-control-sm shadow-sm lista" placeholder="Párrafo"></textarea>
                    </div>
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text col-md-2">Texto pie</span>
                        <input type="text" class="form-control shadow-sm texto2" placeholder="Pie">
                    </div>
                </div>
            </div>

            <div id="encabezadoFields" style="display: none;" class="mt-3 bg-light p-3 rounded">
                <h6 class="text-muted">Configuración del encabezado</h6>
                <div class="mb-2">
                    <label for="label" class="mb-2">Texto descriptivo:</label>
                    <input type="text" id="label" name="label" class="form-control form-control-sm" placeholder="Ej: La importancia de la institución...">
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <label  class="mb-2">Margen izquierdo</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text ">Clave:</span>
                            <input type="text" id="unoClave" name="unoClave" class="form-control ">
                            <span class="input-group-text">Texto:</span>
                            <input type="text" id="unoValor" name="unoValor" class="form-control w-50" placeholder="Ej: Muy en desacuerdo">
                        </div>
                    </div>
                    <div class="col-6">
                        <label  class="mb-2">Margen derecho</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Clave:</span>
                            <input type="text" id="dosClave" name="dosClave" class="form-control " >
                            <span class="input-group-text">Texto:</span>
                            <input type="text" id="dosValor" name="dosValor" class="form-control w-50" placeholder="Ej: Muy de acuerdo">
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="tres" class="mb-2">Campo adicional (opcional):</label>
                    <input type="text" id="tres" name="tres" class="form-control form-control-sm" placeholder="Información adicional">
                </div>
            </div>

            <div id="cajaTextoFields" style="display: none;">
  <div class="input-group mb-3">
    <span class="input-group-text">Placeholder</span>
    <input type="text" id="placeholder" name="placeholder" class="form-control" placeholder="Escribe el texto del placeholder">
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
                    <label class="form-label">Opciones:</label>
                    <div id="opcionesContainer"></div>

                </div>


                <div class="add-option-container my-2 ">
                    <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarOpcion()">
                        <i class="fa-solid fa-plus"></i> Agregar opción
                    </a>
                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="mostrar-filtro" name="mostrar-filtro" onchange="mostrarFiltro()">
                <label class="form-check-label align-top" for="mostrar-filtro">
                    Filtro para saltar ésta pregunta
                </label>
            </div>
            <div id="filtro-container" style="display: none;">
                <span class="muted small">
                    <ol class="alert alert-info">
                        <li class="mx-2">Seleccionamos el tipo de filtro a utilizar</li>
                        <li class="mx-2">Indicamos el ID de la pregunta donde queremos añadir el filtro</li>
                        <li class="mx-2">Introducimos el valor necesario para visualizar ésta pregunta</li>
                    </ol>
                </span>
                <div id="filtroRulesContainer"></div>
                <div class="add-jump-rule-container my-2">
                    <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarFiltro()">
                        <i class="fa-solid fa-plus"></i> Agregar regla
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