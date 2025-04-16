document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const resultsTableBody = document.getElementById('resultsList');
    const summaryChartCtx = document.getElementById('summaryChart').getContext('2d');
    const totalSurveys = document.getElementById('totalSurveys');
    const completedSurveys = document.getElementById('completedSurveys');
    const searchInput = document.getElementById('searchResults');
    const searchClave = document.getElementById('searchClave');
    const claveResponses = document.getElementById('claveResponses');
    const loadingSpinner = document.getElementById('loadingSpinner');

    let selectedQuestion = null;
    let chartInstance = null;

    // Cargar resultados iniciales
    loadResults();

    // Función principal para cargar datos desde el backend
    async function loadResults(filters = {}) {
        try {
            showSpinner();
            const data = await fetchData(filters);

            updateStatistics(data.estadisticas);
            populateResultsTable(data.respuestasPorPregunta);
            initializeChartIfNotExists();
            updateChartWithInitialData(data.respuestasPorPregunta);
        } catch (error) {
            handleError(error, 'Ocurrió un error al cargar los datos.');
        } finally {
            hideSpinner();
        }
    }

    // Mostrar/ocultar spinner
    function showSpinner() {
        loadingSpinner.style.display = 'block';
    }

    function hideSpinner() {
        loadingSpinner.style.display = 'none';
    }

    // Obtener datos del backend
    async function fetchData(filters = {}) {
        const url = Object.keys(filters).length > 0
            ? `controllers/resultadosDB.php?${new URLSearchParams(filters).toString()}`
            : 'controllers/resultadosDB.php';

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return await response.json();
    }

    // Actualizar estadísticas generales
    function updateStatistics(estadisticas) {
        totalSurveys.textContent = estadisticas.totalEncuestas || 0;
        completedSurveys.textContent = estadisticas.encuestasCompletadas || 0;
    }

    // Rellenar la tabla con resultados ordenados por nombre de pregunta
    function populateResultsTable(respuestasPorPregunta) {
        resultsTableBody.innerHTML = '';
        const preguntasOrdenadas = sortQuestionsAlphabetically(respuestasPorPregunta);

        preguntasOrdenadas.forEach(([pregunta, respuestas]) => {
            const totalRespuestas = calculateTotalResponses(respuestas);
            resultsTableBody.appendChild(createTableRow(pregunta, totalRespuestas));
        });
    }

    // Ordenar preguntas alfabéticamente
    function sortQuestionsAlphabetically(respuestasPorPregunta) {
        return Object.entries(respuestasPorPregunta || {}).sort(([a], [b]) => a.localeCompare(b));
    }

    // Calcular el total de respuestas
    function calculateTotalResponses(respuestas) {
        return Object.values(respuestas).reduce((sum, count) => sum + count, 0);
    }

    // Crear una fila para la tabla
    function createTableRow(pregunta, totalRespuestas) {
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
        return row;
    }

    // Inicializar el gráfico si no existe
    function initializeChartIfNotExists() {
        if (!chartInstance) {
            chartInstance = new Chart(summaryChartCtx, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Distribución de Respuestas', data: [] }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    }

    // Actualizar el gráfico con datos iniciales
    function updateChartWithInitialData(respuestasPorPregunta) {
        const preguntasOrdenadas = sortQuestionsAlphabetically(respuestasPorPregunta);
        const firstPregunta = preguntasOrdenadas[0]?.[0];

        if (firstPregunta) {
            const respuestas = respuestasPorPregunta[firstPregunta];
            updateChartData(Object.keys(respuestas), Object.values(respuestas));
        } else {
            updateChartData(['Sin datos'], [0]);
        }
    }

    // Actualizar datos del gráfico
    function updateChartData(labels, data) {
        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = data.map(Math.floor); // Asegurar valores enteros
        chartInstance.update();
    }

    // Búsqueda dinámica en la tabla
    searchInput.addEventListener('input', (event) => {
        const searchTerm = event.target.value.toLowerCase();
        const rows = resultsTableBody.querySelectorAll('tr');
        const found = Array.from(rows).some(row => {
            const match = row.textContent.toLowerCase().includes(searchTerm);
            row.style.display = match ? '' : 'none';
            return match;
        });

        if (!found && searchTerm.trim()) {
            alert('No se encontraron resultados para la búsqueda.');
        }
    });

    // Seleccionar pregunta para el gráfico
    resultsTableBody.addEventListener('change', async (event) => {
        if (event.target.classList.contains('question-radio')) {
            selectedQuestion = event.target.dataset.question;
            await updateChart();
        }
    });

    // Actualizar gráfico dinámico
    async function updateChart() {
        try {
            const data = await fetchData();
            if (!selectedQuestion || !data.respuestasPorPregunta[selectedQuestion]) return;

            const respuestas = data.respuestasPorPregunta[selectedQuestion];
            updateChartData(Object.keys(respuestas), Object.values(respuestas));
        } catch (error) {
            handleError(error, 'Ocurrió un error al actualizar el gráfico.');
        }
    }

    // Buscar clave específica
    searchClave.addEventListener('input', async () => {
        const clave = searchClave.value.trim();
        if (!clave) {
            claveResponses.innerHTML = '';
            return;
        }

        try {
            const data = await fetchData({ clave });
            displayClaveResponses(data.respuestasClave);
        } catch (error) {
            handleError(error, 'Ocurrió un error al buscar la clave.');
        }
    });

    // Mostrar respuestas de la clave
    function displayClaveResponses(respuestasClave) {
        claveResponses.innerHTML = '';
        if (!respuestasClave || respuestasClave.length === 0) {
            claveResponses.innerHTML = '<p class="text-muted">No se encontraron respuestas para esta clave.</p>';
            return;
        }

        respuestasClave.forEach((fila, index) => {
            claveResponses.appendChild(createClaveResponseCard(index + 1, fila));
        });
    }

// Crear tarjeta para mostrar respuestas de la clave
function createClaveResponseCard(encuestaNumber, fila) {
    const div = document.createElement('div');
    div.className = 'mb-3 p-3 border rounded bg-light';

    // Extraer el Registro de Muestra (si existe)
    const registroMuestra = fila['Registro Muestra'] || 'No disponible';
    delete fila['Registro Muestra']; // Eliminarlo del objeto para no mostrarlo en la tabla

    // Crear el contenido HTML
    let contenidoHTML = `<h6 class="mb-3"><strong>Registro de Muestra:</strong> ${registroMuestra}</h6>`;
    contenidoHTML += '<table class="table table-bordered table-striped">';
    contenidoHTML += '<thead>';
    contenidoHTML += '<tr><th>Pregunta</th><th>Respuesta</th></tr>';
    contenidoHTML += '</thead>';
    contenidoHTML += '<tbody>';

    // Rellenar la tabla con preguntas y respuestas
    Object.entries(fila).forEach(([key, value]) => {
        contenidoHTML += `<tr><td><strong>${key}</strong></td><td>${value}</td></tr>`;
    });

    contenidoHTML += '</tbody></table>';
    div.innerHTML = contenidoHTML;

    return div;
}
    // Manejo centralizado de errores
    function handleError(error, message) {
        console.error(message, error);
        alert(message);
    }
});