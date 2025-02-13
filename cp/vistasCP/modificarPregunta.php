<div class="card mb-2">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa-solid fa-list-ul me-2"></i>Agregar / modificar pregunta</h5>
    </div>

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
                    <select class="form-select shadow-sm" id="tipo" required onchange="ajustarParametros()">
                        <option value="radio">Radio</option>
                        <option value="numberInput">Entrada numérica</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="formSelect">Radio desplegable</option>
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

            <div id="valores" class="mt-3">
                <div id="numberInputFields" class="bg-light p-3 rounded" style="display: none;">
                    <h6 class="text-muted">Valores para la entrada numérica:</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="min" class="form-label">Valor mínimo</label>
                            <input type="number" class="form-control" id="min" name="valores[min]">
                        </div>
                        <div class="col-md-4">
                            <label for="max" class="form-label">Valor máximo</label>
                            <input type="number" class="form-control" id="max" name="valores[max]">
                        </div>
                        <div class="col-md-4">
                            <label for="placeholder" class="form-label">Placeholder</label>
                            <input type="text" class="form-control" id="placeholder" name="valores[placeholder]">
                        </div>
                    </div>
                </div>
            </div>

            <div id="opciones" class="mt-3">
                <label class="form-label">Opciones:</label>
                <div id="opcionesContainer"></div>
            </div>
            <div class="add-option-container my-2">
                <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarOpcion()">
                    <i class="fa-solid fa-plus"></i> Agregar opción
                </a>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input " type="checkbox" id="mostrar-filtro" name="mostrar-filtro" onchange="mostrarFiltro()">
                <label class="form-check-label align-top" for="mostrar-filtro">
                    Reglas de filtro
                </label>
            </div>
            <div class="row col-12 col-md-5">
                <div id="filtro-container" style="display: none;">
                    <span class="muted small ">
                        <ol class="alert alert-info">
                        <li class="mx-2">Indicamos el ID de la pregunta donde queremos añadir el filtro</li>
                        <li class="mx-2">Introducimos el valor necesario para visualizar ésta pregunta</li></ol>
                    </span>
                    <div id="filtroRulesContainer"></div>
                    <div class="add-jump-rule-container my-2">
                        <a type="button" class="btn btn-sm btn-outline-primary hover-zoom" onclick="agregarFiltro()">
                            <i class="fa-solid fa-plus"></i> Agregar regla
                        </a>
                    </div>
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