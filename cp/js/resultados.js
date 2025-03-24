document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const resultsTableBody = document.getElementById('resultsList');
    const summaryChart = document.getElementById('summaryChart').getContext('2d');
    const totalSurveys = document.getElementById('totalSurveys');
    const completedSurveys = document.getElementById('completedSurveys');
    const searchInput = document.getElementById('searchResults');
    const searchClave = document.getElementById('searchClave');
    const claveResponses = document.getElementById('claveResponses');
    const loadingSpinner = document.getElementById('loadingSpinner');

    let selectedQuestion = null;

    // Función para cargar datos desde el backend
    async function loadResults(filters = {}) {
        try {
            // Mostrar spinner
            loadingSpinner.style.display = 'block';

            // Construir la URL con los filtros aplicados
            let url = 'includesCP/resultadosDB.php';
            if (Object.keys(filters).length > 0) {
                url += '?' + new URLSearchParams(filters).toString();
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();

            // Actualizar estadísticas generales
            totalSurveys.textContent = data.estadisticas.totalEncuestas || 0;
            completedSurveys.textContent = data.estadisticas.encuestasCompletadas || 0;

            // Limpiar tabla
            resultsTableBody.innerHTML = '';

            // Rellenar tabla con resultados
            Object.entries(data.respuestasPorPregunta || {}).forEach(([pregunta, respuestas]) => {
                const totalRespuestas = Object.values(respuestas).reduce((sum, count) => sum + count, 0);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input type="radio" class="form-check-input question-radio" name="question" data-question="${pregunta}">
                        </div>
                    </td>
                    <td>${pregunta}</td>
                    <td>${totalRespuestas}</td>
                `;
                resultsTableBody.appendChild(row);
            });

            // Crear gráfico si no existe
            if (!window.summaryChartInstance) {
                window.summaryChartInstance = new Chart(summaryChart, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Distribución de Respuestas',
                            data: [],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                });
            }

            // Actualizar gráfico con datos iniciales
            const firstPregunta = Object.keys(data.respuestasPorPregunta || {})[0];
            if (firstPregunta) {
                const respuestas = data.respuestasPorPregunta[firstPregunta];
                window.summaryChartInstance.data.labels = Object.keys(respuestas);
                window.summaryChartInstance.data.datasets[0].data = Object.values(respuestas);
                window.summaryChartInstance.update();
            } else {
                window.summaryChartInstance.data.labels = ['Sin datos'];
                window.summaryChartInstance.data.datasets[0].data = [0];
                window.summaryChartInstance.update();
            }
        } catch (error) {
            console.error('Error al cargar los resultados:', error);
            alert('Ocurrió un error al cargar los datos. Por favor, inténtalo de nuevo.');
        } finally {
            // Ocultar spinner
            loadingSpinner.style.display = 'none';
        }
    }

    // Búsqueda dinámica en la tabla
    searchInput.addEventListener('input', (event) => {
        const searchTerm = event.target.value.toLowerCase();
        const rows = resultsTableBody.querySelectorAll('tr');
        let found = false;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const match = text.includes(searchTerm);
            row.style.display = match ? '' : 'none';
            if (match) found = true;
        });

        if (!found && searchTerm.trim()) {
            alert('No se encontraron resultados para la búsqueda.');
        }
    });

    // Seleccionar pregunta para el gráfico
    resultsTableBody.addEventListener('change', (event) => {
        if (event.target.classList.contains('question-radio')) {
            selectedQuestion = event.target.dataset.question;
            updateChart();
        }
    });

    // Actualizar gráfico dinámico
    function updateChart() {
        fetch('includesCP/resultadosDB.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!selectedQuestion || !data.respuestasPorPregunta[selectedQuestion]) return;

                const respuestas = data.respuestasPorPregunta[selectedQuestion];
                window.summaryChartInstance.data.labels = Object.keys(respuestas);
                window.summaryChartInstance.data.datasets[0].data = Object.values(respuestas);
                window.summaryChartInstance.update();
            })
            .catch(error => {
                console.error('Error al actualizar el gráfico:', error);
                alert('Ocurrió un error al actualizar el gráfico. Por favor, inténtalo de nuevo.');
            });
    }

  // Buscar clave específica
// Buscar clave específica
searchClave.addEventListener('input', async () => {
    const clave = searchClave.value.trim();
    if (!clave) {
        claveResponses.innerHTML = '';
        return;
    }

    try {
        const response = await fetch(`includesCP/resultadosDB.php?clave=${encodeURIComponent(clave)}`);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        const data = await response.json();

        // Mostrar respuestas de la clave
        claveResponses.innerHTML = '';
        if (data.respuestasClave && data.respuestasClave.length === 0) {
            claveResponses.innerHTML = '<p class="text-muted small">No se encontraron respuestas para esta clave.</p>';
            return;
        }

        // Construir el contenido HTML dinámicamente
        data.respuestasClave.forEach((fila, index) => {
            const div = document.createElement('div');
            div.className = 'mb-3 p-3 border rounded bg-light'; // Estilo de tarjeta

            let contenidoHTML = `<h6 class="mb-2">Encuesta ${index + 1}</h6>`;
            contenidoHTML += '<ul class="list-unstyled small">';

            Object.entries(fila).forEach(([key, value]) => {
                contenidoHTML += `<li><strong>${key}:</strong> ${value}</li>`;
            });

            contenidoHTML += '</ul>';
            div.innerHTML = contenidoHTML;

            claveResponses.appendChild(div);
        });
    } catch (error) {
        console.error('Error al buscar clave:', error);
        claveResponses.innerHTML = '<p class="text-danger small">Ocurrió un error al buscar la clave. Por favor, inténtalo de nuevo.</p>';
    }
});

    // Cargar resultados iniciales
    loadResults();
});