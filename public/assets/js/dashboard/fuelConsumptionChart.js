// Inicializa la gráfica de consumo de gasolina
const barChartGasContainer = document.getElementById('barChartGas');
const barChartGas = echarts.init(barChartGasContainer);

// Agregar ResizeObserver para la gráfica de consumo de gasolina
const resizeObserverGas = new ResizeObserver(() => {
    barChartGas.resize();
});
resizeObserverGas.observe(barChartGasContainer);

// Función para actualizar la gráfica de consumo de gasolina
function updateFuelConsumptionChart(data) {
    const labels = data.map(item => `Día ${item.day}`); // Etiquetas de días
    const values = data.map(item => parseFloat(item.total_fuel_consumption).toFixed(1)); // Consumo total

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: (params) => {
                const label = params.name; // 'Día X'
                const value = params.value; // Consumo total
                return `${label}: ${value} GL`; // Formato del tooltip
            }
        },
        xAxis: {
            type: 'category',
            data: labels,
            axisLabel: {
                rotate: 45, // Rotar etiquetas si es necesario
            },
        },
        yAxis: {
            type: 'value',
            name: 'Galones',
            scale: true,
            splitNumber: 2,
        },
        series: [
            {
                name: 'Consumo Total de Gasolina',
                type: 'bar',
                data: values,
                itemStyle: {
                    color: 'rgba(173, 216, 230, 0.6)', // Color pastel
                },
            },
        ],
    };

    // Aplica la opción a la gráfica
    barChartGas.setOption(option);
}