// usuarios.js

document.addEventListener('DOMContentLoaded', function () {
    // Función para cargar la lista de usuarios desde el servidor
    async function cargarUsuarios() {
        try {
            const response = await fetch('usuariosDB.php?action=listar');
            if (!response.ok) throw new Error('Error al cargar usuarios');
            const usuarios = await response.json();
            actualizarTabla(usuarios);
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al cargar los usuarios.');
        }
    }

    // Actualizar la tabla de usuarios
    function actualizarTabla(usuarios) {
        const tbody = document.querySelector('.table tbody');
        tbody.innerHTML = ''; // Limpiar la tabla

        usuarios.forEach(usuario => {
            if (usuario.nivel === 'administrador') return; // Excluir administradores

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${usuario.id}</td>
                <td>${escapeHTML(usuario.nombre)}</td>
                <td>${escapeHTML(usuario.email)}</td>
                <td>${escapeHTML(usuario.nivel)}</td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" onclick="editarUsuario(${usuario.id})">
                        <i class="fa-solid fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${usuario.id})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Función para abrir el modal de edición
    window.editarUsuario = async function (id) {
        try {
            const response = await fetch(`usuariosDB.php?action=obtener&id=${id}`);
            if (!response.ok) throw new Error('Error al obtener el usuario');
            const usuario = await response.json();

            document.getElementById('editarUsuarioId').value = usuario.id;
            document.getElementById('editarNombre').value = usuario.nombre;
            document.getElementById('editarEmail').value = usuario.email;
            document.getElementById('editarNivel').value = usuario.nivel;

            const modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al cargar los datos del usuario.');
        }
    };

    // Guardar cambios del usuario editado
    document.getElementById('editarUsuarioForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('usuariosDB.php?action=editar', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Error al guardar los cambios');
            alert('Cambios guardados correctamente.');
            cargarUsuarios(); // Recargar la lista de usuarios
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal'));
            modal.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al guardar los cambios.');
        }
    });

    // Eliminar un usuario
    window.eliminarUsuario = async function (id) {
        if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) return;

        try {
            const response = await fetch(`usuariosDB.php?action=eliminar&id=${id}`, { method: 'DELETE' });
            if (!response.ok) throw new Error('Error al eliminar el usuario');
            alert('Usuario eliminado correctamente.');
            cargarUsuarios(); // Recargar la lista de usuarios
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al eliminar el usuario.');
        }
    };

    // Crear un nuevo usuario
    document.getElementById('crearUsuarioForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('usuariosDB.php?action=crear', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Error al crear el usuario');
            alert('Usuario creado correctamente.');
            cargarUsuarios(); // Recargar la lista de usuarios
            this.reset(); // Limpiar el formulario
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al crear el usuario.');
        }
    });

    // Función para escapar HTML y evitar inyecciones XSS
    function escapeHTML(str) {
        return str.replace(/[&<>"']/g, function (match) {
            const escapeMap = {
                '&': '&amp;',
                '<': '<',
                '>': '>',
                '"': '&quot;',
                "'": '&#39;'
            };
            return escapeMap[match];
        });
    }

    // Cargar usuarios al iniciar
    cargarUsuarios();
});