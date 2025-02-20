// Variable global para rastrear si se está editando una pregunta
let isEditing = true;

// Función para agregar una nueva opción dinámicamente
function agregarOpcion(clave = "", opcion = "", subLabels = {}) {
  const opcionesDiv = document.getElementById("opciones");
  const tipoPregunta = document.getElementById("tipo").value;
  const preguntaId = parseInt(document.getElementById("preguntaId").value) || 0;

  // Crear la nueva opción principal
  const nuevaOpcion = document.createElement("div");
  nuevaOpcion.classList.add("input-group", "mb-2");
  
  if (tipoPregunta === "matrix3") {
    // Generar clave autoincremental para labels principales (1, 2, 3, ...)
    const claveAutomatica = opcionesDiv.children.length + 1;

    nuevaOpcion.innerHTML = `
      <div class="input-group-text shadow-sm fw-bold">Clave</div>
      <input type="text" class="form-control shadow-sm clave-principal col-1" name="claves[]" value="${clave || claveAutomatica}" required>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control w-75 shadow-sm label-principal" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
  } else if (tipoPregunta === "matrix1") {
    const [izquierda, derecha] = opcion.split(" - ");
    // Generar clave automática basada en el ID para matrix1
    const claveAutomatica = preguntaId + opcionesDiv.children.length;

    nuevaOpcion.innerHTML = `
      <div class="input-group-text shadow-sm fw-bold">ID</div>
      <input type="text" class="input-group-text shadow-sm clave-principal col-1" name="claves[]" value="${clave || claveAutomatica}" disabled>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control shadow-sm w-25" name="izquierda[]" placeholder="Extremo izquierdo" value="${izquierda || ''}" required>
      <div class="input-group-text"><i class="fa-solid fa-minus"></i></div>
      <input type="text" class="form-control shadow-sm w-25" name="derecha[]" placeholder="Extremo derecho" value="${derecha || ''}" required>
    `;
  } else if (tipoPregunta === "matrix2") {
    // Generar clave automática basada en el ID para matrix2
    const claveAutomatica = preguntaId + opcionesDiv.children.length;

    nuevaOpcion.innerHTML = `
      <div class="input-group-text shadow-sm fw-bold">ID</div>
      <input type="text" class="input-group-text shadow-sm clave-principal col-1" name="claves[]" value="${clave || claveAutomatica}" disabled>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control w-75 shadow-sm label-principal" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
  } else {
    // Para otros tipos de preguntas, generar claves autoincrementales desde 1
    const claveAutomatica = opcionesDiv.children.length + 1;

    nuevaOpcion.innerHTML = `
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control shadow-sm clave-principal" name="claves[]" placeholder="Clave" value="${clave || claveAutomatica}">
      <input type="text" class="form-control w-75 shadow-sm label-principal" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
  }

  opcionesDiv.appendChild(nuevaOpcion);

  // Agregar subLabels solo si el tipo de pregunta es formSelect o matrix3
  if (tipoPregunta === "formSelect" || tipoPregunta === "matrix3") {
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
    addSubLabelButton.textContent = tipoPregunta === "matrix3" ? "Agregar opción a la matriz" : "Agregar opción al desplegable";
    addSubLabelButton.onclick = () => agregarSubLabel(subLabelsDiv);
    subLabelsDiv.appendChild(addSubLabelButton);
    opcionesDiv.appendChild(subLabelsDiv);
  }
}

// Evento para actualizar las claves automáticamente cuando el ID cambia
document.getElementById("preguntaId").addEventListener("input", function () {
  const preguntaId = parseInt(this.value) || 0;
  const opcionesDiv = document.getElementById("opciones");

  Array.from(opcionesDiv.children).forEach((opcion, index) => {
    const claveInput = opcion.querySelector('[name="claves[]"]');
    if (claveInput) {
      claveInput.value = preguntaId + index;
    }
  });
});

// Función para agregar un subLabel
function agregarSubLabel(container, clave = "", valor = "") {
  const tipoPregunta = document.getElementById("tipo").value;
  const preguntaId = parseInt(document.getElementById("preguntaId").value) || 0;

  const subLabelDiv = document.createElement("div");
  subLabelDiv.classList.add("input-group", "input-group-sm", "mb-2");

  if (tipoPregunta === "matrix3") {
    // Generar clave automática basada en el ID y el número de sublabels existentes
    const claveAutomatica = preguntaId + container.children.length;

    subLabelDiv.innerHTML = `
      <div class="input-group-text shadow-sm fw-bold">ID</div>
      <input type="text" class="input-group-text shadow-sm clave-sublabel col-1" name="subClaves[]" value="${clave || claveAutomatica}" disabled>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarSubLabel(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control w-75 shadow-sm sublabel-principal" name="subValores[]" placeholder="SubLabel" value="${valor}" required>
    `;
  } else {
    // Para otros tipos de preguntas, usar inputs estándar
    subLabelDiv.innerHTML = `
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarSubLabel(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control shadow-sm clave-sublabel" name="subClaves[]" placeholder="Clave" value="${clave}">
      <input type="text" class="form-control w-75 shadow-sm sublabel-principal" name="subValores[]" placeholder="SubLabel" value="${valor}" required>
    `;
  }

  // Insertar el sublabel antes del botón "Agregar opción al desplegable"
  const addButton = container.querySelector("button.btn-secondary");
  if (addButton) {
    container.insertBefore(subLabelDiv, addButton);
  } else {
    container.appendChild(subLabelDiv);
  }
}

function obtenerSiguienteClavePrincipal(opcionesDiv) {
  // Calcular la siguiente clave principal basada en el número de opciones existentes
  return opcionesDiv.children.length + 1;
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
    if (tipoPregunta === "matrix3") {
      modalMessage = "¿Estás seguro de que deseas eliminar esta opción principal y sus rangos?";
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
      "¿Estás seguro de que deseas eliminar esta opción?";
  
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