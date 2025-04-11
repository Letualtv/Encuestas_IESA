// Definir la función en el ámbito global
window.reordenarIds = function reordenarIds() {
    fetch("includesCP/reordenarPreguntas.php", { method: "POST" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.preguntas)) {
                let siguienteId = 1;

                data.preguntas.forEach(pregunta => {
                    if (['matrix1', 'matrix2'].includes(pregunta.tipo) && pregunta.opciones) {
                        let nuevaClave = siguienteId;
                        const opcionesActualizadas = {};

                        Object.keys(pregunta.opciones || {}).forEach(key => {
                            opcionesActualizadas[nuevaClave] = pregunta.opciones[key];
                            nuevaClave++;
                        });

                        pregunta.opciones = opcionesActualizadas;
                        siguienteId = nuevaClave; // Actualizar el siguiente ID basado en el último key de opciones
                    } else if (pregunta.tipo === 'matrix3' && pregunta.opciones) {
                        let ultimoKeySubLabel = 0;

                        Object.values(pregunta.opciones || {}).forEach(opcion => {
                            if (opcion.subLabel) {
                                const keys = Object.keys(opcion.subLabel || {}).map(key => parseInt(key, 10));
                                ultimoKeySubLabel = Math.max(ultimoKeySubLabel, ...keys);
                            }
                        });

                        siguienteId = ultimoKeySubLabel + 1; // Actualizar el siguiente ID basado en el último key del subLabel
                    } else {
                        pregunta.id = siguienteId; // Asignar el siguiente ID para preguntas normales
                        siguienteId++;
                    }
                });

                cargarPreguntas(); // Recargar la lista de preguntas
                showToast("Preguntas reordenadas correctamente.", "primary"); // Cambiar a color de éxito
                setTimeout(() => location.reload(), 500); // Recargar la página tras un breve retraso
            } else {
                showToast(data.message || "Error al reordenar las preguntas.", "success");
                setTimeout(() => location.reload(), 500); // Recargar la página tras un breve retraso
            }
        })
        .catch(error => {
            console.error("Error al reordenar las preguntas:", error);
            showToast("Ocurrió un error al intentar reordenar las preguntas.", "danger");
        });
};
