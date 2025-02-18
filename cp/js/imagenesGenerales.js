// Función para cargar las imágenes disponibles en la lista
function cargarImagenes() {
    const imagenesList = document.getElementById("encuestaImagesList");
    imagenesList.innerHTML = ""; // Limpiar la lista

    fetch("includesCP/obtenerImagenes.php", { method: "GET" }) // Asegúrate de que la ruta sea correcta
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.error) {
                throw new Error(data.error); // Manejar errores específicos del backend
            }

            if (data.length === 0) {
                imagenesList.innerHTML = "<p>No hay imágenes disponibles.</p>";
                showToast("No hay imágenes disponibles.", "warning");
                return;
            }

            data.forEach((imagen) => {
                const listItem = document.createElement("div");
                listItem.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");

                listItem.innerHTML = `
                    <div>
                        <strong>${imagen.nombre}</strong> (${imagen.tamaño} KB)
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="abrirModalEditarNombre('${imagen.nombre}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger me-2" onclick="confirmarBorrarImagen('${imagen.nombre}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="previsualizarImagen('${imagen.nombre}')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                `;
                imagenesList.appendChild(listItem);
            });
        })
        .catch((error) => {
            console.error("Error al cargar las imágenes:", error);
            imagenesList.innerHTML = `<p>Error: ${error.message || "Ocurrió un problema al cargar las imágenes."}</p>`;
            showToast(`Error al cargar las imágenes: ${error.message || "Ocurrió un problema."}`, "danger");
        });
}
document.getElementById("imageUploadForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío tradicional del formulario

    const formData = new FormData();
    const fileInput = document.getElementById("encuestaImage");
    const file = fileInput.files[0]; // Obtener el archivo seleccionado
    const submitButton = document.querySelector("#imageUploadForm button[type='submit']");

    if (!file) {
        showToast("Selecciona una imagen para subir.", "warning");
        return;
    }

    // Deshabilitar el botón mientras se sube la imagen
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Subiendo...';

    // Agregar el archivo al FormData
    formData.append("imagen", file);

    // Enviar la solicitud al backend
    fetch("includesCP/obtenerImagenes.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                showToast("Imagen subida correctamente.", "success");
                cargarImagenes(); // Recargar la lista de imágenes
                fileInput.value = ""; // Limpiar el campo de archivo
            } else {
                showToast(data.message || "Error al subir la imagen.", "danger");
            }
        })
        .catch((error) => {
            console.error("Error al subir la imagen:", error);
            showToast(`Error: ${error.message || "Ocurrió un problema al subir la imagen."}`, "danger");
        })
        .finally(() => {
            // Habilitar el botón nuevamente
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fa-solid fa-upload me-2"></i>Subir imagen';
        });
});

// Función para previsualizar una imagen
function previsualizarImagen(nombreImagen) {
    const modalBody = document.querySelector("#previewImageModal .modal-body");
    const modalTitle = document.querySelector("#previewImageModal .modal-title");
    const imageUrl = `../assets/img/${nombreImagen}`; // Ajusta la ruta según tu estructura

    modalTitle.textContent = `Previsualización: ${nombreImagen}`;
    modalBody.innerHTML = `<img src="${imageUrl}" class="img-fluid" alt="${nombreImagen}">`;

    const previewImageModal = new bootstrap.Modal(document.getElementById("previewImageModal"));
    previewImageModal.show();
}

// Función para abrir el modal de edición del nombre
function abrirModalEditarNombre(nombreActual) {
    const modal = new bootstrap.Modal(document.getElementById("editImageNameModal"));
    const inputField = document.getElementById("newImageName");
    const saveButton = document.getElementById("saveImageNameButton");

    // Rellenar el campo con el nombre actual
    inputField.value = nombreActual;

    // Configurar la acción del botón "Guardar"
    saveButton.onclick = function () {
        const nuevoNombre = inputField.value.trim();

        if (!nuevoNombre || nuevoNombre === nombreActual) {
            showToast("El nombre no puede estar vacío ni ser igual al actual.", "warning");
            return;
        }

        // Llamar a la función para editar el nombre
        editarNombreImagen(nombreActual, nuevoNombre);

        // Cerrar el modal
        modal.hide();
    };

    // Mostrar el modal
    modal.show();
}

// Función para editar el nombre de una imagen
function editarNombreImagen(id, nuevoNombre) {
    fetch("includesCP/obtenerImagenes.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&nuevoNombre=${encodeURIComponent(nuevoNombre)}`,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                showToast("Nombre de la imagen actualizado correctamente.", "success");
                cargarImagenes(); // Recargar la lista de imágenes
            } else {
                showToast(data.message || "Error al actualizar el nombre de la imagen.", "danger");
            }
        })
        .catch((error) => {
            console.error("Error al actualizar el nombre de la imagen:", error);
            showToast(`Error: ${error.message || "Ocurrió un problema al actualizar el nombre de la imagen."}`, "danger");
        });
}

// Función para confirmar el borrado de una imagen
function confirmarBorrarImagen(id) {
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
    document.getElementById("confirmDeleteButton").onclick = function () {
        borrarImagen(id);
        confirmDeleteModal.hide();
    };
    document.querySelector("#confirmDeleteModal .modal-body").textContent = `¿Estás seguro de que deseas borrar la imagen "${id}"?`;
    confirmDeleteModal.show();
}

// Función para borrar una imagen
function borrarImagen(id) {
    fetch(`includesCP/obtenerImagenes.php?id=${encodeURIComponent(id)}`, { method: "DELETE" })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                showToast("Imagen eliminada correctamente.", "success");
                cargarImagenes(); // Recargar la lista de imágenes
            } else {
                showToast(data.message || "Error al eliminar la imagen.", "danger");
            }
        })
        .catch((error) => {
            console.error("Error al borrar la imagen:", error);
            showToast(`Error: ${error.message || "Ocurrió un problema al eliminar la imagen."}`, "danger");
        });
}