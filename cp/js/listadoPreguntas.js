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
    table.classList.add("table", "table-bordered", "table-sm", "table-hover", "table-responsive", "align-middle", "w-100");

    const thead = document.createElement("thead");
    thead.classList.add("sticky-top");
    thead.innerHTML = `
        <tr class="table table-secondary text-center align-middle table-hover">
            <th id="th-id" class="sortable col-1" data-column="id" data-order="asc">ID <i class="fas fa-sort"></i></th>
            <th class="col-6">Título</th>
            <th id="th-n_pag" class="sortable col-1" data-column="n_pag" data-order="asc">Página <i class="fas fa-sort"></i></th>
            <th id="th-tipo" class="sortable col" data-column="tipo" data-order="asc">Tipo <i class="fas fa-sort"></i></th>
            <th class="col-auto">Acciones</th>
        </tr>
    `;
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    tbody.classList.add("tbody", "table-group-divider");

    const tipoMap = {
        radio: "Radio",
        numberInput: "Entrada numérica",
        cajaTexto: "Caja de texto",
        checkbox: "Checkbox",
        formSelect: "Radio desplegable",
        matrix1: "Matriz con rango",
        matrix2: "Matriz simple",
        matrix3: "Matriz doble",
    };

    data.forEach(pregunta => {
        let idDisplay = pregunta.id;

        // Si es una matriz, calcular el último key de las opciones
        if (['matrix1', 'matrix2'].includes(pregunta.tipo) && pregunta.opciones) {
            const keys = Object.keys(pregunta.opciones).map(key => parseInt(key, 10));
            const ultimoKey = Math.max(...keys);
            idDisplay = `${pregunta.id} - ${ultimoKey}`;
        }
        // Si es una matriz tipo matrix3, calcular el último key del último subLabel
        else if (pregunta.tipo === 'matrix3' && pregunta.opciones) {
            let ultimoKeySubLabel = 0;
            Object.values(pregunta.opciones).forEach(opcion => {
                if (opcion.subLabel) {
                    const keys = Object.keys(opcion.subLabel).map(key => parseInt(key, 10));
                    ultimoKeySubLabel = Math.max(ultimoKeySubLabel, ...keys);
                }
            });
            idDisplay = `${pregunta.id} - ${ultimoKeySubLabel}`;
        }

        const row = document.createElement("tr");
        row.setAttribute("data-pregunta-id", pregunta.id); // Agregar atributo data-pregunta-id
        row.innerHTML = `
            <th scope="row" class="align-middle text-center fw-bold text-primary">${idDisplay}</th>
            <td class="align-middle">${pregunta.titulo}</td>
            <td class="align-middle text-center">${pregunta.n_pag}</td>
            <td class="align-middle text-center">${tipoMap[pregunta.tipo]}</td>
            <td class="align-middle d-flex justify-content-center align-items-center gap-1">
                
                <button 
                    class="btn btn-sm btn-warning" 
                    onclick="editarPregunta(${pregunta.id})"
                    title="Editar pregunta"
                >
                    <i class="fas fa-edit"></i>
                </button>
                <button 
                    class="btn btn-sm btn-info" 
                    onclick="previsualizarPregunta(${pregunta.id})"
                    title="Previsualizar pregunta"
                >
                    <i class="fas fa-eye"></i>
                </button>
                <button 
                    class="btn btn-sm btn-secondary" 
                    onclick="moverPreguntaArriba(${pregunta.id})"
                    title="Mover hacia arriba"
                >
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button 
                    class="btn btn-sm btn-secondary" 
                    onclick="moverPreguntaAbajo(${pregunta.id})"
                    title="Mover hacia abajo"
                >
                    <i class="fas fa-arrow-down"></i>
                </button>
                <i class="fa-solid fa-grip-lines-vertical mx-1"></i>
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
    addSortEventListeners(table);
    return table;
}

// Función para agregar eventos de clic a los encabezados para ordenar columnas
function addSortEventListeners(table) {
    const headers = table.querySelectorAll(".sortable");
    headers.forEach(header => {
        header.addEventListener("click", () => {
            const column = header.getAttribute("data-column");
            const order = header.getAttribute("data-order");
            sortTable(table, column, order);
            header.setAttribute("data-order", order === "asc" ? "desc" : "asc");

            // Actualizar el icono de ordenación
            headers.forEach(h => {
                const icon = h.querySelector("i");
                if (h === header) {
                    icon.classList.remove("fa-sort", "fa-sort-up", "fa-sort-down");
                    icon.classList.add(order === "asc" ? "fa-sort-up" : "fa-sort-down");
                } else {
                    icon.classList.remove("fa-sort-up", "fa-sort-down");
                    icon.classList.add("fa-sort");
                }
            });
        });
    });
}

// Función para ordenar la tabla
function sortTable(table, column, order) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    rows.sort((a, b) => {
        const columnIndex = getColumnIndex(column, table);
        const cellA = a.querySelector(`th, td:nth-child(${columnIndex + 1})`).textContent.trim();
        const cellB = b.querySelector(`th, td:nth-child(${columnIndex + 1})`).textContent.trim();

        // Verificar si la columna es "id" o "n_pag" para ordenar numéricamente
        if (column === "id" || column === "n_pag") {
            return (order === "asc" ? 1 : -1) * (parseInt(cellA, 10) - parseInt(cellB, 10));
        } else {
            return (order === "asc" ? 1 : -1) * cellA.localeCompare(cellB, undefined, { numeric: true });
        }
    });

    rows.forEach(row => tbody.appendChild(row));
}

// Función para obtener el índice de la columna según el nombre
function getColumnIndex(column, table) {
    const headers = table.querySelectorAll("thead th");
    for (let i = 0; i < headers.length; i++) {
        if (headers[i].getAttribute("data-column") === column) {
            return i;
        }
    }
    return -1;
}

// Cargar preguntas al cargar la página
document.addEventListener("DOMContentLoaded", cargarPreguntas);

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

// Función para mover una pregunta hacia arriba
function moverPreguntaArriba(id) {
    fetch(`includesCP/moverPregunta.php?id=${id}&direccion=arriba`, { method: "POST" })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarPreguntas(); // Recargar la lista de preguntas
                showToast("Pregunta movida hacia arriba.", "success");
            } else {
                showToast("Error al mover la pregunta.", "danger");
            }
        })
        .catch(error => {
            console.error("Error al mover la pregunta:", error);
            showToast("Ocurrió un error al intentar mover la pregunta.", "danger");
        });
}

// Función para mover una pregunta hacia abajo
function moverPreguntaAbajo(id) {
    fetch(`includesCP/moverPregunta.php?id=${id}&direccion=abajo`, { method: "POST" })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarPreguntas(); // Recargar la lista de preguntas
                showToast("Pregunta movida hacia abajo.", "success");
            } else {
                showToast("Error al mover la pregunta.", "danger");
            }
        })
        .catch(error => {
            console.error("Error al mover la pregunta:", error);
            showToast("Ocurrió un error al intentar mover la pregunta.", "danger");
        });
}



// Función para previsualizar una pregunta
function previsualizarPregunta(id) {
    // Cerrar cualquier previsualización abierta
    const previsualizacionesAbiertas = document.querySelectorAll(".previsualizacion");
    previsualizacionesAbiertas.forEach(previsualizacion => previsualizacion.remove());

    fetch(`../views/survey/previsualizacion.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al cargar la previsualización: " + response.status);
            }
            return response.text();
        })
        .then(html => {
            const contenedorPregunta = document.querySelector(`[data-pregunta-id='${id}']`);
            if (!contenedorPregunta) {
                console.error(`El contenedor para la pregunta con ID ${id} no existe en el DOM.`);
                return;
            }

            // Crear un nuevo contenedor para la previsualización
            const contenedorPrevisualizacion = document.createElement('tr');
            contenedorPrevisualizacion.id = `previsualizacion-${id}`;
            contenedorPrevisualizacion.classList.add('previsualizacion');
            contenedorPrevisualizacion.innerHTML = `<td colspan='5'>${html}</td>`;

            contenedorPregunta.insertAdjacentElement('afterend', contenedorPrevisualizacion);
        })
        .catch(error => {
            console.error("Error al cargar la previsualización:", error);
            alert("Ocurrió un error al intentar cargar la previsualización.");
        });
}

// Cargar preguntas al cargar la página
document.addEventListener("DOMContentLoaded", cargarPreguntas);

