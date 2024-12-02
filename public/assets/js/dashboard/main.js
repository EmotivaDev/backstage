// select de año
document.addEventListener('DOMContentLoaded', async () => {
    await loadYears(); // Cargar años al iniciar
    await loadCharts(); // Cargar gráficas al iniciar
});

// Función para cargar los años en el select
async function loadYears() {
    const yearSelect = document.getElementById('yearSelect');
    const years = await fetchAvailableYears();

    // Limpiar el select actual
    yearSelect.innerHTML = '';

    // Agregar opciones al select
    years.forEach(year => {
        const option = document.createElement('option');
        option.value = year; // Asumiendo que el año es un número
        option.textContent = year; // Texto del año
        yearSelect.appendChild(option);
    });

    // Establecer el año por defecto si es necesario
    if (years.length > 0) {
        yearSelect.value = years[0]; // Seleccionar el primer año por defecto
    }
}

// Manejo de las gráficas
document.addEventListener('DOMContentLoaded', async () => {
    await loadCharts();
});

let selectedDay = null;

// Cargar todas las gráficas al inicio
async function loadCharts() {
    const year = document.getElementById('yearSelect').value;
    const month = document.getElementById('monthSelect').value;

    // Mostrar los íconos de carga
    document.getElementById('loadingPie').style.display = 'block';
    document.getElementById('loadingGas').style.display = 'block';
    document.getElementById('loadingDistance').style.display = 'block';
    
    document.getElementById('pieChart').style.display = 'none';
    document.getElementById('barChartGas').style.display = 'none';
    document.getElementById('barChartDistance').style.display = 'none';

    // Obtener datos para las gráficas
    const dataPromises = [
        fetchLocationData(year, month),
        fetchFuelConsumptionData(year, month),
        fetchDistanceData(year, month),
        fetchRpmRecords(year, month)
    ];
    
    const [locationData, fuelData, distanceData, rpmData] = await Promise.all(dataPromises);

    // Actualizar las gráficas
    updateCharts(locationData, fuelData, distanceData, rpmData);

    // Ocultar los íconos de carga
    document.getElementById('loadingPie').style.display = 'none';
    document.getElementById('loadingGas').style.display = 'none';
    document.getElementById('loadingDistance').style.display = 'none';
    
    // Mostrar las gráficas
    document.getElementById('pieChart').style.display = 'block';
    document.getElementById('barChartGas').style.display = 'block';
    document.getElementById('barChartDistance').style.display = 'block';

    // Obtener el primer día disponible a partir de los datos de fuelData
    if (!selectedDay) {
        selectedDay = getFirstAvailableDay(fuelData);
    }

    await updateStatistics(year, month);
}

// Actualiza las gráficas y la tabla de registros de RPM
function updateCharts(locationData, fuelData, distanceData, rpmData) {
    updatePieChart(locationData);
    updateFuelConsumptionChart(fuelData);
    updateDistanceChart(distanceData);
    updateRecordsTable(rpmData); // Llenar la tabla con datos de RPM
}

// Función para actualizar estadísticas y campos de velocidad y distancia
async function updateStatistics(year, month) {
    const stats = await fetchDataStats(year, month, selectedDay);
    updateStats(stats); // Actualizar datos si hay estadísticas disponibles

    const speedDistanceStats = await fetchSpeedDistanceStats(year, month, selectedDay);
    updateSpeedDistanceStats(speedDistanceStats); // Actualizar campos de velocidad y distancia

    // Agregar eventos de clic a las gráficas
    addChartEventListeners();
}

// Función para convertir segundos a formato "días hh:mm"
function formatTime(seconds) {
    const days = Math.floor(seconds / 86400);
    const hours = Math.floor((seconds % 86400) / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${days}d ${hours}h ${minutes}m`;
}

// Función para actualizar la tabla de registros
function updateRecordsTable(rpmData) {
    const recordsTableBody = document.getElementById('recordsTable');
    recordsTableBody.innerHTML = ''; // Limpiar la tabla antes de añadir nuevos registros

    rpmData.rpmRanges.forEach(range => {
        const row = createTableRow(range);
        recordsTableBody.appendChild(row);
    });

    // Añadir la fila de totales
    const totalRow = createTotalRow(rpmData.totals);
    recordsTableBody.appendChild(totalRow);
}

// Crea una fila para los totales
function createTotalRow(totals) {
    const row = document.createElement('tr'); // Crear una fila
    row.innerHTML = `
        <td>Total</td>
        <td>${formatTime(totals.tiempo_trans)}</td>
        <td>${totals.km_reco.toFixed(1)}</td>
        <td>${totals.totalFuel.toFixed(1)}</td>
    `;
    return row; // Retornar el nodo de la fila
}

// Crea una fila para los registros de RPM
function createTableRow(range) {
    const row = document.createElement('tr');
    
    row.innerHTML = `
        <td>${range.name}</td>
        <td>${formatTime(range.tiempo_trans)}</td>
        <td>${range.km_reco.toFixed(1)}</td>
        <td>${range.totalFuel.toFixed(1)}</td>
    `;
    return row; // Retornar el nodo de la fila
}

// Función para obtener el primer día disponible
function getFirstAvailableDay(data) {
    return data.length > 0 ? data[0].day : null; // Cambiar 'day' según tu estructura de datos
}

// Escuchar cambios en los selects
document.getElementById('yearSelect').addEventListener('change', resetCharts);
document.getElementById('monthSelect').addEventListener('change', resetCharts);

// Función para reiniciar las gráficas y los datos al cambiar el año o mes
async function resetCharts() {
    clearStats(); // Limpiar datos de la vista
    clearSpeedDistanceStats(); // Limpiar velocidad y distancia
    selectedDay = null; // Reiniciar día seleccionado
    
    // Limpiar la tabla de registros de RPM
    const recordsTableBody = document.getElementById('recordsTable');
    recordsTableBody.innerHTML = ''; // Limpiar la tabla antes de añadir nuevos registros

    await loadCharts(); // Recargar las gráficas y la tabla de registros
}

// Función para agregar eventos de clic a las gráficas de barras
function addChartEventListeners() {
    const chartClickHandler = async (params) => {
        if (params.componentType === 'series') {
            selectedDay = params.name.replace('Día ', ''); // Extraer el día del nombre
            const year = document.getElementById('yearSelect').value;
            const month = document.getElementById('monthSelect').value;

            // Limpiar datos antes de cargar nuevos
            clearStats(); // Limpiar estadísticas
            clearSpeedDistanceStats(); // Limpiar estadísticas de velocidad y distancia

            await updateStatistics(year, month); // Cargar nuevas estadísticas
        }
    };

    // Clic en las gráficas
    barChartGas.on('click', chartClickHandler);
    barChartDistance.on('click', chartClickHandler);
}

// Función para actualizar las estadísticas de velocidad y distancia
function updateSpeedDistanceStats(stats) {
    if (stats) {
        document.getElementById('minSpeed').value = stats.minSpeed !== null ? stats.minSpeed.toFixed(1) : '';
        document.getElementById('maxSpeed').value = stats.maxSpeed !== null ? stats.maxSpeed.toFixed(1) : '';
        document.getElementById('minMeters').value = stats.minDistance !== null ? stats.minDistance.toFixed(1) : '';
        document.getElementById('maxMeters').value = stats.maxDistance !== null ? stats.maxDistance.toFixed(1) : '';
    } else {
        clearSpeedDistanceStats(); // Limpiar si no hay datos
    }
}

// Función para limpiar las estadísticas de velocidad y distancia
function clearSpeedDistanceStats() {
    ['minSpeed', 'maxSpeed', 'minMeters', 'maxMeters'].forEach(id => {
        document.getElementById(id).value = '';
    });
}

// Función para actualizar las estadísticas
function updateStats(stats) {
    if (stats) {
        document.getElementById('selectedDayDisplay').innerText = selectedDay ? ` - Día ${selectedDay}` : '';
        document.getElementById('totalConsumption').innerText = stats.totalConsumption !== null ? stats.totalConsumption.toFixed(1) : '0.0';
        document.getElementById('avgRPM').innerText = stats.avgRPM !== null ? Number(stats.avgRPM).toFixed(1) : '0.0';
        document.getElementById('gpk').innerText = stats.FuelKM !== null ? stats.FuelKM.toFixed(2) : '0.00';
        document.getElementById('avgSpeed').innerText = stats.avgSpeed !== null ? stats.avgSpeed.toFixed(1) : '0.0';
        document.getElementById('totalKm').innerText = stats.totalKm !== null ? stats.totalKm.toFixed(1) : '0.0';
        document.getElementById('elapsedTime').innerText = stats.total_time !== null ? (stats.total_time / 3600).toFixed(1) : '0.0';
        document.getElementById('gph').innerText = stats.FuelH !== null ? stats.FuelH.toFixed(2) : '0.00';
    } else {
        clearStats(); // Limpiar si no hay datos
    }
}

// Función para limpiar las estadísticas
function clearStats() {
    ['selectedDayDisplay', 'totalConsumption', 'avgRPM', 'gpk', 'avgSpeed', 'totalKm', 'elapsedTime', 'gph'].forEach(id => {
        document.getElementById(id).innerText = '';
    });
}
