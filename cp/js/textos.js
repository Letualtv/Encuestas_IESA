function editarUsuario(id) {
  // Obtener datos del usuario (aquí deberías hacer una petición AJAX al servidor)
  // Por simplicidad, utilizaremos datos ficticios
  const usuario = {
      id: id,
      nombre: 'Juan Pérez',
      email: 'juan.perez@example.com'
  };

  // Rellenar el formulario del modal con los datos del usuario
  document.getElementById('editarUsuarioId').value = usuario.id;
  document.getElementById('editarNombre').value = usuario.nombre;
  document.getElementById('editarEmail').value = usuario.email;

  // Mostrar el modal
  const editarModal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
  editarModal.show();
}

// Manejar el envío del formulario de edición
document.getElementById('editarUsuarioForm').addEventListener('submit', function(event) {
  event.preventDefault();

  // Obtener los datos del formulario
  const usuarioId = document.getElementById('editarUsuarioId').value;
  const nombre = document.getElementById('editarNombre').value;
  const email = document.getElementById('editarEmail').value;
  const contrasena = document.getElementById('editarContrasena').value;

  // Aquí deberías enviar los datos al servidor vía AJAX para actualizar el usuario
  // ...

  // Cerrar el modal y mostrar un mensaje de éxito
  const editarModal = bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal'));
  editarModal.hide();
  alert('Usuario actualizado correctamente.');
});

function eliminarUsuario(id) {
  // Obtener el nombre del usuario (en una implementación real, obtendrías esto del servidor)
  const nombreUsuario = 'Juan Pérez';

  document.getElementById('eliminarUsuarioId').value = id;
  document.getElementById('eliminarUsuarioNombre').textContent = nombreUsuario;

  const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarUsuarioModal'));
  eliminarModal.show();
}

document.getElementById('confirmarEliminarUsuario').addEventListener('click', function() {
  const usuarioId = document.getElementById('eliminarUsuarioId').value;

  // Aquí deberías enviar una petición al servidor para eliminar al usuario
  // ...

  // Cerrar el modal y mostrar un mensaje de éxito
  const eliminarModal = bootstrap.Modal.getInstance(document.getElementById('eliminarUsuarioModal'));
  eliminarModal.hide();
  alert('Usuario eliminado correctamente.');
});
