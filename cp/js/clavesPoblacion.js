//! Zona de peligro
////////////////////

document.addEventListener('DOMContentLoaded', () => {
  const usuarioActualEsAdmin = verificarSiEsAdministrador();
  const mensajeNoAdmin = document.getElementById('mensajeNoAdmin');
  const botonesAdmin = document.getElementById('botonesAdmin');

  // Mostrar/ocultar elementos según el rol del usuario
  if (usuarioActualEsAdmin) {
      botonesAdmin.classList.remove('d-none');
      mensajeNoAdmin.classList.add('d-none');
  } else {
      botonesAdmin.classList.add('d-none');
      mensajeNoAdmin.classList.remove('d-none');
  }
});

function verificarSiEsAdministrador() {
  const usuarioActual = JSON.parse(localStorage.getItem('usuario'));
  return usuarioActual && usuarioActual.Rol === 'administrador';
}

// Botón para eliminar TODAS las filas
document.getElementById("deleteAllRowsButton").addEventListener("click", () => {
  showModal(
    "Eliminar todas las filas",
    `¿Estás seguro de que deseas <b>eliminar todas las filas</b>?`,
    () => {
      showModal(
        "Confirmación final",
        `<p class="text-danger fw-bold">¡ADVERTENCIA!</p> <p>Vas a <b>eliminar todas las filas</b>, ésta acción <b>no se puede deshacer.</b></p>`,
        () => {
          eliminarTodasLasFilas();
        },
        null,
        ["bg-danger", "text-white"]
      );
    },
    null,
    ["bg-warning", "text-dark"]
  );
});

// Función para eliminar todas las filas
function eliminarTodasLasFilas() {
  const confirmButton = document.getElementById("customModalConfirmButton");
  const modalBody = document.getElementById("customModalBody");
  const modalHeader = document.getElementById("customModalHeader"); // Encabezado del modal
  const originalText = modalBody.innerHTML; // Guardar el texto original del modal

  // Personalizar el encabezado del modal (colores de fondo y texto)
  modalHeader.classList.add("bg-danger", "text-white"); // Fondo rojo y texto blanco
  modalHeader.textContent = "Eliminar todas las filas"; // Cambiar el título

  // Deshabilitar el botón y cambiar su texto
  confirmButton.disabled = true;
  confirmButton.textContent = "Eliminando...";
  confirmButton.classList.add("btn-danger"); // Botón en color rojo

  // Cambiar el texto del modal
  modalBody.innerHTML = "Eliminando... por favor, espere.";

  fetch("includesCP/poblacionDB.php?action=eliminarTodasLasClaves", {
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
    })
    .finally(() => {
      // Restaurar el estado del modal y el botón
      modalBody.innerHTML = originalText; // Restaurar el texto original del modal
      confirmButton.disabled = false; // Habilitar el botón nuevamente
      confirmButton.textContent = "Confirmar"; // Restaurar el texto original del botón
      confirmButton.classList.remove("btn-danger"); // Restaurar el estilo del botón
    
      // Cerrar el modal correctamente
      const modalElement = document.getElementById("customModal");
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
    
      if (modalInstance) {
        modalInstance.hide(); // Ocultar el modal
      } else {
        console.error("No se pudo obtener una instancia del modal.");
        
      }
    
      // Mover el foco al body para evitar problemas de accesibilidad
      document.body.focus();
    

      // Retrasar la recarga de la página para permitir que el toast se muestre
      setTimeout(() => {
        window.location.reload();
      }, 1500); // Esperar 2 segundos antes de recargar
    });
}

// Botón para cambiar el estado de "terminada" de todas las claves
let markAllAsCompleted = true; // Estado inicial: "terminadas"
document.getElementById("markAllAsCompletedButton").addEventListener("click", () => {
  const newState = markAllAsCompleted ? "terminadas" : "no terminadas";

  showModal(
    `Marcar todas las claves como ${newState}`,
    `¿Estás seguro de que deseas marcar <b>todas las claves como ${newState}</b>?`,
    () => {
      // Obtener el botón del modal
      const confirmButton = document.getElementById("customModalConfirmButton");
      const modalBody = document.getElementById("customModalBody");
      const originalText = modalBody.innerHTML; // Guardar el texto original del modal

      // Deshabilitar el botón y cambiar su texto
      confirmButton.disabled = true;
      confirmButton.textContent = "Marcando...";

      // Cambiar el texto del modal
      modalBody.innerHTML = "Marcando todas las claves... por favor, espere.";

      fetch("includesCP/poblacionDB.php?action=editarTodasLasClaves", {
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
          // Restaurar el estado del modal y el botón
          confirmButton.disabled = false;
          confirmButton.textContent = "Confirmar";
          modalBody.innerHTML = originalText;

          // Cerrar el modal
          const modal = bootstrap.Modal.getInstance(document.getElementById("customModal"));
          modal.hide();

          // Alternar el estado
          markAllAsCompleted = !markAllAsCompleted;
        });
    },
    null,
    ["bg-primary", "text-white"]
  );
});

////////////////////////

let currentPage = 1; // Página actual de claves
const pageSize = 15; // Número de claves por página
let selectedIds = new Set(); // Almacenar los IDs seleccionados

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
        clavesList.innerHTML += "<tr><td colspan='5'>No hay más claves disponibles.</td></tr>";
        document.getElementById("loadMoreButton").disabled = true;
        return;
      }

      data.forEach((clave) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td class=""><input type="checkbox" class="claveCheckbox form-check-input" value="${clave.id}"></td>
          <td>${clave.id}</td>
          <td>${clave.clave || "No disponible"}</td>
          <td>${clave.n_login || "No disponible"}</td> <!-- Nueva columna -->
          <td>${clave.terminada !== null && clave.terminada !== undefined ? (clave.terminada ? "Sí" : "No") : "Clave no usada"}</td>
        `;
        clavesList.appendChild(row);
      });

      // Restaurar el estado de los checkboxes
    })
    .catch((error) => {
      console.error("Error al cargar las claves:", error);
      showToast(`Error: ${error.message || "Ocurrió un problema al cargar las claves."}`, "danger");
    });
}

// Ordenar la tabla al hacer clic en los encabezados
document.querySelectorAll("#clavesTable th").forEach((header) => {
  header.addEventListener("click", () => {
    const orderBy = header.textContent.trim().toLowerCase(); // Columna seleccionada
    const orderDir = header.dataset.order === "asc" ? "desc" : "asc"; // Alternar dirección
    header.dataset.order = orderDir;

    cargarClaves(1, pageSize, orderBy, orderDir); // Recargar la tabla con la nueva ordenación
  });
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

// Botón para marcar claves seleccionadas como "Sí" (terminada = 1)
document.getElementById("markSelectedAsYesButton").addEventListener("click", () => {
  updateSelectedClaves(1); // Marcar como "Sí"
});

// Botón para marcar claves seleccionadas como "No" (terminada = 0)
document.getElementById("markSelectedAsNoButton").addEventListener("click", () => {
  updateSelectedClaves(0); // Marcar como "No"
});

// Función genérica para actualizar el estado de las claves seleccionadas
function updateSelectedClaves(newTerminada) {
  const selectedIdsArray = []; // IDs seleccionados

  // Obtener las filas seleccionadas
  const checkboxes = document.querySelectorAll("#clavesList input[type='checkbox']:checked");
  if (checkboxes.length === 0) {
      showToast("Por favor, selecciona al menos una clave para editar.", "warning");
      return;
  }

  checkboxes.forEach((checkbox) => {
      selectedIdsArray.push(checkbox.value);
  });

  // Mostrar el modal de confirmación con el nuevo estado
  showModal(
      "Editar claves seleccionadas",
      `¿Estás seguro de marcar ${selectedIdsArray.length} claves como ${newTerminada ? "Sí terminada" : "No terminada"}?`,
      () => {
          // Crear instancia del modal
          const modalElement = document.getElementById("confirmEditModal"); // Asegúrate de que este ID existe en tu HTML
          const modalInstance = bootstrap.Modal.getInstance(modalElement);

          fetch("includesCP/poblacionDB.php?action=editarClave", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ ids: selectedIdsArray, terminada: newTerminada }),
          })
              .then((response) => {
                  if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
                  return response.json();
              })
              .then((data) => {
                  if (data.success) {
                      showToast("Estado de claves actualizado correctamente.", "success");

                      // Recargar la lista de claves para reflejar los cambios
                      cargarClaves(currentPage);
                  } else {
                      showToast(data.message || "Error al actualizar las claves.", "danger");
                  }
              })
              .catch((error) => {
                  console.error("Error al actualizar las claves:", error);
                  showToast("Ocurrió un error al intentar actualizar las claves.", "danger");
              })
              .finally(() => {
          // Cerrar el modal correctamente
          const modalElement = document.getElementById("customModal");
          const modalInstance = bootstrap.Modal.getInstance(modalElement);
    
          if (modalInstance) {
            modalInstance.hide(); // Ocultar el modal
          } else {
            console.error("No se pudo obtener una instancia del modal.");
          }
    
          // Mover el foco al body para evitar problemas de accesibilidad
          document.body.focus();
    
              });
      },
      null,
      ["bg-info", "text-white"]
  );
}


// Botón para eliminar claves seleccionadas
document.getElementById("deleteSelectedButton").addEventListener("click", () => {
  const selectedIdsArray = Array.from(selectedIds);

  if (selectedIdsArray.length === 0) {
    showToast("Por favor, selecciona al menos una clave para eliminar.", "warning");
    return;
  }

  // Mostrar el modal de confirmación
  showModal(
    "Eliminar claves seleccionadas",
    `¿Estás seguro de que deseas <b>eliminar ${selectedIdsArray.length} claves</b> seleccionadas?`,
    () => {
      fetch("includesCP/poblacionDB.php?action=eliminarClavesSeleccionadas", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIdsArray }),
      })
        .then((response) => {
          if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showToast("Claves eliminadas correctamente.", "success");
            cargarClaves(currentPage); // Recargar la lista de claves
            selectedIds.clear(); // Limpiar selección
          } else {
            showToast(data.message || "Error al eliminar las claves.", "danger");
          }
        })
        .catch((error) => {
          console.error("Error al eliminar las claves:", error);
          showToast("Ocurrió un error al intentar eliminar las claves.", "danger");
        })
        .finally(() => {
          // Cerrar el modal correctamente
      const modalElement = document.getElementById("customModal");
      const modalInstance = bootstrap.Modal.getInstance(modalElement);

      if (modalInstance) {
        modalInstance.hide(); // Ocultar el modal
      } else {
        console.error("No se pudo obtener una instancia del modal.");
      }

      // Mover el foco al body para evitar problemas de accesibilidad
      document.body.focus();

        });
    },
    null,
    ["bg-danger", "text-white"]
  );
});

// Modal dinámico
function showModal(title, bodyText, confirmAction, cancelAction = null, headerClasses = ["bg-primary"], showSpinner = false) {
  const modal = new bootstrap.Modal(document.getElementById("customModal"));

  // Configurar el título
  document.getElementById("customModalTitle").textContent = title;

  // Configurar el cuerpo del modal
  document.getElementById("modalMessage").innerHTML = bodyText;

  // Mostrar u ocultar el spinner
  const loadingSpinner = document.getElementById("loadingSpinner");
  loadingSpinner.style.display = showSpinner ? "block" : "none";

  // Aplicar clases al encabezado
  const modalHeader = document.getElementById("customModalHeader");
  modalHeader.className = "modal-header"; // Resetear clases previas
  headerClasses.forEach(clase => {
      modalHeader.classList.add(clase);
  });

  // Configurar botones
  document.getElementById("customModalConfirmButton").onclick = confirmAction;
  document.getElementById("customModalCancelButton").onclick = () => {
    if (cancelAction) {
        cancelAction(); // Ejecutar la acción personalizada si se proporciona
    }
    window.location.reload(); // Recargar la página después de cancelar
};
  modal.show();
}

// Función para generar claves aleatorias
function generarClavesAleatorias() {
  const cantidadClaves = parseInt(document.getElementById("randomKeyCount").value);
  const tipoGeneracion = document.querySelector('input[name="tipoGeneracion"]:checked').value;
  let idBase = null;

  if (tipoGeneracion === "especifico") {
      idBase = parseInt(document.getElementById("idBase").value);
      if (!idBase || idBase <= 0) {
          showToast("Por favor, ingresa un ID base válido.", "warning");
          return;
      }
  }

  // Validar la cantidad de claves
  if (!cantidadClaves || cantidadClaves <= 0 || cantidadClaves > 10000) {
      showToast("La cantidad de claves debe estar entre 1 y 10,000.", "warning");
      return;
  }

  // Deshabilitar el botón mientras se genera
  const generateKeysButton = document.getElementById("generateKeysButton");
  generateKeysButton.disabled = true;
  generateKeysButton.textContent = "Generando...";

  // Enviar la solicitud al backend
  fetch("includesCP/poblacionDB.php?action=generarClavesAleatorias", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ cantidad: cantidadClaves, tipoGeneracion, idBase }),
  })
      .then((response) => {
          if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
          return response.json();
      })
      .then((data) => {
          if (data.success) {
              showToast("Claves generadas correctamente.", "success");
              cargarClaves(); // Recargar la lista de claves
          } else {
              showToast(data.message || "Ocurrió un error al generar las claves.", "danger");
          }
      })
      .catch((error) => {
          console.error("Error al generar las claves:", error);
          showToast("Ocurrió un error al intentar generar las claves.", "danger");
      })
      .finally(() => {
          generateKeysButton.disabled = false;
          generateKeysButton.textContent = "Generar";
      });
}
// Evento para el formulario de generación de claves aleatorias
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
      `¿Estás seguro de generar <b>${cantidad}</b> claves aleatorias?`,
      () => {
          // Si la cantidad es mayor a 1000, mostrar el segundo modal de confirmación
          if (cantidad > 1000) {
              showModal(
                  "Confirmación Final",
                  `Está a punto de generar <b>${cantidad}</b> claves. ¿Está completamente seguro?`,
                  () => {
                      generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
                  },
                  null,
                  ["bg-danger", "text-white"]
              );
          } else {
              generarClavesAleatorias(cantidad); // Llamar a la función para generar claves
          }
      },
      null,
      ["bg-primary", "text-white"]
  );
});

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

// Función para cerrar el modal y eliminar el backdrop manualmente
function closeModal(modalId) {
  const modalElement = document.getElementById(modalId);
  const modalInstance = bootstrap.Modal.getInstance(modalElement);

  if (modalInstance) {
      modalInstance.hide(); // Ocultar el modal

      // Forzar la eliminación del backdrop después de que el modal se cierre
      modalElement.addEventListener("hidden.bs.modal", () => {
          const backdrop = document.querySelector(".modal-backdrop");
          if (backdrop) {
              backdrop.remove(); // Eliminar el backdrop manualmente
          }
          document.body.classList.remove("modal-open"); // Restaurar el estado del body
          document.body.style.paddingRight = ""; // Limpiar cualquier padding derecho añadido por Bootstrap
      });
  }
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


// Cargar más claves
document.getElementById("loadMoreButton").addEventListener("click", () => {
  currentPage++;
  cargarClaves(currentPage);
});

// Cargar las primeras claves al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  cargarClaves();
});