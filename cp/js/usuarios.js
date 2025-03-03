document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();

    // Mostrar el botón "Crear Usuario" solo para administradores
    const usuarioActualEsAdmin = verificarSiEsAdministrador();
    if (usuarioActualEsAdmin) {
        document.getElementById('btnCrearUsuario').style.display = 'block';
    }

    // Mostrar el nombre del usuario y su rol
    mostrarInfoUsuario();
});

// Función para cargar la lista de usuarios desde el servidor
async function cargarUsuarios() {
    try {
        const response = await fetch('includesCP/usuariosDB.php?action=listar&page=1&limit=30');
        if (!response.ok) throw new Error('Error al cargar usuarios');

        const { success, message, data } = await response.json();
        if (!success) throw new Error(message || 'Error desconocido');

        actualizarTabla(data);
    } catch (error) {
        console.error('Error:', error);
        showToast('Hubo un problema al cargar los usuarios.', 'danger');
    }
}

function actualizarTabla(usuarios) {
    const tbodyUsuarios = document.getElementById('tabla-usuarios');
    const ulAdministradores = document.getElementById('lista-administradores');

    tbodyUsuarios.innerHTML = '';
    ulAdministradores.innerHTML = '';

    usuarios.forEach(usuario => {
        if (usuario.Rol === 'administrador') {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `<strong>${usuario.Nombre}</strong> 
                            <span class="badge bg-primary">Administrador</span>`;
            ulAdministradores.appendChild(li);
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${usuario.ID}</td>
                <td>${usuario.Nombre}</td>
                <td>${usuario.Email}</td>
                <td>${usuario.Rol}</td>
                <td>
                    ${verificarSiEsAdministrador() ? `
                        <button class="btn btn-sm btn-warning" onclick="editarUsuario(${usuario.ID})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(${usuario.ID})">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </td>
            `;
            tbodyUsuarios.appendChild(row);
        }
    });
}

// Abrir el modal de edición
window.editarUsuario = async function (id) {
    try {
        const response = await fetch(`includesCP/usuariosDB.php?action=obtener&id=${id}`);
        if (!response.ok) throw new Error('Error al obtener el usuario');

        const { success, message, data } = await response.json();
        if (!success) throw new Error(message || 'Error desconocido');

        const usuario = data;

        // Verificar si el usuario actual es administrador
        const usuarioActualEsAdmin = verificarSiEsAdministrador();
        if (!usuarioActualEsAdmin) {
            showToast('Solo los administradores pueden editar usuarios.', 'warning');
            return;
        }

        // Cargar los datos del usuario en el modal
        document.getElementById('editarUsuarioId').value = usuario.ID;
        document.getElementById('editarNombre').value = usuario.Nombre;
        document.getElementById('editarEmail').value = usuario.Email;

        // Mostrar el campo de contraseña solo para administradores
        const passField = document.getElementById('editarPassContainer');
        if (usuarioActualEsAdmin) {
            passField.style.display = 'block';
            document.getElementById('editarPass').value = usuario.Pass; // Mostrar la contraseña en texto plano
        } else {
            passField.style.display = 'none';
        }

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        modal.show();
    } catch (error) {
        console.error('Error:', error);
        showToast('Hubo un problema al cargar los datos del usuario.', 'danger');
    }
};

// Guardar cambios del usuario editado
document.getElementById('editarUsuarioForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    formData.append('action', 'editar'); // Agregar el campo action

    try {
        const response = await fetch('includesCP/usuariosDB.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Error al guardar los cambios');

        const { success, message } = await response.json();
        if (!success) throw new Error(message || 'Error desconocido');

        showToast('Cambios guardados correctamente.', 'success');
        cargarUsuarios(); // Recargar la lista de usuarios
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario'));
        modal.hide();
    } catch (error) {
        console.error('Error:', error);
        showToast('Hubo un problema al guardar los cambios.', 'danger');
    }
});

// Manejar el envío del formulario para crear un nuevo usuario
document.getElementById('crearUsuarioForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    formData.append('action', 'crear'); // Agregar el campo action

    try {
        const response = await fetch('includesCP/usuariosDB.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Error al crear el usuario');

        const { success, message } = await response.json();
        if (!success) throw new Error(message || 'Error desconocido');

        showToast('Usuario creado correctamente.', 'success');
        cargarUsuarios(); // Recargar la lista de usuarios
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearUsuario'));
        modal.hide();
    } catch (error) {
        console.error('Error:', error);
        showToast('Hubo un problema al crear el usuario.', 'danger');
    }
});

// Confirmar eliminación usando un modal
window.confirmarEliminar = function (id) {
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarBorrado'));
    document.getElementById('confirmarEliminarBtn').onclick = async function () {
        try {
            const response = await fetch(`includesCP/usuariosDB.php?action=eliminar&id=${id}`, { method: 'DELETE' });
            if (!response.ok) throw new Error('Error al eliminar el usuario');

            const { success, message } = await response.json();
            if (!success) throw new Error(message || 'Error desconocido');

            showToast('Usuario eliminado correctamente.', 'success');
            cargarUsuarios(); // Recargar la lista de usuarios
            modal.hide(); // Cerrar el modal
        } catch (error) {
            console.error('Error:', error);
            showToast('Hubo un problema al eliminar el usuario.', 'danger');
        }
    };
    modal.show();
};

// Función para verificar si el usuario actual es administrador
function verificarSiEsAdministrador() {
    const usuarioActual = JSON.parse(localStorage.getItem('usuario'));
    if (usuarioActual && usuarioActual.Rol === 'administrador') {
        return true;
    }
    return false;
}


function mostrarInfoUsuario() {
    const usuarioActual = JSON.parse(localStorage.getItem('usuario'));

    // Verificar si el usuario actual existe
    if (!usuarioActual || !usuarioActual.Rol) {
        console.error('Datos del usuario no disponibles.');
        return;
    }

    const infoUsuarioDiv = document.getElementById('infoUsuario');
    infoUsuarioDiv.innerHTML = `
        <span class="me-2">${usuarioActual.Nombre}</span>
        <span class="badge ${usuarioActual.Rol === 'administrador' ? 'bg-primary' : 'bg-secondary'}">
            ${usuarioActual.Rol.charAt(0).toUpperCase() + usuarioActual.Rol.slice(1)}
        </span>
    `;
}