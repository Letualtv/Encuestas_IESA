// Función para ajustar los parámetros del formulario según el tipo de pregunta
function ajustarParametros() {
  const tipo = document.getElementById("tipo").value;
  const numberInputFields = document.getElementById("numberInputFields");

  // Mostrar u ocultar campos adicionales según el tipo de pregunta
  if (tipo === "numberInput") {
    numberInputFields.style.display = "block";
  } else {
    numberInputFields.style.display = "none";
  }
}

function ajustarEncabezado() {
  const tipo = document.getElementById("tipo").value;
  const encabezadoFields = document.getElementById("encabezadoFields");

  // Mostrar u ocultar los campos de encabezado según el tipo de pregunta
  if (tipo === "matrix2" || tipo === "matrix3") {
    encabezadoFields.style.display = "block";
  } else {
    encabezadoFields.style.display = "none";
  }
}

// Función para eliminar una opción dinámicamente
function eliminarOpcion(button) {
  button.parentElement.remove();
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
      if (pregunta.tipo === "formSelect" || pregunta.tipo === "matrix3") {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((clavePrincipal) => {
          const opcion = opciones[clavePrincipal];
          const label = opcion.label || "";
          const subLabels = opcion.subLabel || {};
          agregarOpcion(clavePrincipal, label, subLabels);
        });
      } else if (pregunta.tipo === "matrix1") {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((clave) => {
          const opcionCompleta = opciones[clave];
          const [izquierda, derecha] = opcionCompleta.split(" - ");
          agregarOpcion(clave, `${izquierda} - ${derecha}`);
        });
      } else if (tipoPregunta === "matrix1" || tipoPregunta === "matrix2") {
        Array.from(opcionesDiv.children).forEach((opcion, index) => {
          const claveInput = opcion.querySelector('[name="claves[]"]');
          if (claveInput) {
            claveInput.value = preguntaId + index;
          }
        });
      
      } else {
        const opciones = pregunta.opciones || {};
        Object.keys(opciones).forEach((key) => {
          agregarOpcion(key, opciones[key]);
        });
      }

      if (pregunta.tipo === "numberInput") {
        document.getElementById("min").value = pregunta.valores?.min || "";
        document.getElementById("max").value = pregunta.valores?.max || "";
        document.getElementById("placeholder").value =
          pregunta.valores?.placeholder || "";
      }
      if (pregunta.encabezado) {
        document.getElementById("label").value =
          pregunta.encabezado.label || "";

        // Recuperar la clave y el valor dinámicos para "uno"
        const unoClave = Object.keys(pregunta.encabezado.uno)[0];
        const unoValor = pregunta.encabezado.uno[unoClave];
        document.getElementById("unoClave").value = unoClave || "";
        document.getElementById("unoValor").value = unoValor || "";

        // Recuperar la clave y el valor dinámicos para "dos"
        const dosClave = Object.keys(pregunta.encabezado.dos)[0];
        const dosValor = pregunta.encabezado.dos[dosClave];
        document.getElementById("dosClave").value = dosClave || "";
        document.getElementById("dosValor").value = dosValor || "";

        document.getElementById("tres").value = pregunta.encabezado.tres || "";
      }
      // Recuperar y mostrar la descripción (si existe)
      const descripcion = pregunta.cabecera || null; // Obtener la cabecera de la pregunta
      const mostrarDescripcionCheckbox = document.getElementById(
        "mostrar-descripcion"
      );
      const descripcionContainer = document.getElementById(
        "descripcionContainer"
      );

      if (descripcion) {
        // Verificar si todos los campos de descripción están vacíos
        const texto1Vacio =
          !descripcion.texto1 || descripcion.texto1.trim() === "";
        const listaVacia =
          !descripcion.lista || descripcion.lista.trim() === "";
        const texto2Vacio =
          !descripcion.texto2 || descripcion.texto2.trim() === "";

        if (texto1Vacio && listaVacia && texto2Vacio) {
          // Si todos los campos están vacíos, desactivar el checkbox y ocultar el contenedor
          mostrarDescripcionCheckbox.checked = false;
          descripcionContainer.style.display = "none";
        } else {
          // Si hay contenido en al menos un campo, activar el checkbox y mostrar el contenedor
          mostrarDescripcionCheckbox.checked = true;
          descripcionContainer.style.display = "block";

          // Rellenar los campos de descripción
          document.querySelector("#descripcionRule .texto1").value =
            descripcion.texto1 || "";
          document.querySelector("#descripcionRule .lista").value =
            descripcion.lista || "";
          document.querySelector("#descripcionRule .texto2").value =
            descripcion.texto2 || "";
        }
      } else {
        // Desactivar el interruptor de descripción si no hay descripción
        mostrarDescripcionCheckbox.checked = false;
        descripcionContainer.style.display = "none";
      }

      // Ajustar parámetros del formulario
      ajustarParametros();
      ajustarEncabezado();

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
