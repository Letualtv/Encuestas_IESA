(() => {
    let currentSection = null; // Almacena la sección activa
    let quillEditors = {}; // Almacena instancias de Quill para cada cuadro de texto
    let originalContent = {}; // Almacena el contenido original de cada editor
  
    // Cargar una sección específica
    window.loadSection = function (section) {
      currentSection = section;
  
      // Desactivar todas las secciones en la barra lateral
      document.querySelectorAll(".list-group-item").forEach((item) => {
        item.classList.remove("active");
      });
  
      // Activar la sección seleccionada
      const selectedSection = document.querySelector(
        `a[onclick="loadSection('${section}')"]`
      );
      if (selectedSection) {
        selectedSection.classList.add("active");
      }
  
      // Cargar datos desde el JSON
      fetch("../models/textos.json")
        .then((response) => response.json())
        .then((data) => {
          const questions = data[section] || [];
          renderQuestions(questions);
        })
        .catch((error) => {
          console.error("Error al cargar las preguntas:", error);
          showToast("Error al cargar las preguntas", "danger");
        });
    };
  
    // Renderizar preguntas en el contenedor
    function renderQuestions(questions) {
      const container = document.getElementById("questionsContainer");
      container.innerHTML = ""; // Limpiar el contenedor
  
      questions.forEach((questionObj, index) => {
        const question = questionObj.question;
        const answer = questionObj.answer;
  
        // Generar un ID único para el cuadro de texto
        const cuadroTexto = `cuadroTexto-${index}`;
  
        // Guardar el contenido original
        originalContent[`pregunta-${cuadroTexto}`] = question;
        originalContent[`respuesta-${cuadroTexto}`] = answer;
  
        // HTML de la pregunta
        const questionDiv = document.createElement("div");
        questionDiv.classList.add("col-md-6", "mb-4"); // Añadir margen inferior y ancho adecuado
        questionDiv.innerHTML = `
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="fw-bold">Párrafo ${index + 1}</h5>
              <button type="button" class="btn btn-outline-primary btn-sm" 
                id="editButton-${cuadroTexto}" onclick="toggleEditMode('${cuadroTexto}', ${index})">
                <i class="fas fa-edit"></i> Modificar
              </button>
            </div>
            <hr>  
            <div class="row">
              <div class="col-12 mb-3 pb-2">
                <div id="pregunta-${cuadroTexto}" class="quill-editor card" style="font-size: 1rem;"></div>
              </div>
              <div class="col-12 mb-3">
                <div id="respuesta-${cuadroTexto}" class="quill-editor card"></div>
              </div>
            </div>
            <div class="text-end">
              <button type="button" class="btn btn-success btn-sm" id="saveButton-${cuadroTexto}" 
                onclick="confirmSaveChanges('${cuadroTexto}', '${currentSection}', ${index})" style="display: none;">
                <i class="fas fa-save pe-2"></i> Guardar
              </button>
            </div>
          </div>
        `;
        container.appendChild(questionDiv);
  
        // Inicializar Quill para la pregunta
        quillEditors[`pregunta-${cuadroTexto}`] = new Quill(`#pregunta-${cuadroTexto}`, {
          theme: "bubble",
          modules: {
            toolbar: [
              ["bold", "italic", "underline"], // Botones básicos
              ["clean"], // Limpiar formato
            ],
          },
        });
        quillEditors[`pregunta-${cuadroTexto}`].root.innerHTML = question;
  
        // Inicializar Quill para la respuesta
        quillEditors[`respuesta-${cuadroTexto}`] = new Quill(`#respuesta-${cuadroTexto}`, {
          theme: "bubble",
          modules: {
            toolbar: [
              [{ header: [4, 5, 6, false] }], // Tamaños de encabezado más pequeños
              ["bold", "italic", "underline"], // Botones básicos
              [{ list: "ordered" }, { list: "bullet" }], // Listas
              ["link"], // Botón para insertar enlaces
              ["clean"], // Limpiar formato
            ],
          },
        });
        quillEditors[`respuesta-${cuadroTexto}`].root.innerHTML = answer;
  
        // Deshabilitar los editores inicialmente
        quillEditors[`pregunta-${cuadroTexto}`].enable(false);
        quillEditors[`respuesta-${cuadroTexto}`].enable(false);
      });
    }
  
    // Habilitar/deshabilitar el modo edición
    window.toggleEditMode = function (cuadroTexto, index) {
      const preguntaEditor = quillEditors[`pregunta-${cuadroTexto}`];
      const respuestaEditor = quillEditors[`respuesta-${cuadroTexto}`];
      if (preguntaEditor && respuestaEditor) {
        const isEditable = !preguntaEditor.isEnabled();
        preguntaEditor.enable(isEditable);
        respuestaEditor.enable(isEditable);
  
        // Cambiar el estado del botón "Modificar"
        const editButton = document.getElementById(`editButton-${cuadroTexto}`);
        if (editButton) {
          if (isEditable) {
            editButton.classList.remove("btn-outline-primary");
            editButton.classList.add("btn-danger");
            editButton.innerHTML = '<i class="fa-solid fa-square-xmark"></i> Decartar cambios';
          } else {
              editButton.classList.add("btn-outline-primary");
              editButton.classList.remove("btn-danger");
            editButton.innerHTML = '<i class="fas fa-edit"></i> Modificar';
  
            // Restaurar el contenido original si se desactiva el modo edición
            preguntaEditor.root.innerHTML = originalContent[`pregunta-${cuadroTexto}`];
            respuestaEditor.root.innerHTML = originalContent[`respuesta-${cuadroTexto}`];
          }
        }
  
        // Mostrar/ocultar el botón de guardar
        const saveButton = document.getElementById(`saveButton-${cuadroTexto}`);
        if (saveButton) {
          saveButton.style.display = isEditable ? "inline-block" : "none";
        }

        showToast(
          isEditable ? "Modo edición habilitado" : "Modo edición deshabilitado",
          "info"
        );
      } else {
        showToast("No se pudo habilitar la edición", "danger");
      }
    };
  
    // Confirmar cambios antes de guardar
    window.confirmSaveChanges = function (cuadroTexto, section, index) {
      const preguntaEditor = quillEditors[`pregunta-${cuadroTexto}`];
      const respuestaEditor = quillEditors[`respuesta-${cuadroTexto}`];
  
      // Mostrar modal de confirmación
      const confirmationModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'), {});
      const modalBody = document.querySelector('#confirmSaveModal .modal-body');
      modalBody.innerHTML = `
        ¿Estás seguro de que deseas guardar esta pregunta?
      `;
  
      // Configurar el botón de confirmación en el modal
      const confirmButton = document.getElementById('confirmSaveButton');
      confirmButton.onclick = () => {
        saveChanges(cuadroTexto, section, index);
        confirmationModal.hide();
        
    };
  
      // Mostrar el modal
      confirmationModal.show();
    };
  
    // Guardar cambios
    window.saveChanges = function (cuadroTexto, section, index) {
      const preguntaEditor = quillEditors[`pregunta-${cuadroTexto}`];
      const respuestaEditor = quillEditors[`respuesta-${cuadroTexto}`];
      if (preguntaEditor && respuestaEditor) {
        const updatedQuestion = preguntaEditor.root.innerHTML.trim(); // Obtener el contenido actualizado
        const updatedAnswer = respuestaEditor.root.innerHTML.trim(); // Obtener el contenido actualizado
  
        // Enviar los datos al servidor
        fetch("includesCP/guardarTextos.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            section: section,
            index: index,
            question: updatedQuestion,
            answer: updatedAnswer,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showToast("Cambios guardados correctamente", "success");
  
              // Actualizar el contenido original después de guardar
              originalContent[`pregunta-${cuadroTexto}`] = updatedQuestion;
              originalContent[`respuesta-${cuadroTexto}`] = updatedAnswer;
  
              // Deshabilitar los editores después de guardar
              preguntaEditor.enable(false);
              respuestaEditor.enable(false);
  
              // Restaurar el botón "Modificar"
              const editButton = document.getElementById(`editButton-${cuadroTexto}`);
              if (editButton) {
                editButton.classList.remove("btn-danger");
                editButton.classList.add("btn-outline-primary");
                editButton.innerHTML = '<i class="fas fa-edit"></i> Modificar';
              }
  
              // Ocultar el botón de guardar
              const saveButton = document.getElementById(`saveButton-${cuadroTexto}`);
              if (saveButton) {
                saveButton.style.display = "none";
              }
            } else {
              showToast("Error al guardar los cambios", "danger");
            }
          })
          .catch((error) => {
            console.error("Error al guardar los cambios:", error);
            showToast("Error al guardar los cambios", "danger");
          });
      }
    };
  
    // Mostrar un toast (delegado a utils.js)
    function showToast(message, type = "info") {
      if (typeof window.showToast === "function") {
        window.showToast(message, type);
      } else {
        console.warn("La función showToast no está definida en utils.js");
      }
    }
  
    // Cargar la primera sección al iniciar
    document.addEventListener("DOMContentLoaded", () => {
      const firstSection = document.querySelector(".list-group-item");
      if (firstSection) {
        firstSection.click(); // Simular clic en la primera sección
      }
    });
  })();