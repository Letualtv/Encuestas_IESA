document.getElementById("tipo").addEventListener("change", () => {
  const tipoPregunta = document.getElementById("tipo").value;

  // Limpiar opciones previas
  const opcionesDiv = document.getElementById("opciones");
  while (opcionesDiv.firstChild) {
    opcionesDiv.removeChild(opcionesDiv.firstChild);
  }

  // Reiniciar variables globales
  ultimaClave = 0;
  claveAutomaticaMatrix3 = 1;

  // Ajustar parámetros del formulario según el tipo de pregunta
  ajustarFormulario();
});

// Función para ajustar los parámetros del formulario según el tipo de pregunta
function ajustarFormulario() {
  const tipo = document.getElementById("tipo").value;

  // Ajustar campos adicionales para "numberInput"
  const numberInputFields = document.getElementById("numberInputFields");
  numberInputFields.style.display = tipo === "numberInput" ? "block" : "none";

  // Ajustar campos de encabezado para "matrix2" y "matrix3"
  const encabezadoFields = document.getElementById("encabezadoFields");
  encabezadoFields.style.display = ["matrix2", "matrix3"].includes(tipo)
    ? "block"
    : "none";

  // Ajustar campos para "cajaTexto"
  const cajaTextoFields = document.getElementById("cajaTextoFields");
  cajaTextoFields.style.display = tipo === "cajaTexto" ? "block" : "none";
}

// Función para eliminar una opción dinámicamente
function eliminarOpcion(button) {
  button.parentElement.remove();
}

// Función para agregar una nueva opción dinámicamente
let ultimaClave = 0;
function agregarOpcion(clave = null, label = "", subLabels = {}) {
  const tipoPregunta = document.getElementById("tipo").value;

  if (tipoPregunta === "cajaTexto") {
    // No agregar opciones para "cajaTexto"
    return;
  }

  const opcionesContainer = document.getElementById("opciones");

  // Generar una clave única si no se proporciona
  const nuevaClave = clave || `opcion${++ultimaClave}`;

  // Crear el contenedor de la opción
  const opcionDiv = document.createElement("div");
  opcionDiv.classList.add("input-group", "input-group-sm", "mb-2");

  // Campo de clave
  const claveInput = document.createElement("input");
  claveInput.type = "text";
  claveInput.name = "claves[]";
  claveInput.value = nuevaClave;
  claveInput.readOnly = true;
  claveInput.classList.add("form-control", "w-25");

  // Campo de etiqueta
  const labelInput = document.createElement("input");
  labelInput.type = "text";
  labelInput.name = "labels[]";
  labelInput.value = label;
  labelInput.placeholder = "Etiqueta";
  labelInput.classList.add("form-control");

  // Botón para eliminar la opción
  const eliminarButton = document.createElement("button");
  eliminarButton.type = "button";
  eliminarButton.classList.add("btn", "btn-outline-danger");
  eliminarButton.innerHTML = '<i class="fa-solid fa-trash"></i>';
  eliminarButton.onclick = () => opcionDiv.remove();

  // Agregar elementos al contenedor
  opcionDiv.appendChild(claveInput);
  opcionDiv.appendChild(labelInput);
  opcionDiv.appendChild(eliminarButton);

  // Agregar al DOM
  opcionesContainer.appendChild(opcionDiv);
}

// Función para editar una pregunta existente
function editarPregunta(id) {
  fetch(`includesCP/obtenerPreguntas.php?id=${id}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la solicitud: " + response.status);
      }
      return response.json();
    })
    .then((pregunta) => {
      if (!pregunta || !pregunta.id) {
        throw new Error("Los datos de la pregunta son inválidos.");
      }

      // Rellenar el formulario con los datos de la pregunta
      document.getElementById("preguntaId").value = pregunta.id || "";
      document.getElementById("titulo").value = pregunta.titulo || "";
      document.getElementById("n_pag").value = pregunta.n_pag || 1;
      document.getElementById("tipo").value = pregunta.tipo || "radio";
      document.getElementById("subTitulo").value = pregunta.subTitulo || "";

      const opcionesDiv = document.getElementById("opciones");

      // Limpiar opciones previas
      while (opcionesDiv.firstChild) {
        opcionesDiv.removeChild(opcionesDiv.firstChild);
      }

      // Agregar las opciones existentes
      if (["matrix3", "formSelect"].includes(pregunta.tipo)) {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((clavePrincipal) => {
          const opcion = opciones[clavePrincipal];
          const label = opcion.label || "";
          const subLabels = opcion.subLabel || {};
          agregarOpcion(clavePrincipal, label, subLabels);
        });
      }
      // Rellenar valores específicos para "cajaTexto"
      else if (pregunta.tipo === "cajaTexto") {
        document.getElementById("placeholder").value =
          pregunta.placeholder || "";
      } else if (pregunta.tipo === "matrix1") {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((clave) => {
          const opcionCompleta = opciones[clave];
          const [izquierda, derecha] = opcionCompleta.split(" - ");
          agregarOpcion(clave, `${izquierda} - ${derecha}`);
        });
      } else {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((key) => {
          agregarOpcion(key, opciones[key]);
        });
      }

      // Rellenar valores específicos para "numberInput"
      if (pregunta.tipo === "numberInput") {
        document.getElementById("min").value = pregunta.valores?.min || "";
        document.getElementById("max").value = pregunta.valores?.max || "";
        document.getElementById("placeholder").value =
          pregunta.valores?.placeholder || "";
      }

      // Rellenar encabezados si existen
      if (pregunta.encabezado) {
        document.getElementById("label").value =
          pregunta.encabezado.label || "";
        const unoClave = Object.keys(pregunta.encabezado.uno)[0];
        const unoValor = pregunta.encabezado.uno[unoClave];
        document.getElementById("unoClave").value = unoClave || "";
        document.getElementById("unoValor").value = unoValor || "";

        const dosClave = Object.keys(pregunta.encabezado.dos)[0];
        const dosValor = pregunta.encabezado.dos[dosClave];
        document.getElementById("dosClave").value = dosClave || "";
        document.getElementById("dosValor").value = dosValor || "";

        document.getElementById("tres").value = pregunta.encabezado.tres || "";
      }

      // Recuperar y mostrar la descripción (si existe)
      const descripcion = pregunta.cabecera || null;
      const mostrarDescripcionCheckbox = document.getElementById(
        "mostrar-descripcion"
      );
      const descripcionContainer = document.getElementById(
        "descripcionContainer"
      );

      if (descripcion) {
        const textoVacio =
          !descripcion.texto || descripcion.texto.trim() === "";

        if (textoVacio) {
          mostrarDescripcionCheckbox.checked = false;
          descripcionContainer.style.display = "none";
        } else {
          mostrarDescripcionCheckbox.checked = true;
          descripcionContainer.style.display = "block";

            // Usar directamente el contenedor existente en el HTML
            const descripcionRuleDiv = document.getElementById("descripcionRule");

            // Reutilizar la instancia de Quill ya inicializada si existe
            if (quillDescripcion) {
            quillDescripcion.root.innerHTML = descripcion.texto || "";
            } else {
            // Inicializar Quill en el contenedor existente si no está inicializado
            quillDescripcion = new Quill(descripcionRuleDiv, {
              theme: "bubble",
              placeholder: "Escribe la descripción aquí...",
              modules: {
              toolbar: [
                ["bold", "italic", "underline"], // Botones básicos
                ["link"], // Añadir enlaces
                [{ list: "ordered" }, { list: "bullet" }], // Listas ordenadas y desordenadas
                ["clean"], // Limpiar formato
              ],
              },
            });
            quillDescripcion.root.innerHTML = descripcion.texto || "";
            }
          }
          } else {
          mostrarDescripcionCheckbox.checked = false;
          descripcionContainer.style.display = "none";
          }

      // Ajustar parámetros del formulario
      ajustarFormulario();

      // Cargar las reglas de filtro (si existen)
      cargarFiltro(pregunta.filtro || {});

      // Mostrar las reglas de filtro (si existen)
      document.getElementById("mostrar-filtro").checked = !!Object.keys(
        pregunta.filtro || {}
      ).length;
      mostrarFiltro();

      // Indicar que se está editando una pregunta
      isEditing = true;
    })
    .catch((error) => {
      console.error("Error al cargar los datos de la pregunta:", error);
      showToast(
        "Ocurrió un error al intentar cargar los datos de la pregunta.",
        "danger"
      );
    });
}

// Listener para cambios en el tipo de pregunta
document.getElementById("tipo").addEventListener("change", () => {
  ajustarFormulario();
});

// Función para inicializar el formulario (creación de nueva pregunta)
function inicializarFormulario() {
  // Limpiar el formulario
  document.getElementById("preguntaId").value = "";
  document.getElementById("titulo").value = "";
  document.getElementById("n_pag").value = 1;
  document.getElementById("tipo").value = "radio"; // Valor predeterminado
  document.getElementById("subTitulo").value = "";
  document.getElementById("placeholder").value = "";

  const opcionesDiv = document.getElementById("opciones");
  while (opcionesDiv.firstChild) {
    opcionesDiv.removeChild(opcionesDiv.firstChild);
  }

  // Limpiar campos específicos
  document.getElementById("min").value = "";
  document.getElementById("max").value = "";
  document.getElementById("placeholder").value = "";

  // Limpiar encabezado
  document.getElementById("label").value = "";
  document.getElementById("unoClave").value = "";
  document.getElementById("unoValor").value = "";
  document.getElementById("dosClave").value = "";
  document.getElementById("dosValor").value = "";
  document.getElementById("tres").value = "";

  // Limpiar descripción
  const mostrarDescripcionCheckbox = document.getElementById(
    "mostrar-descripcion"
  );
  const descripcionContainer = document.getElementById("descripcionContainer");
  mostrarDescripcionCheckbox.checked = false;
  descripcionContainer.style.display = "none";

  // Ajustar parámetros del formulario
  ajustarFormulario();
}

function cargarDatosPregunta(id) {
  fetch(`includesCP/obtenerPregunta.php?id=${id}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error al obtener los datos de la pregunta.");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success && data.pregunta) {
        const pregunta = data.pregunta;
        document.getElementById("preguntaId").value = pregunta.id;
        document.getElementById("titulo").value = pregunta.titulo;
        document.getElementById("n_pag").value = pregunta.n_pag;
        document.getElementById("tipo").value = pregunta.tipo;
        document.getElementById("subTitulo").value = pregunta.subTitulo || "";

        // Procesar opciones si existen
        if (pregunta.opciones && typeof pregunta.opciones === "object") {
          Object.keys(pregunta.opciones).forEach((key) => {
            // Lógica para cargar las opciones en el formulario
          });
        }

        // Procesar encabezado si existe
        if (pregunta.encabezado && typeof pregunta.encabezado === "object") {
          // Lógica para cargar el encabezado en el formulario
        }

        // Procesar otros campos según sea necesario
      } else {
        console.error(
          "Error: Datos de la pregunta no encontrados o inválidos."
        );
      }
    })
    .catch((error) => {
      console.error("Error al cargar los datos de la pregunta:", error);
    });
}
