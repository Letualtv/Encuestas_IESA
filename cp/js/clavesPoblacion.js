// Variables globales
let currentPage = 1; // Página actual de claves
const pageSize = 20; // Número de claves por página
document.querySelectorAll("#clavesTable th").forEach((header, index) => {
  header.addEventListener("click", () => {
    const orderBy = header.textContent.trim().toLowerCase(); // Columna seleccionada
    const orderDir = header.dataset.order === "asc" ? "desc" : "asc"; // Alternar dirección
    header.dataset.order = orderDir;

    cargarClaves(1, pageSize, orderBy, orderDir); // Recargar la tabla con la nueva ordenación
  });
});

// Modificar la función cargarClaves para aceptar orderBy y orderDir
function cargarClaves(
  page = 1,
  limit = pageSize,
  orderBy = "id",
  orderDir = "asc"
) {
  fetch(
    `includesCP/poblacionDB.php?action=obtenerClaves&page=${page}&limit=${limit}&orderBy=${orderBy}&orderDir=${orderDir}`
  )
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
        throw new Error(data.error);
      }

      if (data.length === 0) {
        clavesList.innerHTML +=
          "<tr><td colspan='4' class='text-center'>No hay más claves disponibles.</td></tr>";
        document.getElementById("loadMoreButton").disabled = true;
        return;
      }

      data.forEach((clave) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td><input type="checkbox" value="${
                      clave.id
                    }" class="form-check-input claveCheckbox"></td>
                    <td>${clave.id}</td>
                    <td>${clave.clave}</td>
                    <td>${clave.terminada ? "Sí" : "No"}</td>
                `;
        clavesList.appendChild(row);
      });
    })
    .catch((error) => {
      console.error("Error al cargar las claves:", error);
      showToast(
        `Error: ${
          error.message || "Ocurrió un problema al cargar las claves."
        }`,
        "danger"
      );
    });
}

// Función para buscar claves
document.getElementById("searchClaves").addEventListener("input", () => {
  const searchTerm = document
    .getElementById("searchClaves")
    .value.toLowerCase();

  fetch(`includesCP/poblacionDB.php?action=obtenerTodasClaves`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error en la solicitud: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      const clavesList = document.getElementById("clavesList");
      clavesList.innerHTML = ""; // Limpiar la lista actual

      const filteredData = data.filter(
        (clave) =>
          clave.id.toString().includes(searchTerm) ||
          clave.clave.toLowerCase().includes(searchTerm) ||
          (clave.terminada ? "sí" : "no").includes(searchTerm)
      );

      if (filteredData.length === 0) {
        clavesList.innerHTML =
          "<tr><td colspan='4' class='text-center'>No se encontraron resultados.</td></tr>";
        return;
      }

      filteredData.forEach((clave) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td><input type="checkbox" value="${
                      clave.id
                    }" class="form-check-input claveCheckbox"></td>
                    <td>${clave.id}</td>
                    <td>${clave.clave}</td>
                    <td>${clave.terminada ? "Sí" : "No"}</td>
                `;
        clavesList.appendChild(row);
      });
    })
    .catch((error) => {
      console.error("Error al buscar claves:", error);
      showToast("Ocurrió un error al intentar buscar claves.", "danger");
    });
});

// Resaltar/desresaltar filas seleccionadas
document.addEventListener("change", (event) => {
  if (event.target.classList.contains("claveCheckbox")) {
    const row = event.target.closest("tr");
    if (event.target.checked) {
      row.classList.add("table-active");
    } else {
      row.classList.remove("table-active");
    }
  }
});

// Función para seleccionar/deseleccionar todas las filas
document.getElementById("selectAllCheckboxes").addEventListener("change", function () {
    const checkboxes = document.querySelectorAll("#clavesList input[type='checkbox']");
    checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
        const row = checkbox.closest("tr");
        if (this.checked) {
            row.classList.add("table-active");
        } else {
            row.classList.remove("table-active");
        }
    });
});

// Botón para seleccionar TODAS las filas
document.getElementById("selectAllRowsButton").addEventListener("click", () => {
    const modalBody = document.querySelector("#confirmSelectAllModal .modal-body");
    modalBody.textContent = "¿Estás seguro de que deseas seleccionar TODAS las filas?";

    const confirmationModal = new bootstrap.Modal(document.getElementById("confirmSelectAllModal"));
    confirmationModal.show();

    document.getElementById("confirmSelectAllButton").onclick = () => {
        confirmationModal.hide(); // Ocultar el modal

        fetch(`includesCP/poblacionDB.php?action=obtenerTodasClaves`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.error) {
                    throw new Error(data.error);
                }

                // Marcar todos los checkboxes como seleccionados
                data.forEach((clave) => {
                    const checkbox = document.querySelector(`#clavesList input[type='checkbox'][value='${clave.id}']`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest("tr").classList.add("table-active");
                    }
                });

                showToast("Todas las filas han sido seleccionadas.", "info");
            })
            .catch((error) => {
                console.error("Error al seleccionar todas las filas:", error);
                showToast("Ocurrió un error al intentar seleccionar todas las filas.", "danger");
            });
    };
});

// Función para editar claves seleccionadas
document
  .getElementById("editSelectedButton")
  .addEventListener("click", function () {
    const selectedIds = [];
    const selectedStates = []; // Almacenar los estados actuales de las claves seleccionadas
    const checkboxes = document.querySelectorAll(
      "#clavesList input[type='checkbox']:checked"
    );

    checkboxes.forEach((checkbox) => {
      const row = checkbox.closest("tr");
      const terminadaCell = row
        .querySelector("td:nth-child(4)")
        .textContent.trim(); // Estado actual ("Sí" o "No")
      const terminadaValue = terminadaCell === "Sí" ? 1 : 0; // Convertir a 1 o 0
      selectedIds.push(checkbox.value);
      selectedStates.push(terminadaValue);
    });

    if (selectedIds.length === 0) {
      showToast(
        "Por favor, selecciona al menos una clave para editar.",
        "warning"
      );
      return;
    }

    // Determinar el nuevo estado común para todas las claves seleccionadas
    const allAreYes = selectedStates.every((state) => state === 1); // ¿Todas están en "Sí"?
    const newTerminada = allAreYes ? 0 : 1; // Si todas están en "Sí", cambiar a "No". De lo contrario, cambiar a "Sí".

    // Verificar que el modal y el .modal-body existan
    const modalBody = document.querySelector("#confirmEditModal .modal-body");
    if (!modalBody) {
      console.error(
        "El modal de confirmación o el .modal-body no están presentes en el DOM."
      );
      return;
    }

    modalBody.textContent = `¿Estás seguro de marcar ${
      selectedIds.length
    } claves como ${newTerminada ? "SÍ terminada" : "NO terminada"}?`;

    const confirmationModal = new bootstrap.Modal(
      document.getElementById("confirmEditModal")
    );
    confirmationModal.show();

    // Asignar el evento onclick al botón de confirmación
    const confirmEditButton = document.getElementById(
      "confirmEditActionButton"
    );
    if (!confirmEditButton) {
      console.error("El botón de confirmación no está presente en el DOM.");
      return;
    }

    confirmEditButton.onclick = () => {
      fetch("includesCP/poblacionDB.php?action=editarClave", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIds, terminada: newTerminada }), // Enviar el nuevo estado
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showToast("Estado de claves actualizado correctamente.", "success");
            cargarClaves(currentPage); // Recargar la lista de claves
          } else {
            showToast(
              data.message || "Error al actualizar las claves.",
              "danger"
            );
          }
        })
        .catch((error) => {
          console.error("Error al actualizar las claves:", error);
          showToast(
            "Ocurrió un error al intentar actualizar las claves.",
            "danger"
          );
        })
        .finally(() => {
          confirmationModal.hide();
        });
    };
  });
// Función para eliminar claves seleccionadas
document
  .getElementById("deleteSelectedButton")
  .addEventListener("click", function () {
    const selectedIds = [];
    const checkboxes = document.querySelectorAll(
      "#clavesList input[type='checkbox']:checked"
    );
    checkboxes.forEach((checkbox) => {
      selectedIds.push(checkbox.value);
    });

    if (selectedIds.length === 0) {
      showToast(
        "Por favor, selecciona al menos una clave para eliminar.",
        "warning"
      );
      return;
    }

    // Verificar que el modal y el .modal-body existan
    const modalBody = document.querySelector("#confirmDeleteModal .modal-body");
    if (!modalBody) {
      console.error(
        "El modal de confirmación o el .modal-body no están presentes en el DOM."
      );
      return;
    }

    modalBody.textContent = `¿Estás seguro de que deseas eliminar ${selectedIds.length} claves seleccionadas?`;

    const confirmationModal = new bootstrap.Modal(
      document.getElementById("confirmDeleteModal")
    );
    confirmationModal.show();

    // Asignar el evento onclick al botón de confirmación
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
    if (!confirmDeleteButton) {
      console.error("El botón de confirmación no está presente en el DOM.");
      return;
    }

    confirmDeleteButton.onclick = () => {
      fetch("includesCP/poblacionDB.php?action=eliminarClavesSeleccionadas", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIds }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showToast("Claves eliminadas correctamente.", "success");
            cargarClaves(currentPage);
          } else {
            showToast(
              data.message || "Error al eliminar las claves.",
              "danger"
            );
          }
        })
        .catch((error) => {
          console.error("Error al eliminar las claves:", error);
          showToast(
            "Ocurrió un error al intentar eliminar las claves.",
            "danger"
          );
        })
        .finally(() => {
          confirmationModal.hide();
        });
    };
  });

// Función para agregar una clave manual
document
  .getElementById("customKeyForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío del formulario por defecto

    const customKeyInput = document.getElementById("customKey");
    const clave = customKeyInput.value.trim();

    // Validar que la clave tenga exactamente 5 caracteres alfanuméricos
    if (!/^[a-zA-Z0-9]{5}$/.test(clave)) {
      showToast(
        "La clave debe tener exactamente 5 caracteres alfanuméricos.",
        "warning"
      );
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

// Función para generar claves aleatorias
document
  .getElementById("randomKeyForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío del formulario por defecto

    const randomKeyCountInput = document.getElementById("randomKeyCount");
    const cantidad = parseInt(randomKeyCountInput.value.trim(), 10);

    // Validar que la cantidad sea válida
    if (isNaN(cantidad) || cantidad <= 0 || cantidad > 10000) {
      showToast("La cantidad debe estar entre 1 y 10,000.", "warning");
      return;
    }

    // Mostrar el primer modal de confirmación
    const modalBody = document.querySelector(
      "#confirmGenerateModal .modal-body"
    );
    modalBody.textContent = `¿Estás seguro de generar ${cantidad} claves aleatorias?`;

    const confirmationModal = new bootstrap.Modal(
      document.getElementById("confirmGenerateModal")
    );
    confirmationModal.show();

    // Asignar el evento onclick al botón de confirmación
    const confirmGenerateButton = document.getElementById(
      "confirmGenerateButton"
    );
    confirmGenerateButton.onclick = () => {
      confirmationModal.hide(); // Ocultar el primer modal

      // Si la cantidad es mayor a 1000, mostrar el segundo modal de confirmación
      if (cantidad > 1000) {
        const finalConfirmModal = new bootstrap.Modal(
          document.getElementById("finalConfirmModal")
        );
        const finalConfirmModalBody = document.querySelector(
          "#finalConfirmModal .modal-body"
        );
        finalConfirmModalBody.textContent = `Estás a punto de generar ${cantidad} claves. ¿Estás completamente seguro?`;

        finalConfirmModal.show();

        // Asignar el evento onclick al botón de confirmación final
        const finalConfirmButton =
          document.getElementById("finalConfirmButton");
        finalConfirmButton.onclick = () => {
          generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
          finalConfirmModal.hide(); // Ocultar el segundo modal
        };
      } else {
        generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
      }
    };
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

document.getElementById("loadMoreButton").addEventListener("click", () => {
  currentPage++;
  cargarClaves(currentPage);
});

// Cargar las primeras claves al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  cargarClaves();
});
