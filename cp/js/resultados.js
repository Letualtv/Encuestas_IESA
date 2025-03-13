document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const resultsTableBody = document.getElementById('resultsList');
    const summaryChart = document.getElementById('summaryChart').getContext('2d');
    const totalSurveys = document.getElementById('totalSurveys');
    const completedSurveys = document.getElementById('completedSurveys');
    const searchInput = document.getElementById('searchResults');
    const searchClave = document.getElementById('searchClave');
    const claveResponses = document.getElementById('claveResponses');

    let selectedQuestion = null;

    // Función para cargar datos desde el backend
    async function loadResults(filters = {}) {
        try {
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
            }
        } catch (error) {
            console.error('Error al cargar los resultados:', error);
        }
    }

    // Búsqueda dinámica en la tabla
    searchInput.addEventListener('input', (event) => {
        const searchTerm = event.target.value.toLowerCase();
        const rows = resultsTableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
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
                const labels = Object.keys(respuestas);
                const counts = Object.values(respuestas);

                window.summaryChartInstance.data.labels = labels;
                window.summaryChartInstance.data.datasets[0].data = counts;
                window.summaryChartInstance.update();
            })
            .catch(error => {
                console.error('Error al actualizar el gráfico:', error);
            });
    }

    

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
                claveResponses.textContent = 'No se encontraron respuestas para esta clave.';
                return;
            }

            data.respuestasClave.forEach((fila, index) => {
                const div = document.createElement('div');
                div.className = 'mb-2 small';
                div.innerHTML = `
                    <strong>Encuesta ${index + 1}:</strong><br>
                    ${Object.entries(fila)
                        .filter(([key]) => key.startsWith('r'))
                        .map(([key, value]) => `<span>${key}: ${value}</span><br>`)
                        .join('')}
                `;
                claveResponses.appendChild(div);
            });
        } catch (error) {
            console.error('Error al buscar clave:', error);
            claveResponses.textContent = 'Ocurrió un error al buscar la clave.';
        }
    });

    // Cargar resultados iniciales
    loadResults();
});