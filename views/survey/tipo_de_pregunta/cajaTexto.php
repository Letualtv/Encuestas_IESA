<?php
if (isset($_GET['id'])) {
    $preguntaId = $_GET['id'];
    // Aquí puedes cargar los datos de la pregunta desde la base de datos o un array
    // Por ejemplo: $pregunta = obtenerPreguntaPorId($preguntaId);
}

// Verifica si los datos básicos están presentes
if (isset($pregunta['id'])) {
    // Obtiene el valor guardado previamente para esta pregunta, si existe
    $valorGuardado = '';
    if (isset($respuestas[$pregunta['id']])) {
        $valorGuardado = htmlspecialchars($respuestas[$pregunta['id']]);
    }

    // Define el placeholder desde el JSON o usa un valor predeterminado
    $placeholder = ''; // Valor predeterminado
    if (isset($pregunta['placeholder'])) {
        $placeholder = htmlspecialchars($pregunta['placeholder']);
    }

    echo "
    <div class='form-group'>
        <textarea 
            class='form-control' 
            id='texto-{$pregunta['id']}' 
            name='{$pregunta['id']}' 
            rows='3' 
            placeholder='$placeholder'
            required>$valorGuardado</textarea>
        <div class='invalid-feedback'>
            Este campo es obligatorio. Por favor, escribe una respuesta.
        </div>
    </div>";
}
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Obtener el ID de la pregunta desde PHP
    const preguntaId = '<?php echo $pregunta['id']; ?>';
    const textarea = document.getElementById(`texto-${preguntaId}`);
    const form = document.querySelector('form');

    if (!textarea) {
        console.error(`No se encontró el elemento con ID 'texto-${preguntaId}'`);
        return;
    }

    // Función para validar el campo de texto
    function validateTextarea() {
        if (textarea.value.trim() === '') {
            textarea.setCustomValidity('Este campo es obligatorio.');
            textarea.classList.add('is-invalid'); // Muestra el error visualmente
        } else {
            textarea.setCustomValidity('');
            textarea.classList.remove('is-invalid'); // Elimina el error visualmente
        }
    }

    // Validar el campo cuando cambia su valor
    textarea.addEventListener('input', () => {
        validateTextarea();
    });

    // Validar el formulario al intentar enviarlo
    form.addEventListener('submit', (event) => {
        validateTextarea(); // Ejecuta la validación antes de enviar

        if (!form.checkValidity()) {
            event.preventDefault(); // Evita el envío si el formulario no es válido
            event.stopPropagation(); // Detiene la propagación del evento
        }

        form.classList.add('was-validated'); // Activa el estado de validación de Bootstrap
    });

    // Evitar el envío accidental al presionar Enter en el textarea
    textarea.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault(); // Previene el envío del formulario
        }
    });
});
</script>