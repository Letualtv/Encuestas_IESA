<?php
if (isset($_GET['id'])) {
    $preguntaId = $_GET['id'];
    // Aquí puedes cargar los datos de la pregunta desde la base de datos usando $preguntaId
}

$encabezado = isset($pregunta['encabezado']) ? $pregunta['encabezado'] : null;

if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    $opciones = $pregunta['opciones'];
    $opcionesKeys = array_keys($opciones);

    foreach ($opcionesKeys as $index => $clave) {
        $label = $opciones[$clave]['label'];
        $subLabel = $opciones[$clave]['subLabel'];
        $subId = ceil(($index + 1) / 2);

        ?>

        <div class="row mb-3 align-items-center">
            <div class="col-12 col-lg-3 ">
                <p class="align-bottom"><?= $label ?></p>
            </div>
        
            <div class="row mb-3 align-items-center col-lg-9 col-12 mx-auto">
            <?php foreach ($subLabel as $key => $text): 
                // Verifica si hay una respuesta guardada para 'No sabe' y 'No contesta'
                $checked1 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para valores de 1 a 7
                $checked88 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para 'No sabe'
                $checked99 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para 'No contesta'
                
                if (isset($respuestas[$key])) {
                    $respuesta = $respuestas[$key];
                    if ($respuesta >= 1 && $respuesta <= 7) {
                        $checked1 = 'checked';
                    } elseif ($respuesta == 88) {
                        $checked88 = 'checked';
                    } elseif ($respuesta == 99) {
                        $checked99 = 'checked';
                    }
                }
                ?>
            <div class="col-12 col-lg-3">
                <label for="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>" class="form-label"><?= $text ?></label>
            </div>

            <div class="col-12 col-md-5 col-lg-auto d-flex mx-auto">
                <div class="btn-group my-3 my-lg-0 ps-lg-4 " >
                <?php 
                    for ($i = $keyUno; $i <= $keyDos; $i++):
                        $checked = '';
                        if (isset($respuestas[$key]) && $respuestas[$key] == $i) {
                            $checked = 'checked';
                        }
                        ?>
                        <input type="radio" required class="btn-check " name="<?= $key ?>" 
                        id="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_<?= $i ?>" value="<?= $i ?>" <?= $checked ?> autocomplete="off"> 
                        <label class="btn btn-outline-primary px-3 mb-2" for="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_<?= $i ?>"><?= $i ?></label>
                        <?php endfor; ?>
                </div>
            </div>

            <div class="col-12 col-md-5 col-lg-3 pb-3 pb-md-0 ms-auto">
                <div class="justify-content-md-end justify-content-between d-flex gap-2 ">
                    <input type="radio" required name="<?= $key ?>" value="88" class="btn-check"
                        id="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_88" <?= $checked88 ?> autocomplete="off">
                    <label class="btn btn-outline-secondary" for="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_88">No sabe</label>

                    <input type="radio" required name="<?= $key ?>" value="99" class="btn-check"
                        id="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_99" <?= $checked99 ?> autocomplete="off">
                    <label class="btn btn-outline-secondary" for="q<?= $clave ?>_<?= $subId ?>_<?= $key ?>_99">No contesta</label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
</div>

<?php
    }
}
?>
