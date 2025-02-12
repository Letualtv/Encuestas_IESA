// Variable global para rastrear si se está editando una pregunta
let isEditing = false;

// Función para agregar una nueva opción dinámicamente
function agregarOpcion(clave = "", opcion = "") {
    const opcionesDiv = document.getElementById("opcionesContainer"); // Cambiado a opcionesContainer
    const nuevaOpcion = document.createElement("div");
    nuevaOpcion.classList.add("input-group", "mb-2");
    nuevaOpcion.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
            <i class="fa-solid fa-trash"></i>
        </button>
        <input type="text" class="form-control shadow-sm" name="claves[]" placeholder="Clave" value="${clave}" required>
        <input type="text" class="form-control w-75 shadow-sm" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
    opcionesDiv.appendChild(nuevaOpcion);

    // Asegurarse de que el botón "Agregar Opción" exista solo una vez
    let addButtonContainer = document.querySelector(".add-option-container");
    if (!addButtonContainer) {
        addButtonContainer = document.createElement("div");
        addButtonContainer.classList.add("add-option-container", "my-2");
        addButtonContainer.innerHTML = `
            <a type="button" class="hover-zoom" onclick="agregarOpcion()">
                <i class="fa-xl fa-solid fa-circle-plus"></i>
            </a>
        `;
        opcionesDiv.parentElement.appendChild(addButtonContainer);
    }
}

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

// Función para eliminar una opción dinámicamente
function eliminarOpcion(button) {
    button.parentElement.remove();
}

function editarPregunta(id) {
    fetch(`obtenerPreguntas.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(pregunta => {
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
            opcionesDiv.innerHTML = ""; // Limpiar opciones previas

// Agregar las opciones existentes
const opciones = pregunta.opciones || {};
Object.keys(opciones).forEach((key) => {
    agregarOpcion(key, opciones[key]);
});

            if (pregunta.tipo === "numberInput") {
                document.getElementById("min").value = pregunta.valores?.min || "";
                document.getElementById("max").value = pregunta.valores?.max || "";
                document.getElementById("placeholder").value = pregunta.valores?.placeholder || "";
            }

            ajustarParametros();
            cargarJumpRules(pregunta.jump_rules || {});
            document.getElementById('mostrar-jump-rules').checked = !!Object.keys(pregunta.jump_rules || {}).length;
            mostrarJumpRules();
            isEditing = true;
        })
        .catch(error => {
            console.error("Error al cargar los datos de la pregunta:", error);
            showToast("Ocurrió un error al intentar cargar los datos de la pregunta.", "danger");
        });
}
// Función para guardar una pregunta
document.getElementById("preguntaForm").addEventListener("submit", function (event) {
    event.preventDefault();
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.innerHTML = "Guardando...";
    submitButton.disabled = true;

    const preguntaId = document.getElementById("preguntaId").value;
    const titulo = document.getElementById("titulo").value;
    const n_pag = document.getElementById("n_pag").value;
    const tipo = document.getElementById("tipo").value;
    const subTitulo = document.getElementById("subTitulo").value;
    const opciones = Array.from(document.querySelectorAll('[name="opciones[]"]')).map(input => input.value);
    const claves = Array.from(document.querySelectorAll('[name="claves[]"]')).map(input => input.value);

    const valores =
        tipo === "numberInput"
            ? {
                min: document.getElementById("min").value,
                max: document.getElementById("max").value,
                placeholder: document.getElementById("placeholder").value,
            }
            : {};

    // Recopilar las reglas de filtro (si existen)
    const filtro = {};
    const rangos = Array.from(document.querySelectorAll('[name="jump_rules[rango][]"]')).map(input => input.value);
    const paginasDestino = Array.from(document.querySelectorAll('[name="jump_rules[paginaDestino][]"]')).map(input => input.value);

    rangos.forEach((rango, index) => {
        if (rango && paginasDestino[index]) {
            filtro[rango] = parseInt(paginasDestino[index]);
        }
    });

    fetch("guardarPregunta.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: preguntaId,
            titulo,
            n_pag,
            tipo,
            subTitulo,
            claves,
            opciones,
            valores,
            filtro: Object.keys(filtro).length ? filtro : undefined, // Incluir filtro solo si tiene datos
        }),
    })
        .then(response => response.json())
        .then(data => {
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
        .catch(error => {
            console.error("Error al guardar la pregunta:", error);
            showToast("Ocurrió un error al intentar guardar la pregunta.", "danger");
            submitButton.innerHTML = "Guardar pregunta";
            submitButton.disabled = false;
        });
});