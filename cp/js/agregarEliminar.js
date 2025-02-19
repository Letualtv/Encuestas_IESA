// Variable global para rastrear si se está editando una pregunta
let isEditing = true;

// Función para agregar una nueva opción dinámicamente
function agregarOpcion(clave = "", opcion = "", subLabels = {}) {
  const opcionesDiv = document.getElementById("opciones");
  const tipoPregunta = document.getElementById("tipo").value;

  // Crear la nueva opción principal
  const nuevaOpcion = document.createElement("div");
  nuevaOpcion.classList.add("input-group", "mb-2");

  if (tipoPregunta === "matrix1") {
    const [izquierda, derecha] = opcion.split(" - ");



    nuevaOpcion.innerHTML = `
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control shadow-sm clave-principal" name="claves[]" placeholder="Clave" value="${clave}" required>
      <input type="text" class="form-control shadow-sm w-25" name="izquierda[]" placeholder="Extremo izquierdo" value="${izquierda || ''}" required>
      <div class="input-group-text"><i class="fa-solid fa-minus"></i></div>
      <input type="text" class="form-control shadow-sm w-25" name="derecha[]" placeholder="Extremo derecho" value="${derecha || ''}" required>
    `;
  } else {
    // Para otros tipos de preguntas, usar los inputs estándar
    nuevaOpcion.innerHTML = `
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control shadow-sm clave-principal" name="claves[]" placeholder="Clave" value="${clave}" required>
      <input type="text" class="form-control w-75 shadow-sm label-principal" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
  }

  opcionesDiv.appendChild(nuevaOpcion);

  // Agregar subLabels solo si el tipo de pregunta es formSelect
  if (tipoPregunta === "formSelect") {
    const subLabelsDiv = document.createElement("div");
    subLabelsDiv.classList.add("sublabels-container", "ms-4");

    // Agregar subLabels existentes
    Object.keys(subLabels).forEach((subClave) => {
      const subValor = subLabels[subClave];
      agregarSubLabel(subLabelsDiv, subClave, subValor);
    });

    // Botón para agregar un nuevo subLabel
    const addSubLabelButton = document.createElement("button");
    addSubLabelButton.classList.add("btn", "btn-sm", "btn-secondary", "mt-2", "mb-3");
    addSubLabelButton.textContent = "Agregar opción al desplegable";
    addSubLabelButton.onclick = () => agregarSubLabel(subLabelsDiv);
    subLabelsDiv.appendChild(addSubLabelButton);

    opcionesDiv.appendChild(subLabelsDiv);
  }
}

// Función para agregar un subLabel
function agregarSubLabel(container, clave = "", valor = "") {
  const subLabelDiv = document.createElement("div");
  subLabelDiv.classList.add("input-group", "mb-2", "input-group-sm");
  subLabelDiv.innerHTML = `
    <i class="fa-solid fa-arrow-right-from-bracket align-middle px-2 align-self-center"></i>  
    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarSubLabel(this)">
      <i class="fa-solid fa-trash"></i>
    </button>
    <input type="text" class="form-control shadow-sm clave-sublabel" name="subClaves[]" placeholder="Clave" value="${clave}" required>
    <input type="text" class="form-control w-75 shadow-sm valor-sublabel" name="subValores[]" placeholder="Valor" value="${valor}" required>
  `;

  // Insertar el sublabel antes del botón "Agregar opción al desplegable"
  const addButton = container.querySelector("button.btn-secondary");
  if (addButton) {
    container.insertBefore(subLabelDiv, addButton);
  } else {
    container.appendChild(subLabelDiv);
  }
}

// Función para eliminar una opción principal
function eliminarOpcion(button) {
    const modal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  
    // Verificar si el tipo de pregunta es "formSelect"
    const tipoPregunta = document.getElementById("tipo").value;
    let modalMessage = "¿Estás seguro de que deseas eliminar esta opción?";
  
    if (tipoPregunta === "formSelect") {
      modalMessage = "¿Estás seguro de que deseas eliminar esta opción principal y sus opciones de desplegable?";
    }
  
    // Actualizar el título y el cuerpo del modal
    document.getElementById("confirmDeleteModalLabel").textContent = "Confirmar eliminación";
    document.querySelector("#confirmDeleteModal .modal-body").textContent = modalMessage;
  
    modal.show();
  
    confirmDeleteButton.onclick = () => {
      // Encontrar el contenedor padre de la opción principal
      const opcionPrincipalDiv = button.parentElement;
  
      // Buscar el contenedor de sublabels (si existe)
      const subLabelsContainer = opcionPrincipalDiv.nextElementSibling;
      if (
        subLabelsContainer &&
        subLabelsContainer.classList.contains("sublabels-container")
      ) {
        // Eliminar el contenedor de sublabels (incluyendo sublabels y el botón)
        subLabelsContainer.remove();
      }
  
      // Eliminar la opción principal
      opcionPrincipalDiv.remove();
  
      modal.hide(); // Cerrar el modal después de eliminar
      showToast("Opción eliminada correctamente.", "success");
    };
  }

// Función para eliminar un sublabel
function eliminarSubLabel(button) {
    const modal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  
    // Actualizar el título y el cuerpo del modal
    document.getElementById("confirmDeleteModalLabel").textContent = "Confirmar eliminación";
    document.querySelector("#confirmDeleteModal .modal-body").textContent =
      "¿Estás seguro de que deseas eliminar esta opción del desplegable?";
  
    modal.show();
    confirmDeleteButton.onclick = () => {
      button.parentElement.remove();
      modal.hide();
      showToast("Opción eliminada correctamente.", "success");
    };
  }

// Función para mostrar notificaciones
function showToast(message, type) {
  const toastContainer =
    document.getElementById("toastContainer") ||
    document.body.appendChild(document.createElement("div"));
  toastContainer.id = "toastContainer";
  toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";

  const toast = document.createElement("div");
  toast.className = `toast align-items-center border-0 bg-${type} text-white`;
  toast.role = "alert";
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");

  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  toastContainer.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  setTimeout(() => toast.remove(), 3000); // Eliminar el toast después de 3 segundos
}