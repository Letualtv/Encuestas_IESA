<!-- controlUsuarios.php -->
<section class="d-flex flex-column min-vh-100">
    <?php include 'vistasCP/navbarCP.php'; ?>

    <div class="container-fluid">
        <div class="row pb-3 gy-2 align-items-stretch ">
            <!-- Sección de Usuarios -->
            <div class="col-12 col-md-8 border-end ">
                <!-- Lista de Usuarios -->
                <div class=" h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Usuarios del panel de control registrados</h5>
                        <!-- Botón para crear un nuevo usuario -->
                        <div class="ms-auto">
                            <button
                                id="btnCrearUsuario"
                                class="btn btn-primary btn-sm align-middle"
                                style="display: none;"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCrearUsuario">
                                <i class="fas fa-plus me-2"></i>Crear usuario
                            </button>
                        </div>

                    </div>
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
                            <tbody id="tabla-usuarios">
                                <!-- Aquí se cargarán los usuarios dinámicamente -->
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
                        <ul class="list-group" id="lista-administradores">
                            <!-- Aquí se cargarán los administradores dinámicamente -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para crear un nuevo usuario -->
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="crearUsuarioForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCrearUsuarioLabel">Crear un nuevo usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="crearNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="crearNombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="crearEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="crearEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="crearPass" class="form-label">Contraseña</label>
                                <input type="text" class="form-control" id="crearPass" name="pass" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar usuario -->
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editarUsuarioForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar usuario</h5>
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
                            <div class="mb-3" id="editarPassContainer" style="display: none;">
                                <label for="editarPass" class="form-label">Contraseña</label>
                                <input type="text" class="form-control" id="editarPass" name="pass" placeholder="Dejar en blanco para no cambiar">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para confirmar eliminación -->
        <div class="modal fade" id="modalConfirmarBorrado" tabindex="-1" aria-labelledby="modalConfirmarBorradoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalConfirmarBorradoLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar este usuario?</p>
                        <p>Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmarEliminarBtn">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'vistasCP/footerCP.php'; ?>
</section>

<!-- Scripts -->
<script src="js/utils.js"></script>
<script src="js/usuarios.js"></script>