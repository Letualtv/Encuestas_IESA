<?php
if (isset($_GET['id'])) {
    $preguntaId = $_GET['id'];
    // Aquí puedes cargar los datos de la pregunta desde la base de datos o un array
    // Por ejemplo: $pregunta = obtenerPreguntaPorId($preguntaId);
}
?>

<?php if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])): ?>
    <?php foreach ($pregunta['opciones'] as $clave => $opcion): ?>
        <?php
        // Verifica si hay una respuesta en las respuestas recuperadas para esta pregunta
        $checked = '';
        if (isset($respuestas[$pregunta['id']]) && $respuestas[$pregunta['id']] == $clave) {
            $checked = 'checked';
        }

        // Genera un id único para cada opción de radio
        $inputId = $pregunta['id'] . '_' . $clave;
        ?>
        <div class="form-check pb-1">
            <input class="form-check-input required" type="radio" name="<?= $pregunta['id'] ?>" id="<?= $inputId ?>" value="<?= $clave ?>" required <?= $checked ?>>
            <label class="align-middle form-check-label" for="<?= $inputId ?>"><?= $opcion ?></label>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
