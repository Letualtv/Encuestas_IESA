// Variables globales
let currentPage = 1; // Página actual de claves
const pageSize = 30; // Número de claves por página
const selectedIds = new Set(); // Almacenar los IDGrupo seleccionados

// Función para cargar claves desde el backend
function cargarClaves(page = 1, limit = pageSize, orderBy = "IDGrupo", orderDir = "asc") {
    fetch(`includesCP/poblacionDB.php?action=obtenerClaves&page=${page}&limit=${limit}&orderBy=${orderBy}&orderDir=${orderDir}`)
        .then((response) => {
            if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            const clavesList = document.getElementById("clavesList");

            // Validar que data sea un array
            if (!Array.isArray(data)) {
                console.error("La respuesta del servidor no es un array:", data);
                showToast(data.message || "Ocurrió un error al cargar las claves.", "danger");
                return;
            }

            if (page === 1) clavesList.innerHTML = ""; // Limpiar la lista si es la primera página

            if (data.length === 0) {
                clavesList.innerHTML += "<tr><td colspan='5'>No hay más claves disponibles.</td></tr>";
                document.getElementById("loadMoreButton").disabled = true;
                return;
            }

            data.forEach((clave) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td><input type="checkbox" value="${clave.IDGrupo}" class="claveCheckbox"></td>
                    <td>${clave.IDGrupo}</td>
                    <td>${clave.clave}</td>
                    <td>${clave.n_login || "No asignado"}</td>
                    <td>${clave.terminada ? "Sí" : "No"}</td>
                `;
                clavesList.appendChild(row);
            });
        })
        .catch((error) => {
            console.error("Error al cargar las claves:", error);
            showToast(error.message || "Ocurrió un error al cargar las claves.", "danger");
        });
}


document.addEventListener("DOMContentLoaded", () => {
    const deleteAllRowsButton = document.getElementById("deleteAllRowsButton");

    if (deleteAllRowsButton) {
        deleteAllRowsButton.addEventListener("click", () => {
            showModal(
                "Eliminar TODAS las filas",
                `<div class="text-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡ADVERTENCIA!</strong>
                </div>
                <p class="mt-2">¿Estás seguro de eliminar <b>todas las filas</b>?</p>
                <p class="mt-2">Esta acción no se puede deshacer.</p>`,
                () => {
                    // Segunda confirmación: Última advertencia
                    showModal(
                        "Confirmación Final",
                        `<div class="text-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡ADVERTENCIA FINAL!</strong>
                        </div>
                        <p class="mt-2">¿Estás completamente seguro de eliminar <b>todas las filas</b>?</p>`,
                        async () => {
                            try {
                                // Deshabilitar el botón mientras se elimina
                                deleteAllRowsButton.disabled = true;
                                deleteAllRowsButton.textContent = "Eliminando...";

                                // Enviar solicitud al backend
                                const response = await fetch("includesCP/poblacionDB.php?action=eliminarClavesSeleccionadas", {
                                    method: "DELETE",
                                    headers: { "Content-Type": "application/json" },
                                    body: JSON.stringify({ ids: "all" }),
                                });

                                if (!response.ok) {
                                    throw new Error(`Error en la solicitud: ${response.status}`);
                                }

                                const data = await response.json();

                                if (data.success) {
                                    showToast("TODAS las claves han sido eliminadas correctamente.", "success");
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000); // Espera 1 segundo antes de recargar
                                } else {
                                    showToast(data.message || "Error al eliminar las claves.", "danger");
                                }
                            } catch (error) {
                                console.error("Error al eliminar las claves:", error);
                                showToast("Ocurrió un error al intentar eliminar las claves.", "danger");
                            } finally {
                                // Restaurar el estado del botón
                                deleteAllRowsButton.disabled = false;
                                deleteAllRowsButton.textContent = "Eliminar TODAS las filas";
                            }
                        }
                    );
                }
            );
        });
    }
});

// Función para mostrar modales de confirmación
function showModal(title, message, onConfirm) {
    const modal = document.createElement("div");
    modal.className = "modal fade";
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">${message}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmButton">Confirmar</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    const confirmButton = modal.querySelector("#confirmButton");
    confirmButton.addEventListener("click", () => {
        onConfirm();
        bsModal.hide();
        modal.remove();
    });

    modal.addEventListener("hidden.bs.modal", () => {
        modal.remove();
    });
}




// Generar claves
document.addEventListener("DOMContentLoaded", () => {
    const randomKeyForm = document.getElementById("randomKeyForm");
    const customKeyForm = document.getElementById("customKeyForm");

    // Elementos relacionados con el ID Base
    const idBaseInput = document.getElementById("idBase");
    const useCustomIdGroupCheckbox = document.getElementById("useCustomIdGroupCheckbox");

    // Establecer el valor predeterminado del campo ID Base
    idBaseInput.value = "1"; // Valor predeterminado
    idBaseInput.style.display = "none"; // Ocultar el campo por defecto

    // Mostrar/ocultar el campo de ID Base según el estado del checkbox
    useCustomIdGroupCheckbox.addEventListener("change", function () {
        if (this.checked) {
            idBaseInput.style.display = "block";
        } else {
            idBaseInput.style.display = "none";
            idBaseInput.value = "1"; // Restablecer al valor predeterminado
        }
    });

    if (randomKeyForm) {
        randomKeyForm.addEventListener("submit", async function (event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto

            const randomKeyCountInput = document.getElementById("randomKeyCount");

            let idBase = parseInt(idBaseInput.value.trim(), 10) || 1; // Valor predeterminado: 1
            const cantidadClaves = parseInt(randomKeyCountInput.value.trim(), 10);

            // Validar que el ID Base sea un número positivo
            if (isNaN(idBase) || idBase <= 0) {
                showToast("El ID Base debe ser un número positivo.", "warning");
                return;
            }

            // Validar que la cantidad de claves sea válida
            if (isNaN(cantidadClaves) || cantidadClaves <= 0 || cantidadClaves > 10000) {
                showToast("La cantidad de claves debe estar entre 1 y 10,000.", "warning");
                return;
            }

            // Mostrar el primer modal de confirmación
            showModal(
                "Confirmación",
                `¿Estás seguro de generar ${cantidadClaves} claves aleatorias?`,
                async () => {
                    // Si la cantidad es mayor a 1000, mostrar el segundo modal de confirmación
                    if (cantidadClaves > 1000) {
                        showModal(
                            "Confirmación Final",
                            `<p class="text-danger fw-bold">¡ADVERTENCIA!</p> <p>Vas a <b>generar ${cantidadClaves} claves</b>. ¿Estás completamente seguro?</p>`,
                            async () => {
                                // Deshabilitar el botón mientras se genera
                                const generateKeysButton = randomKeyForm.querySelector("button[type='submit']");
                                generateKeysButton.disabled = true;
                                generateKeysButton.textContent = "Generando...";

                                try {
                                    // Enviar la solicitud al backend
                                    const response = await fetch("includesCP/poblacionDB.php?action=generarClavesAleatorias", {
                                        method: "POST",
                                        headers: { "Content-Type": "application/json" },
                                        body: JSON.stringify({ cantidad: cantidadClaves, idBase }),
                                    });

                                    if (!response.ok) {
                                        throw new Error(`Error en la solicitud: ${response.status}`);
                                    }

                                    const data = await response.json();

                                    if (data.success) {
                                        showToast(data.message, "success");
                                        cargarClaves(currentPage); // Recargar la lista de claves
                                    } else {
                                        showToast(data.message || "Error al generar las claves.", "danger");
                                    }
                                } catch (error) {
                                    console.error("Error al generar las claves:", error);
                                    showToast("Ocurrió un error al intentar generar las claves.", "danger");
                                } finally {
                                    // Restaurar el estado del botón
                                    generateKeysButton.disabled = false;
                                    generateKeysButton.textContent = "Generar claves";
                                }
                            },
                            null,
                            "Aceptar",
                            "Cancelar"
                        );
                    } else {
                        // Si la cantidad es menor o igual a 1000, proceder directamente
                        const generateKeysButton = randomKeyForm.querySelector("button[type='submit']");
                        generateKeysButton.disabled = true;
                        generateKeysButton.textContent = "Generando...";

                        try {
                            // Enviar la solicitud al backend
                            const response = await fetch("includesCP/poblacionDB.php?action=generarClavesAleatorias", {
                                method: "POST",
                                headers: { "Content-Type": "application/json" },
                                body: JSON.stringify({ cantidad: cantidadClaves, idBase }),
                            });

                            if (!response.ok) {
                                throw new Error(`Error en la solicitud: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                showToast(data.message, "success");
                                cargarClaves(currentPage); // Recargar la lista de claves
                            } else {
                                showToast(data.message || "Error al generar las claves.", "danger");
                            }
                        } catch (error) {
                            console.error("Error al generar las claves:", error);
                            showToast("Ocurrió un error al intentar generar las claves.", "danger");
                        } finally {
                            // Restaurar el estado del botón
                            generateKeysButton.disabled = false;
                            generateKeysButton.textContent = "Generar claves";
                        }
                    }
                },
                null,
                "Aceptar",
                "Cancelar"
            );
        });
    }

    if (customKeyForm) {
        customKeyForm.addEventListener("submit", async function (event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto

            const customKeyIdInput = document.getElementById("customKeyId");
            const customKeyInput = document.getElementById("customKey");

            let idBase = parseInt(customKeyIdInput.value.trim(), 10) || 1; // Valor predeterminado: 1
            const clave = customKeyInput.value.trim();

            // Validar que la clave tenga exactamente 5 caracteres alfanuméricos
            if (!/^[a-zA-Z0-9]{5}$/.test(clave)) {
                showToast("La clave debe tener exactamente 5 caracteres alfanuméricos.", "warning");
                return;
            }

            // Validar que el ID base sea un número positivo
            if (isNaN(idBase) || idBase <= 0) {
                showToast("El ID Base debe ser un número positivo.", "warning");
                return;
            }

            // Enviar la solicitud al backend
            try {
                const response = await fetch("includesCP/poblacionDB.php?action=agregarClave", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ clave, idBase }),
                });

                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showToast("Clave agregada correctamente.", "success");
                    cargarClaves(currentPage); // Recargar la lista de claves
                    customKeyIdInput.value = ""; // Limpiar el campo de ID base
                    customKeyInput.value = ""; // Limpiar el campo de clave
                } else {
                    showToast(data.message || "Error al agregar la clave.", "danger");
                }
            } catch (error) {
                console.error("Error al agregar la clave:", error);
                showToast("Ocurrió un error al intentar agregar la clave.", "danger");
            }
        });
    }

    // Cargar las claves iniciales
    cargarClaves();
});