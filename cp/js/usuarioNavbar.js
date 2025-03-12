document.addEventListener('DOMContentLoaded', () => {
    // Mostrar la información del usuario al cargar la página
    mostrarInfoUsuario();

    // Manejar el cierre de sesión
    document.getElementById('btnCerrarSesion')?.addEventListener('click', async () => {
        try {
            const response = await fetch('includesCP/logout.php', {
                method: 'POST'
            });

            if (!response.ok) throw new Error('Error al cerrar sesión');

            const { success, message } = await response.json();
            if (!success) throw new Error(message || 'Error desconocido');

            // Limpiar los datos del usuario y redirigir al inicio de sesión
            localStorage.removeItem('usuario');
            window.location.href = 'login.php';
        } catch (error) {
            console.error('Error:', error);
            showToast('Hubo un problema al cerrar sesión.', 'danger'); // Usar la función showToast de utils.js
        }
    });
});

/**
 * Función para mostrar la información del usuario en el navbar.
 */
function mostrarInfoUsuario() {
    // Recuperar los datos del usuario desde localStorage
    const usuarioActual = JSON.parse(localStorage.getItem('usuario'));

    // Verificar si el usuario actual existe
    if (!usuarioActual || !usuarioActual.Rol) {
        console.error('Datos del usuario no disponibles.');
        return;
    }

    // Actualizar el nombre del usuario en el navbar
    document.getElementById('nombreUsuario').textContent = usuarioActual.Nombre;

    // Actualizar el rol del usuario en el badge
    const rolBadge = document.getElementById('rolUsuario');
    const rolFormateado = usuarioActual.Rol.charAt(0).toUpperCase() + usuarioActual.Rol.slice(1);
    rolBadge.textContent = rolFormateado;
    rolBadge.className = `badge ${usuarioActual.Rol === 'administrador' ? 'bg-primary' : 'bg-secondary'}`;
}