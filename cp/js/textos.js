let originalContent = JSON.parse(document.getElementById('originalContent').textContent);
let currentContent = JSON.parse(JSON.stringify(originalContent));
let isEditing = false;

// Función para cargar los textos de una sección
function loadSection(section) {
    const textsContainer = document.getElementById('textsContainer');
    textsContainer.innerHTML = '';

    if (section && currentContent[section]) {
        textsContainer.innerHTML += '<input type="hidden" name="section" value="' + section + '">';
        currentContent[section].forEach((text, index) => {
            addText(text.question, text.answer, false, index);
        });
    }
}

// Función para agregar una nueva área de texto
function addText(question = '', answer = '', editable = false, index = null) {
    const textsContainer = document.getElementById('textsContainer');

    const textGroup = document.createElement('div');
    textGroup.className = 'form-group mb-3 p-3 border rounded shadow-sm d-flex align-items-center';

    const textInputs = document.createElement('div');
    textInputs.className = 'flex-grow-1';

    const questionLabel = document.createElement('label');
    questionLabel.textContent = 'Pregunta';
    textInputs.appendChild(questionLabel);

    const questionInput = document.createElement('textarea');
    questionInput.name = `texts[${index}][question]`;
    questionInput.className = 'form-control mb-2';
    questionInput.rows = 1;
    questionInput.value = question;
    questionInput.disabled = !editable;
    textInputs.appendChild(questionInput);

    const answerLabel = document.createElement('label');
    answerLabel.textContent = 'Respuesta';
    textInputs.appendChild(answerLabel);

    const answerInput = document.createElement('textarea');
    answerInput.name = `texts[${index}][answer]`;
    answerInput.className = 'form-control mb-2';
    answerInput.rows = 2;
    answerInput.value = answer;
    answerInput.disabled = !editable;
    textInputs.appendChild(answerInput);

    textGroup.appendChild(textInputs);

    const buttonGroup = document.createElement('div');
    buttonGroup.className = 'd-flex flex-column align-items-end ms-3';

    const modifyButton = document.createElement('button');
    modifyButton.type = 'button';
    modifyButton.className = 'btn btn-warning mb-1';
    modifyButton.textContent = 'Modificar';
    modifyButton.onclick = function() {
        questionInput.disabled = false;
        answerInput.disabled = false;
        modifyButton.disabled = true;
        isEditing = true;
    };
    buttonGroup.appendChild(modifyButton);

    const saveButton = document.createElement('button');
    saveButton.type = 'button';
    saveButton.className = 'btn btn-success mb-1';
    saveButton.textContent = 'Guardar';
    saveButton.onclick = function() {
        saveQuestion(index);
        questionInput.disabled = true;
        answerInput.disabled = true;
        modifyButton.disabled = false;
        isEditing = false;
    };
    buttonGroup.appendChild(saveButton);

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-danger';
    removeButton.textContent = 'Eliminar';
    removeButton.onclick = function() {
        textsContainer.removeChild(textGroup);
    };
    buttonGroup.appendChild(removeButton);

    textGroup.appendChild(buttonGroup);

    textsContainer.appendChild(textGroup);
}

// Función para guardar una pregunta específica
function saveQuestion(index) {
    const questionInput = document.querySelector(`textarea[name="texts[${index}][question]"]`);
    const answerInput = document.querySelector(`textarea[name="texts[${index}][answer]"]`);

    if (questionInput && answerInput) {
        currentContent[document.querySelector('input[name="section"]').value][index] = {
            question: questionInput.value,
            answer: answerInput.value
        };

        originalContent = JSON.parse(JSON.stringify(currentContent));
        showToast('success', 'Pregunta guardada correctamente.');
    }
}

// Función para deshacer cambios
function undoChange() {
    const section = document.querySelector('input[name="section"]').value;
    currentContent = JSON.parse(JSON.stringify(originalContent));
    loadSection(section);
}

// Función para mostrar el modal de guardar
function showSaveModal() {
    const saveModal = new bootstrap.Modal(document.getElementById('saveModal'));
    saveModal.show();
}

// Función para mostrar el modal antes de salir si hay cambios sin guardar
window.onbeforeunload = function() {
    if (isEditing) {
        showModal('confirmExitModal');
        return false;
    }
};

// Función para mostrar modales
function showModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

// Función para mostrar notificaciones toast
function showToast(type, message) {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    const toastContainer = document.getElementById('toastContainer');
    toastContainer.innerHTML = toastHTML;
    const toast = new bootstrap.Toast(toastContainer.firstElementChild);
    toast.show();
}
