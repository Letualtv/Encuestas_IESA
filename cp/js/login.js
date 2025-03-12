document.addEventListener('DOMContentLoaded', () => {
    // Agregar listener al formulario de inicio de sesión
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            console.log('Datos enviados:', { email, password }); // Depuración

            try {
                const response = await fetch('includesCP/loginDB.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                });

                console.log('Respuesta del servidor:', response); // Depuración

                if (!response.ok) throw new Error('Error en la solicitud');

                const data = await response.json();
                console.log('Datos recibidos:', data); // Depuración

                if (!data.success) {
                    document.getElementById('mensaje').innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    return;
                }

                // Almacenar los datos del usuario en localStorage
                const usuario = {
                    ID: data.data.id,
                    Nombre: data.data.nombre,
                    Email: data.data.email,
                    Rol: data.data.rol
                };
                localStorage.setItem('usuario', JSON.stringify(usuario));

                // Redirigir al panel de control
                window.location.href = 'controlPanel.php';
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('mensaje').innerHTML = `<div class="alert alert-danger">Hubo un problema al iniciar sesión.</div>`;
            }
        });
    } else {
        console.error('El formulario de inicio de sesión no existe en esta página.');
    }
});


