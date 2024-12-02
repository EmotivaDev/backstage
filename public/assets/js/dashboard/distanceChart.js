// Inicializa la gráfica de distancia recorrida
const barChartDistanceContainer = document.getElementById('barChartDistance');
const barChartDistance = echarts.init(barChartDistanceContainer);

// Agregar ResizeObserver para la gráfica de distancia recorrida
const resizeObserverDistance = new ResizeObserver(() => {
    barChartDistance.resize();
});
resizeObserverDistance.observe(barChartDistanceContainer);

// Función para actualizar la gráfica de distancia recorrida
function updateDistanceChart(data) {
    const labels = data.map(item => `Día ${item.day}`); // Etiquetas de días
    const values = data.map(item => parseFloat(item.total_distance).toFixed(1)); // Distancia total

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: (params) => {
                const day = params.name; // 'Día X'
                const value = params.value; // Distancia total
                return `${day}: ${value} km`; // Formato del tooltip
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
            name: 'Kilómetros',
            scale: true,
            splitNumber: 2,
        },
        series: [
            {
                name: 'Distancia Total Recorrida',
                type: 'bar',
                data: values,
                itemStyle: {
                    color: 'rgba(144, 238, 144, 0.6)', // Color pastel
                },
            },
        ],
    };

    // Aplica la opción a la gráfica
    barChartDistance.setOption(option);
}
