// quillManager.js
var Quill = window.Quill;

var quillInstances = {}; // Objeto para almacenar múltiples instancias de Quill

function inicializarQuill(id) {
  var quill = new Quill(`#${id}`, {
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline'],
        ['link', 'image'],
        [{ list: 'ordered' }, { list: 'bullet' }],
      ],
    },
    theme: 'bubble',
  });

  quillInstances[id] = quill;
}

// ---












function inicializarQuillParaInputs(selector) {
  var inputs = document.querySelectorAll(selector);

  inputs.forEach(function(input) {
    var id = input.id;
    inicializarQuill(id);
  });
}

function obtenerContenidoQuill(id) {
    var quill = quillInstances[id];
    return quill.root.innerHTML;
  }

window.QuillManager = {
  inicializarQuill: inicializarQuill,
  inicializarQuillParaInputs: inicializarQuillParaInputs,
  obtenerContenidoQuill: obtenerContenidoQuill,
};