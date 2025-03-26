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

// Función para mostrar mensajes toast
function showToast(message, type = "info") {
    const toastContainer = document.getElementById("toastContainer");

    if (!toastContainer) {
        console.error("El contenedor de toasts no existe en el DOM.");
        return;
    }

    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    setTimeout(() => {
        toast.remove();
    }, 3500);
}
// Generar claves específicas
document.getElementById("customKeyForm").addEventListener("submit", function (event) {
  event.preventDefault(); // Evitar el envío del formulario por defecto

  const customKeyIdInput = document.getElementById("customKeyId");
  const customKeyInput = document.getElementById("customKey");

  const idBase = customKeyIdInput.value.trim();
  const clave = customKeyInput.value.trim();

  // Validar que la clave tenga exactamente 5 caracteres alfanuméricos
  if (!/^[a-zA-Z0-9]{5}$/.test(clave)) {
      showToast("La clave debe tener exactamente 5 caracteres alfanuméricos.", "warning");
      return;
  }

  // Validar que el ID base sea un número positivo
  if (idBase && !/^\d+$/.test(idBase)) {
      showToast("El ID Base debe ser un número positivo.", "warning");
      return;
  }

  // Enviar la solicitud al backend
  fetch("includesCP/poblacionDB.php?action=agregarClave", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ clave, idBase }), // Enviar clave e ID base
  })
      .then((response) => {
          if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
          return response.json();
      })
      .then((data) => {
          if (data.success) {
              showToast("Clave agregada correctamente.", "success");
              cargarClaves(currentPage); // Recargar la lista de claves
              customKeyIdInput.value = ""; // Limpiar el campo de ID base
              customKeyInput.value = ""; // Limpiar el campo de clave
          } else {
              showToast(data.message || "Error al agregar la clave.", "danger");
          }
      })
      .catch((error) => {
          console.error("Error al agregar la clave:", error);
          showToast("Ocurrió un error al intentar agregar la clave.", "danger");
      });
});
// Generar claves aleatorias
document.addEventListener("DOMContentLoaded", () => {
    const randomKeyForm = document.getElementById("randomKeyForm");
    if (!randomKeyForm) {
        console.error("El formulario de generación de claves aleatorias no está presente en el DOM.");
        return;
    }

    randomKeyForm.addEventListener("submit", async function (event) {
        event.preventDefault(); // Evitar el envío del formulario por defecto

        const idBaseInput = document.getElementById("idBase");
        const randomKeyCountInput = document.getElementById("randomKeyCount");

        const idBase = idBaseInput.value.trim();
        const cantidadClaves = parseInt(randomKeyCountInput.value.trim(), 10);

        // Validar que el ID Base sea un número positivo
        if (!/^\d+$/.test(idBase) || parseInt(idBase, 10) <= 0) {
            showToast("El ID Base debe ser un número positivo.", "warning");
            return;
        }

        // Validar que la cantidad de claves sea válida
        if (isNaN(cantidadClaves) || cantidadClaves <= 0 || cantidadClaves > 10000) {
            showToast("La cantidad de claves debe estar entre 1 y 10,000.", "warning");
            return;
        }

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
    });

    // Cargar las claves iniciales
    cargarClaves();
});
