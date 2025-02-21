<?php include 'vistasCP/navbarCP.php'; ?>

<section class="container-fluid d-flex flex-column min-vh-100">
    <div class="row flex-grow-1">
        <!-- Barra lateral izquierda -->
        <div class="col-md-3 bg-light border-end">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Secciones</h5>
                </div>
                <div class="list-group">
                    <?php
                    $jsonFile = __DIR__ . '../../models/textos.json';
                    $content = json_decode(file_get_contents($jsonFile), true);

                    foreach ($content as $section => $texts): ?>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action" onclick="loadSection('<?php echo $section; ?>')"><?php echo ucfirst($section); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Panel de modificación -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edición de textos</h5>
                    <button type="button" class="btn btn-outline-secondary" onclick="undoChange()">Deshacer</button>
                </div>
                <div class="card-body">
                    <form method="POST" action="">

                        <!-- Contenedor para los textos -->
                        <div id="textsContainer"></div>

                        <!-- Botón para agregar nuevo texto -->
                        <button type="button" class="btn btn-primary mb-3" onclick="addText()">Añadir Nuevo Texto</button>

                        <!-- Botón de Guardar -->
                        <button type="button" class="btn btn-success" onclick="showSaveModal()">Guardar Cambios</button>

                        <!-- Modal para confirmar guardado -->
                        <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveModalLabel">Confirmar Guardado</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Está seguro de que desea guardar los cambios?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal para confirmar salida sin guardar -->
                        <div class="modal fade" id="confirmExitModal" tabindex="-1" aria-labelledby="confirmExitModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmExitModalLabel">Cambios sin Guardar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Tiene cambios sin guardar. ¿Está seguro de que desea salir sin guardar?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" onclick="confirmExit()">Salir</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11;"></div>

    <script id="originalContent" type="application/json"><?php echo json_encode($content); ?></script>
    <script src="js/textos.js"></script>
</section>

<?php include 'vistasCP/footerCP.php'; ?>
<script src="js/utils.js"></script>
