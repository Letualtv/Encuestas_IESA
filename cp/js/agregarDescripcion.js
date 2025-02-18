// Función para alternar la visibilidad de la descripción
function toggleDescripcion() {
  const mostrarDescripcion = document.getElementById("mostrar-descripcion");
  const descripcionContainer = document.getElementById("descripcionContainer");

  // Validar que los elementos existan
  if (!mostrarDescripcion || !descripcionContainer) {
    console.error("Error: Elementos 'mostrar-descripcion' o 'descripcionContainer' no existen.");
    return;
  }

  // Alternar la visibilidad del contenedor
  descripcionContainer.style.display = mostrarDescripcion.checked ? "block" : "none";
}
// Función para recopilar los datos de la descripción
function recopilarDescripcion() {
  const mostrarDescripcion = document.getElementById("mostrar-descripcion");

  // Validar que el interruptor exista
  if (!mostrarDescripcion) {
    console.error("Error: El interruptor 'mostrar-descripcion' no existe.");
    return null;
  }

  // Si el interruptor no está activado, no se guarda nada
  if (!mostrarDescripcion.checked) {
    return null;
  }

  // Validar que los campos de descripción existan
  const texto1Input = document.querySelector("#descripcionRule .texto1");
  const listaInput = document.querySelector("#descripcionRule .lista");
  const texto2Input = document.querySelector("#descripcionRule .texto2");

  if (!texto1Input || !listaInput || !texto2Input) {
    console.error("Error: Alguno de los campos de descripción no existe.");
    return null;
  }

  // Recopilar los valores de los campos
  const texto1 = texto1Input.value.trim() || "";
  const lista = listaInput.value.trim() || "";
  const texto2 = texto2Input.value.trim() || "";

  // Validar que al menos un campo tenga contenido
  if (!texto1 && !lista && !texto2) {
    return null; // No hay descripción válida
  }

  // Devolver la descripción en el formato deseado
  return {
    texto1: texto1,
    lista: lista, // Guardar la lista tal como está (sin convertirla a HTML)
    texto2: texto2,
  };
}
