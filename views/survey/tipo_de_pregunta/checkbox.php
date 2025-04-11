<?php
if (isset($_GET['id'])) {
    $preguntaId = $_GET['id'];
    // Aquí puedes cargar los datos de la pregunta desde la base de datos o un array
    // Por ejemplo: $pregunta = obtenerPreguntaPorId($preguntaId);
}

if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        $checked = '';  // Inicializa la variable para verificar si la opción debe estar marcada

        if (isset($respuestas[$pregunta['id']])) {
            $respuestaArray = explode(', ', $respuestas[$pregunta['id']]);
            
            // Verifica si la respuesta es un valor numérico y no es uno de los valores esperados
            foreach ($respuestaArray as $respuesta) {
                if (!in_array($respuesta, range(1, 10)) && is_numeric($respuesta)) {
                    $inputDisabled = ''; // Habilita el input number si hay un valor guardado
                } elseif (in_array($clave, $respuestaArray)) {
                    $checked = 'checked';  // Marca la opción si coincide con la respuesta guardada
                }
            }
        }

        echo "
        <div class='form-check d-flex align-items-center'>
            <input 
                class='form-check-input me-2 main-checkbox' 
                type='checkbox' 
                name='{$pregunta['id']}[]' 
                id='checkbox-{$pregunta['id']}-{$clave}' 
                value='$clave' 
                $checked>
            <label class='form-check-label' for='checkbox-{$pregunta['id']}-{$clave}'>$opcion</label>";

        echo "</div>";
    }
}
?>

<script>
   
   (function() {
    const form = document.querySelector('form');
    const checkboxes = form.querySelectorAll('input[type=checkbox]');
    const checkboxLength = checkboxes.length;
    const firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;

    function init() {
        if (firstCheckbox) {
            for (let i = 0; i < checkboxLength; i++) {
                checkboxes[i].addEventListener('change', checkValidity);
            }

            checkValidity();
        }
    }

    function isChecked() {
        for (let i = 0; i < checkboxLength; i++) {
            if (checkboxes[i].checked) return true;
        }

        return false;
    }

    function checkValidity() {
        const errorMessage = !isChecked() ? 'Debe seleccionar al menos una opción.' : '';
        firstCheckbox.setCustomValidity(errorMessage);
    }

    init();
})();
</script>
