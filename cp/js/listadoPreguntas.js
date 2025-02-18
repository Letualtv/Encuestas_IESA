

// Función para cargar preguntas en la lista
function cargarPreguntas() {
    fetch("includesCP/obtenerPreguntas.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            const preguntasList = document.getElementById("preguntasList");
            preguntasList.innerHTML = ""; // Limpiar la lista

            if (data.length === 0) {
                preguntasList.innerHTML = "<p class='text-muted text-center'>No hay preguntas disponibles.</p>";
                return;
            }

            const table = crearTablaPreguntas(data);
            preguntasList.appendChild(table);
        })
        .catch(error => {
            console.error("Error al cargar las preguntas:", error);
            document.getElementById("preguntasList").innerHTML = `
                <p class='text-danger text-center'>
                    Error al cargar las preguntas. Inténtalo de nuevo más tarde.
                </p>`;
        });
}

// Función para crear una tabla de preguntas
function crearTablaPreguntas(data) {
    const table = document.createElement("table");
    table.classList.add("table", "table-bordered", "table-sm");

    const thead = document.createElement("thead");
    thead.innerHTML = `
        <tr class="table-primary text-center align-middle">
            <th>ID</th>
            <th>Título</th>
            <th>Nº página</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
    `;
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    tbody.classList.add("tbody", "table-group-divider");

    const tipoMap = {
        radio: "Radio",
        numberInput: "Entrada numérica",
        checkbox: "Checkbox",
        formSelect: "Radio desplegable",
        matrix1: "Matriz con rango",
        matrix2: "Matriz simple",
        matrix3: "Matriz doble",
    };

    data.forEach(pregunta => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <th scope="row" class="align-middle text-center fw-bold text-primary">${pregunta.id}</th>
            <td class="align-middle">${pregunta.titulo}</td>
            <td class="align-middle text-center">${pregunta.n_pag}</td>
            <td class="align-middle text-center">${tipoMap[pregunta.tipo]}</td>
            <td class="align-middle d-flex justify-content-center align-items-center gap-2">
                <button 
                    class="btn btn-sm btn-warning" 
                    onclick="editarPregunta(${pregunta.id})"
                    title="Editar pregunta"
                >
                    <i class="fas fa-edit"></i>
                </button>
                <button 
                    type="button" 
                    class="btn btn-danger btn-sm" 
                    onclick="confirmarBorrarPregunta(${pregunta.id})"
                >
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });

    table.appendChild(tbody);
    return table;
}

// Función para confirmar el borrado de una pregunta
function confirmarBorrarPregunta(id) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.onclick = function () {
        borrarPregunta(id);
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const modalInstance = bootstrap.Modal.getInstance(confirmDeleteModal);
        modalInstance.hide();
    };
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    confirmDeleteModal.show();
}

// Función para borrar una pregunta
function borrarPregunta(id) {
    fetch(`includesCP/borrarPregunta.php?id=${id}`, { method: "DELETE" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                cargarPreguntas(); // Recargar la lista de preguntas
                showToast("Pregunta eliminada correctamente.", "success");
            } else {
                showToast("Error al eliminar la pregunta.", "danger");
            }
        })
        .catch(error => {
            console.error("Error al borrar la pregunta:", error);
            showToast("Ocurrió un error al intentar borrar la pregunta. Inténtalo de nuevo más tarde.", "danger");
        });
}

// Función para buscar preguntas
function buscarPregunta() {
    const searchTerm = document.getElementById("searchQuestions").value.toLowerCase();
    const tableBody = document.querySelector("#preguntasList tbody");
    const rows = tableBody.querySelectorAll("tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        let matchFound = false;

        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                matchFound = true;
            }
        });

        // Mostrar u ocultar la fila según el resultado de la búsqueda
        row.style.display = matchFound || !searchTerm ? "" : "none";
    });
}
// Cargar preguntas al cargar la página
document.addEventListener("DOMContentLoaded", cargarPreguntas);

