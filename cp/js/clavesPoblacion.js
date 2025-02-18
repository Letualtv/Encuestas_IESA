// Variables globales
let currentPage = 1; // Página actual de claves
const pageSize = 30; // Número de claves por página

// Función para cargar claves desde el backend
function cargarClaves(page = 1, limit = pageSize) {
    fetch(`includesCP/poblacion.php?accion=obtenerClaves&page=${page}&limit=${limit}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            const clavesList = document.getElementById("clavesList");
            if (page === 1) {
                clavesList.innerHTML = ""; // Limpiar la lista si es la primera página
            }

            if (data.error) {
                throw new Error(data.error); // Manejar errores específicos del backend
            }

            if (data.length === 0) {
                clavesList.innerHTML += "<tr><td colspan='4'>No hay más claves disponibles.</td></tr>";
                document.getElementById("loadMoreButton").disabled = true;
                return;
            }

            data.forEach((clave) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${clave.id}</td>
                    <td>${clave.clave}</td>
                    <td>${clave.terminada ? "Sí" : "No"}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="abrirModalEditarClave(${clave.id}, '${clave.clave}', ${clave.terminada})">
                    <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarClave(${clave.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                clavesList.appendChild(row);
            });
        })
        .catch((error) => {
            console.error("Error al cargar las claves:", error);
            showToast(`Error: ${error.message || "Ocurrió un problema al cargar las claves."}`, "danger");
        });
}

// Función para abrir el modal de edición de clave
function abrirModalEditarClave(id, clave, terminada) {
    document.getElementById("editClaveId").value = id;
    document.getElementById("editClaveValue").value = clave;
    document.getElementById("editClaveTerminada").value = terminada ? "1" : "0";

    const editClaveModal = new bootstrap.Modal(document.getElementById("editClaveModal"));
    editClaveModal.show();
}

// Función para guardar los cambios de una clave
document.getElementById("saveEditClaveButton").addEventListener("click", () => {
    const id = document.getElementById("editClaveId").value;
    const clave = document.getElementById("editClaveValue").value.trim();
    const terminada = document.getElementById("editClaveTerminada").value;

    if (!clave || clave.length > 5) {
        showToast("La clave debe tener entre 1 y 5 caracteres.", "warning");
        return;
    }

    fetch(`includesCP/poblacion.php?accion=editarClave`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, clave, terminada }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                showToast("Clave actualizada correctamente.", "success");
                cargarClaves(currentPage); // Recargar la lista de claves
                const editClaveModal = bootstrap.Modal.getInstance(document.getElementById("editClaveModal"));
                editClaveModal.hide(); // Cerrar el modal
            } else {
                showToast(data.message || "Error al actualizar la clave.", "danger");
            }
        })
        .catch((error) => {
            console.error("Error al actualizar la clave:", error);
            showToast("Ocurrió un error al intentar actualizar la clave.", "danger");
        });
});

// Función para buscar claves
document.getElementById("searchClaves").addEventListener("input", () => {
    const searchTerm = document.getElementById("searchClaves").value.toLowerCase();
    const rows = document.querySelectorAll("#clavesList tr");

    rows.forEach((row) => {
        const cells = row.querySelectorAll("td");
        let matchFound = false;

        cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                matchFound = true;
            }
        });

        row.style.display = matchFound ? "" : "none";
    });
});

// Función para eliminar una clave individual
function eliminarClave(id) {
    showConfirmationModal(
        "Confirmar eliminación",
        "¿Estás seguro de que deseas eliminar esta clave?",
        () => {
            fetch(`includesCP/poblacion.php?accion=eliminarClave&id=${id}`, { method: "DELETE" })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`Error en la solicitud: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        showToast("Clave eliminada correctamente.", "success");
                        cargarClaves(currentPage); // Recargar la lista de claves
                    } else {
                        showToast(data.message || "Error al eliminar la clave.", "danger");
                    }
                })
                .catch((error) => {
                    console.error("Error al eliminar la clave:", error);
                    showToast("Ocurrió un error al intentar eliminar la clave.", "danger");
                });
        }
    );
}

// Cargar las primeras claves al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarClaves();
});