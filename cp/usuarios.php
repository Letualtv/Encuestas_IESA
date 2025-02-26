<!-- controlUsuarios.php -->
<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>

    
    <div class="container-fluid">
        <div class="row pb-3 gy-2 align-items-stretch">
            <!-- Sección de Usuarios -->
            <div class="col-12 col-md-8 border-end">
                <!-- Lista de Usuarios -->
                <div class="mb-2 h-100 ">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Usuarios del panel de control registrados</h5>
                    <hr>
                    <div class="bg-body p-2">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($usuario['ID']) ?></td>
                                            <td><?= htmlspecialchars($usuario['Nombre']) ?></td>
                                            <td><?= htmlspecialchars($usuario['Email']) ?></td>
                                            <td><?= htmlspecialchars($usuario['Rol']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary me-1" onclick="editarUsuario(<?= $usuario['ID'] ?>)">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?= $usuario['ID'] ?>)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Administradores -->
            <div class="col-12 col-md-4">
                <div class="mb-2 h-100">
                    <h5 class="mb-0"><i class="fa-solid fa-user-shield me-2"></i>Administradores</h5>
                    <hr>
                    <div class="bg-body p-2">
                        <ul class="list-group">
                            <?php if (!empty($administradores)): ?>
                                <?php foreach ($administradores as $admin): ?>
                                    <li class="list-group-item">
                                        <i class="fa-solid fa-shield-alt me-2"></i>
                                        <?= htmlspecialchars($admin['Nombre']) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No hay administradores registrados.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

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
                                <label for="editarNivel" class="form-label">Rol</label>
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
