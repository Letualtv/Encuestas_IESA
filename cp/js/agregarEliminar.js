// Variable global para rastrear si se está editando una pregunta
let isEditing = false;

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
    const opcionesPrincipales = Array.from(opcionesDiv.children).filter(
      (child) => !child.classList.contains("sublabels-container")
    );
    const claveAutomatica = opcionesPrincipales.length + 1;

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

  // Reorganizar las claves de los sublabels en matrix3
  if (tipoPregunta === "matrix3") {
    reorganizarSublabelsMatrix3();
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

  // Reorganizar las claves de los sublabels en matrix3
  reorganizarSublabelsMatrix3();
});

// Función para agregar un subLabel
function agregarSubLabel(container, clave = "", valor = "") {
  const tipoPregunta = document.getElementById("tipo").value;
  const preguntaId = parseInt(document.getElementById("preguntaId").value) || 0;

  const subLabelDiv = document.createElement("div");
  subLabelDiv.classList.add("input-group", "input-group-sm", "mb-2");

  if (tipoPregunta === "matrix3") {
    // Obtener todos los sublabels actuales
    const todosLosSublabels = Array.from(document.querySelectorAll(".sublabels-container .input-group"));
    const claveAutomatica = preguntaId + todosLosSublabels.length;

    subLabelDiv.innerHTML = `
      <div class="input-group-text shadow-sm fw-bold">ID</div>
      <input type="text" class="input-group-text shadow-sm clave-sublabel col-1" name="subClaves[]" value="${clave || claveAutomatica}" disabled>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarSubLabel(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
      <input type="text" class="form-control w-75 shadow-sm sublabel-principal" name="subValores[]" placeholder="SubLabel" value="${valor}" required>
    `;
  } else if (tipoPregunta === "formSelect") {
     // Obtener el contenedor principal correspondiente a esta opción
     const opcionPrincipalDiv = container.closest(".input-group");
     if (!opcionPrincipalDiv) {
       console.error("Error: No se encontró el contenedor principal para el sublabel. Verifica la estructura del DOM.");
       return;
     }
 
     const clavePrincipalInput = opcionPrincipalDiv.querySelector('[name="claves[]"]');
     if (!clavePrincipalInput) {
       console.error("Error: No se encontró el campo de clave principal. Verifica la estructura del DOM.");
       return;
     }
 
     const clavePrincipal = parseInt(clavePrincipalInput.value) || 0;
 
     // Contar solo los sublabels existentes, ignorando otros elementos
     const subLabelsExistentes = Array.from(container.children).filter(
       (child) => child.classList.contains("input-group")
     );
     const indiceSublabel = subLabelsExistentes.length;
 
     // Calcular la clave del sublabel como clavePrincipal * 10 + índice del sublabel
     const claveSublabel = clavePrincipal * 10 + indiceSublabel;
 
     subLabelDiv.innerHTML = `
       <div class="input-group-text shadow-sm fw-bold">ID</div>
       <input type="text" class="input-group-text shadow-sm clave-sublabel col-1" name="subClaves[]" value="${claveSublabel}" disabled>
       <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarSubLabel(this)">
         <i class="fa-solid fa-trash"></i>
       </button>
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

  // Reorganizar las claves de los sublabels en matrix3
  if (tipoPregunta === "matrix3") {
    reorganizarSublabelsMatrix3();
  }
}
function obtenerSiguienteClavePrincipal(opcionesDiv) {
  const opcionesPrincipales = Array.from(opcionesDiv.children).filter(
    (child) => !child.classList.contains("sublabels-container")
  );
  const ultimaClave = opcionesPrincipales.reduce((max, opcion) => {
    const claveInput = opcion.querySelector('[name="claves[]"]');
    const clave = parseInt(claveInput?.value) || 0;
    return Math.max(max, clave);
  }, 0);
  return ultimaClave + (ultimaClave % 2 === 0 ? 1 : 2); // Claves impares consecutivas
}
// Función para reorganizar las claves de los sublabels en matrix3
function reorganizarSublabelsMatrix3() {
  const opcionesDiv = document.getElementById("opciones");
  const preguntaId = parseInt(document.getElementById("preguntaId").value) || 0;

  // Obtener todos los sublabels actuales
  const todosLosSublabels = Array.from(document.querySelectorAll(".sublabels-container .input-group"));

  // Asignar claves correlativas a los sublabels
  todosLosSublabels.forEach((subLabelDiv, index) => {
    const claveSublabelInput = subLabelDiv.querySelector('[name="subClaves[]"]');
    if (claveSublabelInput) {
      claveSublabelInput.value = preguntaId + index;
    }
  });
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
    if (subLabelsContainer && subLabelsContainer.classList.contains("sublabels-container")) {
      // Eliminar el contenedor de sublabels (incluyendo sublabels y el botón)
      subLabelsContainer.remove();
    }

    // Eliminar la opción principal
    opcionPrincipalDiv.remove();

    // Reorganizar las claves de los sublabels en matrix3
    if (tipoPregunta === "matrix3") {
      reorganizarSublabelsMatrix3();
    }

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

    // Reorganizar las claves de los sublabels en matrix3
    const tipoPregunta = document.getElementById("tipo").value;
    if (tipoPregunta === "matrix3") {
      reorganizarSublabelsMatrix3();
    }

    modal.hide();
    showToast("Opción eliminada correctamente.", "success");
  };
}

