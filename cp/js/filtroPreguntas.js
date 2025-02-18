// Mostrar/ocultar las reglas de filtro
function mostrarFiltro() {
    const checkBox = document.getElementById("mostrar-filtro");
    const filtroContainer = document.getElementById("filtro-container");
    filtroContainer.style.display = checkBox.checked ? "block" : "none";
}

// Agregar una nueva regla de filtro
function agregarFiltro(preguntaId = "", tipoFiltro = "", parametros = "") {
    const container = document.getElementById("filtroRulesContainer");
    const ruleDiv = document.createElement("div");
    ruleDiv.classList.add(
        "input-group",
        "input-group-sm",
        "my-2",
        "align-items-center",
        "row"
    );
    ruleDiv.setAttribute("data-pregunta-id", preguntaId);

    // Crear el contenido del filtro con el nuevo orden
    ruleDiv.innerHTML = `
    <div class="col d-flex input-group input-group-sm">
        <button type="button" class="btn btn-outline-danger btn-sm " onclick="eliminarFiltro(this)">
            <i class="fa-solid fa-trash"></i>
        </button>
        <input type="text" class="form-control shadow-sm " value="${preguntaId}" placeholder="ID de la pregunta" required>
        <select class="form-select form-select-sm shadow-sm me-2" onchange="actualizarParametrosFiltro(this)">
            <option value="unico" ${tipoFiltro === "unico" ? "selected" : ""
        }>Igual a</option>
        <option value="rango-arriba" ${tipoFiltro === "rango-arriba" ? "selected" : ""
        }>Igual o superior a</option>
        <option value="rango-abajo" ${tipoFiltro === "rango-abajo" ? "selected" : ""
        }>Igual o menor a</option>
        <option value="exclusion" ${tipoFiltro === "exclusion" ? "selected" : ""
        }>Distinto de</option>
        <option value="rango-cerrado" ${tipoFiltro === "rango-cerrado" ? "selected" : ""
    }>Entre </option>
        </select>
        <div class="parametros-filtro d-flex align-items-center col-6">
            <!-- Campos dinámicos para los parámetros del filtro -->
        </div></div>

    `;

    // Añadir campos de parámetros según el tipo de filtro seleccionado
    const parametrosFiltro = ruleDiv.querySelector(".parametros-filtro");
    actualizarParametrosFiltro(
        ruleDiv.querySelector("select"),
        parametros,
        parametrosFiltro
    );

    // Agregar la regla al contenedor
    container.appendChild(ruleDiv);
}

// Actualizar los campos de parámetros del filtro según el tipo seleccionado
function actualizarParametrosFiltro(select, parametros = "", container = null) {
    if (!container) {
        container = select.parentElement.querySelector(".parametros-filtro");
    }
    container.innerHTML = ""; // Limpiar campos previos

    switch (select.value) {
        case "unico":
            container.innerHTML = `
            <div class="input-group input-group-sm  ">

                <input type="text" class="form-control shadow-sm " value="${parametros}" placeholder="Valor único" required>
                </div>

            `;
            break;
        case "rango-arriba":
            container.innerHTML = `
                            <div class="input-group input-group-sm">
    
                    <input type="text" class="form-control shadow-sm" value="${parametros.replace(
                "+",
                ""
            )}" placeholder="Valor mínimo" required>
                                    </div>
    
                `;
            break;

        case "rango-abajo":
            container.innerHTML = `
                        <div class="input-group input-group-sm">

                <input type="text" class="form-control shadow-sm" value="${parametros.replace(
                "-",
                ""
            )}" placeholder="Valor máximo" required>
                                </div>

            `;
            break;

        case "exclusion":
            container.innerHTML = `
                        <div class="input-group input-group-sm">

                <input type="text" class="form-control shadow-sm" value="${parametros.replace(
                "!=",
                ""
            )}" placeholder="Valor excluido" required>
                                </div>

            `;
            break;
case "rango-cerrado":
    const [min, max] = parametros ? parametros.split("-") : ["", ""];

    container.innerHTML = `
    <div class="input-group input-group-sm ">
        <input type="text" class="form-control shadow-sm" value="${min}" placeholder="Mínimo (X)" required>
        <div><i class="fa fa-arrow-right mx-2 fa-lg align-middle" ></i></div>
        <input type="text" class="form-control shadow-sm" value="${max}" placeholder="Máximo (Y)" required>
    </div>
    `;
    break;

    }
}

// Eliminar una regla de filtro con confirmación
function eliminarFiltro(button) {
    const modal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  
    // Actualizar el título y el cuerpo del modal
    document.getElementById("confirmDeleteModalLabel").textContent = "Confirmar eliminación";
    document.querySelector("#confirmDeleteModal .modal-body").textContent =
      "¿Estás seguro de que deseas eliminar este filtro?";
  
    modal.show();
    confirmDeleteButton.onclick = () => {
      // Lógica para eliminar el filtro
      button.parentElement.remove();
      modal.hide();
      showToast("Filtro eliminado correctamente.", "success");
    };
  }

// Cargar las reglas de filtro existentes
function cargarFiltro(filtro) {
    const container = document.getElementById("filtroRulesContainer");
    container.innerHTML = ""; // Limpiar reglas anteriores

    for (const [preguntaId, rango] of Object.entries(filtro)) {
        const tipoFiltro = determinarTipoFiltro(rango);
        agregarFiltro(preguntaId, tipoFiltro, rango);
    }
}

// Determinar el tipo de filtro basado en su formato
function determinarTipoFiltro(rango) {
    if (rango.includes("-") && !rango.endsWith("-")) return "rango-cerrado";
    if (rango.endsWith("-")) return "rango-abajo";
    if (rango.endsWith("+")) return "rango-arriba";
    if (rango.startsWith("!=")) return "exclusion";
    return "unico";
}
