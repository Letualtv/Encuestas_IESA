<!-- controlUsuarios.php -->
<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>

    <div class="container-fluid">
        <!-- Título de la página -->
        <h5 class="mb-3"><i class="fa-solid fa-users me-2"></i>Control de Usuarios</h5>
        <hr>

        <div class="row pb-3 gy-2 align-items-stretch">
            <!-- Sección de Usuarios -->
            <div class="col-12 col-md-8 border-end">
                <!-- Lista de Usuarios -->
                <div class="mb-2 h-100 ">
                    <h6 class="mb-0"><i class="fa-solid fa-user me-2"></i>Usuarios Registrados</h6>
                    <hr>
                    <div class="bg-body p-2">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Nivel</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Supongamos que tienes una función para obtener usuarios y el usuario actual
                              /*   $usuarioActual = obtenerUsuarioActual(); // Función que obtiene el usuario en sesión
                                $usuarios = obtenerUsuarios(); // Función que obtiene todos los usuarios */

                                foreach ($usuarios as $usuario) {
                                    // Si es administrador, lo excluimos de esta tabla
                                    if ($usuario['nivel'] === 'administrador') {
                                        continue;
                                    }

                                    echo '<tr>';
                                    echo '<td>' . $usuario['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($usuario['nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                                    echo '<td>' . htmlspecialchars($usuario['nivel']) . '</td>';
                                    echo '<td>';
                                    // Verificamos si el usuario actual tiene permiso para modificar/eliminar
                                    if ($usuarioActual['nivel'] === 'administrador' || $usuarioActual['id'] === $usuario['id']) {
                                        echo '<button class="btn btn-sm btn-primary me-1" onclick="editarUsuario(' . $usuario['id'] . ')">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>';
                                        echo '<button class="btn btn-sm btn-danger" onclick="eliminarUsuario(' . $usuario['id'] . ')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>';
                                    } else {
                                        echo '<span class="text-muted">Sin permisos</span>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Administradores -->
            <div class="col-12 col-md-4">
                <div class="mb-2 h-100">
                    <h6 class="mb-0"><i class="fa-solid fa-user-shield me-2"></i>Administradores</h6>
                    <hr>
                    <div class="bg-body p-2">
                        <ul class="list-group">
                            <?php
                            foreach ($usuarios as $usuario) {
                                if ($usuario['nivel'] === 'administrador') {
                                    echo '<li class="list-group-item">';
                                    echo '<i class="fa-solid fa-shield-alt me-2"></i>';
                                    echo htmlspecialchars($usuario['nombre']);
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales -->
        <!-- Modal para editar usuario -->
        <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editarUsuarioForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editarUsuarioId" name="id">
                            <div class="mb-3">
                                <label for="editarNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editarNombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editarEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarNivel" class="form-label">Nivel</label>
                                <select class="form-select" id="editarNivel" name="nivel" required>
                                    <option value="usuario">Usuario</option>
                                    <option value="editor">Editor</option>
                                    <!-- No permitimos cambiar a administrador desde aquí -->
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        

    </div>

    <?php include 'vistasCP/footerCP.php'; ?>
</section>

<!-- Scripts -->
<script src="js/usuarios.js"></script>
