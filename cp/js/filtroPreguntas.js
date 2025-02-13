// Mostrar/ocultar las reglas de filtro
function mostrarFiltro() {
    const checkBox = document.getElementById("mostrar-filtro");
    const filtroContainer = document.getElementById("filtro-container");
    filtroContainer.style.display = checkBox.checked ? "block" : "none";
}
// Agregar una nueva regla de filtro
function agregarFiltro(rango = "", paginaDestino = "") {
    const container = document.getElementById("filtroRulesContainer");
    const ruleDiv = document.createElement("div");
    ruleDiv.classList.add("input-group", "input-group-sm", "my-2", "align-items-center");
    ruleDiv.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFiltro(this)">
            <i class="fa-solid fa-trash"></i>
        </button>
        <input type="text" class="form-control shadow-sm" name="filtro[rango][]" value="${rango}" placeholder="ID" required>
        <div class="mx-3">
            <i class="fa-solid fa-arrow-right fa-lg shadow-sm"></i>
        </div>
        <input type="number" class="form-control shadow-sm" name="filtro[paginaDestino][]" value="${paginaDestino}" placeholder="Valor" required>
    `;
    container.appendChild(ruleDiv);
}
// Eliminar una regla de filtro
function eliminarFiltro(button) {
    button.parentElement.remove();
}
function cargarFiltro(filtro) {
    const container = document.getElementById("filtroRulesContainer");
    container.innerHTML = ""; // Limpiar reglas anteriores

    for (const [rango, paginaDestino] of Object.entries(filtro)) {
        agregarFiltro(rango, paginaDestino);
    }
}
