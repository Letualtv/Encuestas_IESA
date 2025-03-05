// Variables globales
let currentPage = 1; // Página actual de claves
const pageSize = 15; // Número de claves por página
let selectedIds = new Set(); // Almacenar los IDs seleccionados

// Evento para ordenar la tabla al hacer clic en los encabezados
document.querySelectorAll("#clavesTable th").forEach((header) => {
  header.addEventListener("click", () => {
    const orderBy = header.textContent.trim().toLowerCase(); // Columna seleccionada
    const orderDir = header.dataset.order === "asc" ? "desc" : "asc"; // Alternar dirección
    header.dataset.order = orderDir;

    cargarClaves(1, pageSize, orderBy, orderDir); // Recargar la tabla con la nueva ordenación
  });
});

// Función para cargar claves desde el backend
function cargarClaves(page = 1, limit = pageSize, orderBy = "id", orderDir = "asc") {
  fetch(`includesCP/poblacionDB.php?action=obtenerClaves&page=${page}&limit=${limit}&orderBy=${orderBy}&orderDir=${orderDir}`)
    .then((response) => {
      if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
      return response.json();
    })
    .then((data) => {
      const clavesList = document.getElementById("clavesList");
      if (page === 1) clavesList.innerHTML = ""; // Limpiar la lista si es la primera página

      if (data.error) throw new Error(data.error); // Manejar errores específicos del backend
      if (data.length === 0) {
        clavesList.innerHTML += " No hay más claves disponibles.";
        document.getElementById("loadMoreButton").disabled = true;
        return;
      }

      data.forEach((clave) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td><input type="checkbox" class="claveCheckbox" value="${clave.id}"></td>
          <td>${clave.id}</td>
          <td>${clave.clave}</td>
          <td>${clave.terminada ? "Sí" : "No"}</td>
        `;
        clavesList.appendChild(row);
      });

      // Restaurar el estado de los checkboxes
      restoreCheckboxState();
    })
    .catch((error) => {
      console.error("Error al cargar las claves:", error);
      showToast(`Error: ${error.message || "Ocurrió un problema al cargar las claves."}`, "danger");
    });
}

// Función para agregar una clave manual
document.getElementById("customKeyForm").addEventListener("submit", function (event) {
  event.preventDefault(); // Evitar el envío del formulario por defecto

  const customKeyInput = document.getElementById("customKey");
  const clave = customKeyInput.value.trim();

  // Validar que la clave tenga exactamente 5 caracteres alfanuméricos
  if (!/^[a-zA-Z0-9]{5}$/.test(clave)) {
    showToast("La clave debe tener exactamente 5 caracteres alfanuméricos.", "warning");
    return;
  }

  fetch("includesCP/poblacionDB.php?action=agregarClave", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ clave }), // Enviar solo la clave
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error en la solicitud: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        showToast("Clave agregada correctamente.", "success");
        cargarClaves(currentPage); // Recargar la lista de claves
        customKeyInput.value = ""; // Limpiar el campo de entrada
      } else {
        showToast(data.message || "Error al agregar la clave.", "danger");
      }
    })
    .catch((error) => {
      console.error("Error al agregar la clave:", error);
      showToast("Ocurrió un error al intentar agregar la clave.", "danger");
    });
});



document.getElementById("randomKeyForm").addEventListener("submit", function (event) {
  event.preventDefault(); // Evitar el envío del formulario por defecto

  const randomKeyCountInput = document.getElementById("randomKeyCount");
  const cantidad = parseInt(randomKeyCountInput.value.trim(), 10);

  // Validar que la cantidad sea válida
  if (isNaN(cantidad) || cantidad < 1 || cantidad > 10000) {
    showToast("La cantidad debe estar entre 1 y 10,000.", "warning");
    return;
  }

  // Mostrar el primer modal de confirmación
  showModal(
    "Generar Claves Aleatorias",
    `¿Estás seguro de generar ${cantidad} claves aleatorias?`,
    () => {
      // Si la cantidad es mayor a 1000, mostrar el segundo modal de confirmación
      if (cantidad > 1000) {
        showModal(
          "Confirmación Final",
          `Estás a punto de generar ${cantidad} claves. ¿Estás completamente seguro?`,
          () => {
            generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
          }
        );
      } else {
        generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
      }
    }
  );
});
// Función para generar claves aleatorias
function generarClavesAleatorias(cantidad) {
    fetch("includesCP/poblacionDB.php?action=generarClavesAleatorias", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cantidad }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                showToast(data.message, "success");
                cargarClaves(currentPage); // Recargar la lista de claves
            } else {
                showToast(data.message || "Error al generar las claves.", "danger");
            }
        })
        .catch((error) => {
            console.error("Error al generar las claves:", error);
            showToast("Ocurrió un error al intentar generar las claves.", "danger");
        });
}



// Restaurar el estado de los checkboxes después de cargar la tabla
function restoreCheckboxState() {
  document.querySelectorAll("#clavesList input[type='checkbox']").forEach((checkbox) => {
    if (selectedIds.has(checkbox.value)) {
      checkbox.checked = true;
      checkbox.closest("tr").classList.add("table-active");
    } else {
      checkbox.checked = false;
      checkbox.closest("tr").classList.remove("table-active");
    }
  });
}

// Guardar el estado de los checkboxes cuando cambian
document.addEventListener("change", (event) => {
  if (event.target.classList.contains("claveCheckbox")) {
    const id = event.target.value;
    const row = event.target.closest("tr");

    if (event.target.checked) {
      selectedIds.add(id);
      row.classList.add("table-active");
    } else {
      selectedIds.delete(id);
      row.classList.remove("table-active");
    }
  }
});

// Seleccionar/deseleccionar todas las filas
document.getElementById("selectAllCheckboxes").addEventListener("change", function () {
  const checkboxes = document.querySelectorAll("#clavesList input[type='checkbox']");
  checkboxes.forEach((checkbox) => {
    checkbox.checked = this.checked;
    const row = checkbox.closest("tr");

    if (this.checked) {
      selectedIds.add(checkbox.value);
      row.classList.add("table-active");
    } else {
      selectedIds.delete(checkbox.value);
      row.classList.remove("table-active");
    }
  });
});

// Modal Dinámico
function showModal(title, bodyText, confirmAction, cancelAction = null, headerClasses = ["bg-primary"]) {
  const modal = new bootstrap.Modal(document.getElementById("customModal"));

  // Configurar el título
  document.getElementById("customModalTitle").textContent = title;

  // Configurar el cuerpo del modal
  document.getElementById("customModalBody").innerHTML = bodyText;

  // Aplicar clases al encabezado
  const modalHeader = document.getElementById("customModalHeader");
  modalHeader.className = "modal-header"; // Resetear clases previas

  // Agregar cada clase individualmente
  headerClasses.forEach(clase => {
    modalHeader.classList.add(clase);
  });

  // Configurar botones
  document.getElementById("customModalConfirmButton").onclick = confirmAction;
  document.getElementById("customModalCancelButton").onclick = cancelAction || (() => {});

  modal.show();
}

// Botón para eliminar TODAS las filas
document.getElementById("deleteAllRowsButton").addEventListener("click", () => {
  // Primera confirmación: Advertencia inicial
  showModal(
    "Eliminar todas las filas",
    `
      <div class="text-danger">
        ¿Estás seguro de que deseas <b> eliminar todas </b> las filas?
      </div>
      <p class="mt-2">Esta acción no se puede deshacer.</p>
    `,
    () => {
      // Segunda confirmación: Última advertencia
      showModal(
        "Confirmación final",
        `
          <div class="text-danger d-flex align-items-center">
        <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡ADVERTENCIA!</strong>
          </div>
          <p class="mt-2">¿Estás completamente seguro de eliminar <b>todas las filas</b>?</p>
        `,
        () => {
          // Proceder con la eliminación
          fetch("includesCP/poblacionDB.php?action=eliminarClavesSeleccionadas", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids: "all" }),
          })
            .then((response) => {
              if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
              return response.json();
            })
            .then((data) => {
              if (data.success) {
                showToast("Todas las filas han sido eliminadas correctamente.", "success");
                cargarClaves(1); // Recargar la lista
              } else {
                showToast(data.message || "Error al eliminar las filas.", "danger");
              }
            })
            .catch((error) => {
              console.error("Error al eliminar las filas:", error);
              showToast("Ocurrió un error al intentar eliminar las filas.", "danger");
            });
        },
        null,
        ["bg-danger", "text-white"] // Clases para el segundo modal
      );
    },
    null,
    ["bg-danger", "text-white"] // Clases para el primer modal
  );
});

// Botón para marcar TODAS las claves como terminadas/no terminadas
let markAllAsCompleted = true; // Estado inicial: "terminadas"
document.getElementById("markAllAsCompletedButton").addEventListener("click", () => {
  const newState = markAllAsCompleted ? "terminadas" : "no terminadas";
  showModal(
    `Marcar todas las Claves como ${newState}`,
    `¿Estás seguro de que deseas marcar todas las claves como ${newState}?`,
    () => {
      fetch("includesCP/poblacionDB.php?action=editarClave", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: "all", terminada: markAllAsCompleted ? 1 : 0 }),
      })
        .then((response) => {
          if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showToast(`Todas las claves han sido marcadas como ${newState}.`, "success");
            cargarClaves(1); // Recargar la lista de claves
          } else {
            showToast(data.message || "Error al actualizar las claves.", "danger");
          }
        })
        .catch((error) => {
          console.error("Error al actualizar las claves:", error);
          showToast("Ocurrió un error al intentar actualizar las claves.", "danger");
        })
        .finally(() => {
          markAllAsCompleted = !markAllAsCompleted; // Alternar el estado
        });
    }
  );
});



// Botón para cargar más claves
document.getElementById("loadMoreButton").addEventListener("click", () => {
  currentPage++;
  cargarClaves(currentPage);
});

// Cargar las primeras claves al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  cargarClaves();
});