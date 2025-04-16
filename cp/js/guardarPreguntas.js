// Inicializar Quill para el elemento #descripcionRule
const descripcionElement = document.querySelector("#descripcionRule");
if (descripcionElement) {
  quillDescripcion = new Quill(descripcionElement, {
    theme: "bubble",
    modules: {
      toolbar: [
        ["bold", "italic", "underline"], // Botones básicos
        ["link"], // Añadir enlaces
        [{ list: "ordered" }, { list: "bullet" }], // Listas ordenadas y desordenadas
        ["clean"], // Limpiar formato
      ],
    },
  });
} else {
  console.error(
    "No se encontró el elemento #descripcionRule para inicializar Quill."
  );
}

// Función principal para manejar el envío del formulario
document
  .getElementById("preguntaForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    // Mostrar el modal de confirmación
    const confirmSaveModal = new bootstrap.Modal(
      document.getElementById("confirmSaveModal")
    );
    const confirmSaveButton = document.getElementById("confirmSaveButton");
    confirmSaveModal.show();

    // Acción al confirmar el guardado
    confirmSaveButton.onclick = () => {
      const submitButton = document.querySelector('button[type="submit"]');
      submitButton.innerHTML = "Guardando...";
      submitButton.disabled = true;

      try {
        // Recopilar datos generales
        const preguntaId = document.getElementById("preguntaId").value;
        const titulo = QuillManager.obtenerContenidoQuill("titulo");
        const n_pag = document.getElementById("n_pag").value;
        const tipo = document.getElementById("tipo").value;
        const subTitulo = QuillManager.obtenerContenidoQuill("subTitulo");

        // Recopilar datos específicos según el tipo de pregunta
        const descripcion = recopilarDescripcion();
        const encabezado = recopilarEncabezado(tipo);
        const valores = recopilarValores(tipo);
        const filtro = recopilarFiltro();
        const opcionesObj = recopilarOpciones(tipo);

        // Validar que se hayan agregado opciones válidas
        if (!Object.keys(opcionesObj).length && tipo !== "cajaTexto") {
          showToast("No se han agregado opciones válidas.", "danger");
          throw new Error("No hay opciones válidas.");
        }

        // Enviar los datos al backend
        enviarDatosAlBackend(
          preguntaId,
          titulo,
          n_pag,
          tipo,
          subTitulo,
          opcionesObj,
          valores,
          filtro,
          descripcion,
          encabezado
        );
      } catch (error) {
        console.error(error);
        submitButton.innerHTML = "Guardar pregunta";
        submitButton.disabled = false;
      }

      confirmSaveModal.hide(); // Cerrar el modal después de enviar los datos
    };
  });

// Función para recopilar la descripción
function recopilarDescripcion() {
  const mostrarDescripcionCheckbox = document.getElementById(
    "mostrar-descripcion"
  );
  if (!mostrarDescripcionCheckbox.checked) return null;

  return {
    texto: quillDescripcion?.root.innerHTML.trim() || "", // Guardar como HTML limpio
  };
}

// Función para recopilar el encabezado
function recopilarEncabezado(tipo) {
  if (tipo !== "matrix2" && tipo !== "matrix3") return {};

  const encabezado = {
    label: document.getElementById("label")?.value.trim() || "",
    uno: {
      [document.getElementById("unoClave")?.value]:
        document.getElementById("unoValor")?.value.trim() || "",
    },
    dos: {
      [document.getElementById("dosClave")?.value]:
        document.getElementById("dosValor")?.value.trim() || "",
    },
    tres: document.getElementById("tres")?.value.trim() || "",
  };

  // Filtrar campos vacíos
  const encabezadoFiltrado = Object.fromEntries(
    Object.entries(encabezado).filter(([key, value]) => {
      if (key === "uno" || key === "dos") {
        return Object.values(value)[0];
      }
      return value;
    })
  );

  return Object.keys(encabezadoFiltrado).length
    ? encabezadoFiltrado
    : undefined;
}

// Función para recopilar valores específicos según el tipo de pregunta
function recopilarValores(tipo) {
  if (tipo === "numberInput") {
    return {
      min: document.getElementById("min")?.value || "",
      max: document.getElementById("max")?.value || "",
      placeholder: document.getElementById("placeholder")?.value || "",
    };
  }

  if (tipo === "cajaTexto") {
    return {
      placeholder: document.getElementById("placeholder")?.value || "",
    };
  }

  return {};
}

// Función para recopilar las reglas de filtro
function recopilarFiltro() {
  const filtro = {};

  document
    .querySelectorAll("#filtroRulesContainer > div")
    .forEach((ruleDiv) => {
      const preguntaIdInput = ruleDiv.querySelector("input[type='text']");
      const tipoFiltroSelect = ruleDiv.querySelector("select");
      const parametrosDiv = ruleDiv.querySelector(".parametros-filtro");

      if (!preguntaIdInput || !tipoFiltroSelect || !parametrosDiv) {
        console.error("Error: Elementos de filtro incompletos en una regla.");
        return;
      }

      const preguntaId = preguntaIdInput.value.trim();
      const tipoFiltro = tipoFiltroSelect.value.trim();
      let rango = "";

      switch (tipoFiltro) {
        case "unico":
          const valorUnico = parametrosDiv.querySelector("input")?.value.trim();
          if (valorUnico) rango = valorUnico;
          break;
        case "rango-cerrado":
          const min = parametrosDiv.querySelectorAll("input")[0]?.value.trim();
          const max = parametrosDiv.querySelectorAll("input")[1]?.value.trim();
          if (min && max) rango = `${min}-${max}`;
          break;
        case "rango-abajo":
          const valorRangoAbajo = parametrosDiv
            .querySelector("input")
            ?.value.trim();
          if (valorRangoAbajo) rango = `${valorRangoAbajo}-`;
          break;
        case "rango-arriba":
          const valorRangoArriba = parametrosDiv
            .querySelector("input")
            ?.value.trim();
          if (valorRangoArriba) rango = `${valorRangoArriba}+`;
          break;
        case "exclusion":
          const valorExclusion = parametrosDiv
            .querySelector("input")
            ?.value.trim();
          if (valorExclusion) rango = `!=${valorExclusion}`;
          break;
        default:
          console.error(`Tipo de filtro desconocido: ${tipoFiltro}`);
          return;
      }

      if (preguntaId && rango) {
        filtro[preguntaId] = rango;
      } else {
        console.warn(`Filtro inválido: ID=${preguntaId}, Rango=${rango}`);
      }
    });

  return filtro;
}

// Función para recopilar las opciones según el tipo de pregunta
function recopilarOpciones(tipo) {
  if (tipo === "cajaTexto") {
    return {};
  }

  const opcionesObj = {};

  if (tipo === "formSelect" || tipo === "matrix3") {
    Array.from(document.querySelectorAll('[name="claves[]"]')).forEach(
      (claveInput, index) => {
        const clave = claveInput.value.trim();
        const opcion = document
          .querySelectorAll('[name="opciones[]"]')
          [index].value.trim();

        if (clave && opcion) {
          opcionesObj[clave] = { label: opcion, subLabel: {} };

          const subLabelsContainer =
            claveInput.closest(".input-group").nextElementSibling;
          if (
            subLabelsContainer &&
            subLabelsContainer.classList.contains("sublabels-container")
          ) {
            Array.from(
              subLabelsContainer.querySelectorAll('[name="subClaves[]"]')
            ).forEach((subClaveInput, subIndex) => {
              const subClave = subClaveInput.value.trim();
              const subValor = subLabelsContainer
                .querySelectorAll('[name="subValores[]"]')
                [subIndex]?.value.trim();

              if (subClave && subValor) {
                opcionesObj[clave].subLabel[subClave] = subValor;
              }
            });
          }
        }
      }
    );
  } else if (tipo === "matrix1") {
    Array.from(document.querySelectorAll('[name="claves[]"]')).forEach(
      (claveInput) => {
        const clave = claveInput.value.trim();
        const opcionContainer = claveInput.closest(".input-group");

        const izquierda = opcionContainer
          .querySelector('[name="izquierda[]"]')
          ?.value.trim();
        const derecha = opcionContainer
          .querySelector('[name="derecha[]"]')
          ?.value.trim();

        if (clave && izquierda && derecha) {
          opcionesObj[clave] = `${izquierda} - ${derecha}`;
        } else {
          console.warn(
            `Opción inválida: Clave=${clave}, Izquierda=${izquierda}, Derecha=${derecha}`
          );
        }
      }
    );
  } else {
    Array.from(document.querySelectorAll('[name="claves[]"]')).forEach(
      (claveInput, index) => {
        const clave = claveInput.value.trim();
        const opcion = document
          .querySelectorAll('[name="opciones[]"]')
          [index]?.value.trim();

        if (clave && opcion) {
          opcionesObj[clave] = opcion;
        }
      }
    );
  }

  return opcionesObj;
}

// Función para enviar los datos al backend
function enviarDatosAlBackend(
  preguntaId,
  titulo,
  n_pag,
  tipo,
  subTitulo,
  opciones,
  valores,
  filtro,
  descripcion,
  encabezado
) {
  fetch("includesCP/guardarPregunta.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: preguntaId,
      titulo,
      n_pag,
      tipo,
      subTitulo,
      opciones,
      valores,
      filtro: Object.keys(filtro).length ? filtro : {},
      cabecera: descripcion || undefined,
      encabezado: encabezado || undefined,
      placeholder:
        tipo === "cajaTexto"
          ? document.getElementById("placeholder")?.value || ""
          : undefined,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("Pregunta guardada correctamente.", "success");
        setTimeout(() => {
          document.querySelector('button[type="submit"]').innerHTML =
            "Guardar pregunta";
          document.querySelector('button[type="submit"]').disabled = false;
          document.getElementById("preguntaForm").reset();
          document.getElementById("preguntaId").value = "";
          document.getElementById("opciones").innerHTML = "";
          cargarPreguntas();
          isEditing = false;
        }, 500);
        setTimeout(() => location.reload(), 500); // Recargar la página tras un breve retraso
      } else {
        showToast("Error al guardar la pregunta.", "danger");
        throw new Error("Error en la respuesta del servidor.");
      }
    })
    .catch((error) => {
      console.error("Error al guardar la pregunta:", error);
      showToast("Ocurrió un error al intentar guardar la pregunta.", "danger");
      document.querySelector('button[type="submit"]').innerHTML =
        "Guardar pregunta";
      document.querySelector('button[type="submit"]').disabled = false;
    });
}
