// Mostrar/u ocultar las reglas de salto
function mostrarJumpRules() {
    const checkBox = document.getElementById("mostrar-jump-rules");
    const jumpRules = document.getElementById("jump-rules");
    jumpRules.style.display = checkBox.checked ? "block" : "none";
}

// Agregar una nueva regla de salto
function agregarJumpRule(rango = "", paginaDestino = "") {
    const container = document.getElementById("jumpRulesContainer");
    const ruleDiv = document.createElement("div");
    ruleDiv.classList.add("input-group", "input-group-sm", "mb-2", "align-items-center");

    ruleDiv.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarJumpRule(this)">
            <i class="fa-solid fa-trash"></i>
        </button>
        <input type="text" class="form-control shadow-sm" name="jump_rules[rango][]" value="${rango}" placeholder="Rango" required>
        <div class="mx-3">
            <i class="fa-solid fa-arrow-right fa-lg"></i>
        </div>
        <input type="number" class="form-control w-50 shadow-sm" name="jump_rules[paginaDestino][]" value="${paginaDestino}" placeholder="Página destino" required>
    `;
    container.appendChild(ruleDiv);
}

// Eliminar una regla de salto
function eliminarJumpRule(button) {
    button.parentElement.remove();
}
function cargarJumpRules(filtro) {
    const container = document.getElementById("jumpRulesContainer");
    container.innerHTML = ''; // Limpiar reglas anteriores

    for (const [rango, paginaDestino] of Object.entries(filtro)) {
        agregarJumpRule(rango, paginaDestino);
    }
}