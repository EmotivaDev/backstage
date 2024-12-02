// Función para obtener todos los años disponibles
async function fetchAvailableYears() {
    try {
        const response = await fetch('http://localhost:3000/api/years');
        if (!response.ok) {
            throw new Error(`Error al obtener años: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error al obtener años:', error);
        return [];
    }
}

// Función para obtener datos de localizaciones
async function fetchLocationData(year, month) {
    try {
        const response = await fetch(`http://localhost:3000/api/location-pie-chart?year=${year}&month=${month}`);
        if (!response.ok) {
            throw new Error(`Error al obtener los datos de localizaciones: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de localizaciones para el año ${year} y mes ${month}:`, error);
        return [];
    }
}

// Función para obtener el consumo total de gasolina por día
async function fetchFuelConsumptionData(year, month) {
    try {
        const response = await fetch(`http://localhost:3000/api/fuel-consumption-daily?year=${year}&month=${month}`);
        if (!response.ok) {
            throw new Error(`Error al obtener datos de consumo de gasolina: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de consumo de gasolina para el año ${year} y mes ${month}:`, error);
        return [];
    }
}

// Función para obtener la distancia total recorrida por día
async function fetchDistanceData(year, month) {
    try {
        const response = await fetch(`http://localhost:3000/api/distance-daily?year=${year}&month=${month}`);
        if (!response.ok) {
            throw new Error(`Error al obtener datos de distancia: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de distancia para el año ${year} y mes ${month}:`, error);
        return [];
    }
}

async function fetchLocationBarData(year, month, zone) {
    try {
        const response = await fetch(`http://localhost:3000/api/location-bar-data?year=${year}&month=${month}&zone=${zone}`);
        if (!response.ok) {
            throw new Error(`Error al obtener datos de zona: ${response.statusText}`);
        }
        const data = await response.json();

        // Extraer solo el día de la fecha completa
        return data.map(item => {
            const parsedDate = new Date(item.day);
            const day = !isNaN(parsedDate) ? parsedDate.getDate() : null; // Extrae el día numérico
            
            if (day === null) {
                console.warn("Fecha inválida:", item.day); // Advertencia si la fecha es inválida
            }
            return {
                day: day !== null ? `${day}` : "Fecha no válida", // Solo muestra "Día X" o un mensaje de error
                total_fuel_consumption: item.total_fuel_consumption,
                total_distance: item.total_distance,
            };
        });
    } catch (error) {
        console.error(`Error al obtener datos de zona ${zone}:`, error);
        return [];
    }
}


// Función para obtener las estadísticas de datos
async function fetchDataStats(year, month, day) {
    try {
        const response = await fetch(`http://localhost:3000/api/data-stats?year=${year}&month=${month}&day=${day}`);
        if (!response.ok) {
            throw new Error(`Error al obtener datos de estadísticas: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de estadísticas para el año ${year}, mes ${month} y día ${day}:`, error);
        return [];
    }
}

// Función para obtener las estadísticas de velocidad y distancia
async function fetchSpeedDistanceStats(year, month, day) {
    try {
        const response = await fetch(`http://localhost:3000/api/speed-distance-stats?year=${year}&month=${month}&day=${day}`);
        if (!response.ok) {
            throw new Error(`Error al obtener datos de estadísticas: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de estadísticas para el año ${year}, mes ${month} y día ${day}:`, error);
        return {};
    }
}

// Función para obtener registros agrupados por rangos de RPM
async function fetchRpmRecords(year, month) {
    try {
        const response = await fetch(`http://localhost:3000/api/rpm-records?year=${year}&month=${month}`);
        if (!response.ok) {
            throw new Error(`Error al obtener registros de RPM: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener registros de RPM para el año ${year} y mes ${month}:`, error);
        return [];
    }
}

