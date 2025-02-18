// Función para guardar una pregunta
document.getElementById("preguntaForm").addEventListener("submit", function (event) {
  event.preventDefault();

  // Mostrar el modal de confirmación
  const confirmSaveModal = new bootstrap.Modal(document.getElementById("confirmSaveModal"));
  const confirmSaveButton = document.getElementById("confirmSaveButton");
  confirmSaveModal.show();

  // Acción al confirmar el guardado
  confirmSaveButton.onclick = () => {
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.innerHTML = "Guardando...";
    submitButton.disabled = true;

    // Recopilar datos generales
    const preguntaId = document.getElementById("preguntaId").value;
    const titulo = document.getElementById("titulo").value;
    const n_pag = document.getElementById("n_pag").value;
    const tipo = document.getElementById("tipo").value;
    const subTitulo = document.getElementById("subTitulo").value;

    // Recopilar la descripción (solo si está activada)
  const descripcion = recopilarDescripcion();

    // Recopilar valores específicos para numberInput
    const valores =
      tipo === "numberInput"
        ? {
            min: document.getElementById("min")?.value || "",
            max: document.getElementById("max")?.value || "",
            placeholder: document.getElementById("placeholder")?.value || "",
          }
        : {};

    // Recopilar las reglas de filtro
    const filtro = {};
    document.querySelectorAll("#filtroRulesContainer > div").forEach((ruleDiv) => {
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
          const valorRangoAbajo = parametrosDiv.querySelector("input")?.value.trim();
          if (valorRangoAbajo) rango = `${valorRangoAbajo}-`;
          break;
        case "rango-arriba":
          const valorRangoArriba = parametrosDiv.querySelector("input")?.value.trim();
          if (valorRangoArriba) rango = `${valorRangoArriba}+`;
          break;
        case "exclusion":
          const valorExclusion = parametrosDiv.querySelector("input")?.value.trim();
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

    // Recopilar las opciones según el tipo de pregunta
    const tipoPregunta = document.getElementById("tipo").value;
    let opcionesObj = {}; // Inicializar opcionesObj aquí

    if (tipoPregunta === "formSelect") {
      // Para preguntas de tipo formSelect, incluir sublabels
      Array.from(document.querySelectorAll('[name="claves[]"]')).forEach((claveInput, index) => {
        const clave = claveInput.value.trim();
        const opcion = document.querySelectorAll('[name="opciones[]"]')[index].value.trim();

        if (clave && opcion) {
          opcionesObj[clave] = {
            label: opcion,
            subLabel: {},
          };

          // Buscar el contenedor de sublabels asociado a esta opción
          const subLabelsContainer = claveInput.closest(".input-group").nextElementSibling;
          if (
            subLabelsContainer &&
            subLabelsContainer.classList.contains("sublabels-container")
          ) {
            Array.from(subLabelsContainer.querySelectorAll('[name="subClaves[]"]')).forEach(
              (subClaveInput, subIndex) => {
                const subClave = subClaveInput.value.trim();
                const subValor = subLabelsContainer.querySelectorAll('[name="subValores[]"]')[
                  subIndex
                ]?.value.trim();

                if (subClave && subValor) {
                  opcionesObj[clave].subLabel[subClave] = subValor;
                }
              }
            );
          }
        }
      });
    } else {
      // Para otros tipos de preguntas, guardar solo las opciones principales
      Array.from(document.querySelectorAll('[name="claves[]"]')).forEach((claveInput, index) => {
        const clave = claveInput.value.trim();
        const opcion = document.querySelectorAll('[name="opciones[]"]')[index]?.value.trim();

        if (clave && opcion) {
          opcionesObj[clave] = opcion;
        }
      });
    }

    // Validar que opcionesObj esté definido
    if (!Object.keys(opcionesObj).length) {
      showToast("No se han agregado opciones válidas.", "danger");
      submitButton.innerHTML = "Guardar pregunta";
      submitButton.disabled = false;
      return;
    }

    // Enviar los datos al backend
    fetch("includesCP/guardarPregunta.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: preguntaId,
        titulo,
        n_pag,
        tipo,
        subTitulo,
        opciones: opcionesObj,
        valores,
        filtro: Object.keys(filtro).length ? filtro : {},
        cabecera: descripcion || undefined, // Incluir la descripción solo si existe
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showToast("Pregunta guardada correctamente.", "success");
          setTimeout(() => {
            submitButton.innerHTML = "Guardar pregunta";
            submitButton.disabled = false;
            document.getElementById("preguntaForm").reset();
            document.getElementById("preguntaId").value = "";
            document.getElementById("opciones").innerHTML = "";
            cargarPreguntas();
            isEditing = false;
          }, 500);
        } else {
          showToast("Error al guardar la pregunta.", "danger");
          submitButton.innerHTML = "Guardar pregunta";
          submitButton.disabled = false;
        }
      })
      .catch((error) => {
        console.error("Error al guardar la pregunta:", error);
        showToast("Ocurrió un error al intentar guardar la pregunta.", "danger");
        submitButton.innerHTML = "Guardar pregunta";
        submitButton.disabled = false;
      });

    confirmSaveModal.hide(); // Cerrar el modal después de enviar los datos
  };
});