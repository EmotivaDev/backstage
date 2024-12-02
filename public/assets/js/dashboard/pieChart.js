// Inicializa la gráfica de pie
const pieChartContainer = document.getElementById('pieChart');
const pieChart = echarts.init(pieChartContainer);

// Agregar ResizeObserver para la gráfica de Pie
const resizeObserverPie = new ResizeObserver(() => {
    pieChart.resize();
});
resizeObserverPie.observe(pieChartContainer);

// Función para convertir segundos a formato "días hh:mm"
function updatePieChart(data) {
    const labels = data.map(item => item.location_description);
    const values = data.map(item => item.total_time);

    // Divide la leyenda en dos partes
    const legendLeft = labels.slice(0, 10); // Primera columna (izquierda) - máximo 10 elementos
    const legendRight = labels.slice(10);   // Segunda columna (derecha) si hay más de 10

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: (params) => {
                const valueInHours = formatTime(params.data.value);
                const label = params.data.name;
                return `${label}: ${valueInHours} (${params.percent}%)`;
            }
        },
        legend: [
            {
                orient: 'vertical',
                left: 10,          // Columna de leyenda a la izquierda
                top: 'center',
                data: legendLeft,  // Asigna los primeros 10 elementos
                itemWidth: 10,     // Ancho de los íconos de la leyenda
                itemHeight: 10,    // Altura de los íconos de la leyenda
                textStyle: {
                    color: '#555',
                    fontSize: 10,
                },
            },
            {
                orient: 'vertical',
                right: 10,         // Columna de leyenda a la derecha
                top: 'center',
                data: legendRight, // Asigna el resto de los elementos
                itemWidth: 10,     // Ancho de los íconos de la leyenda
                itemHeight: 10,    // Altura de los íconos de la leyenda
                textStyle: {
                    color: '#555',
                    fontSize: 10,
                },
            }
        ],
        series: [
            {
                name: 'Localizaciones',
                type: 'pie',
                radius: '50%',
                center: ['50%', '50%'],  // Centra la gráfica horizontalmente
                data: labels.map((label, index) => ({ value: values[index], name: label })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)',
                    },
                },
                label: {
                    show: false,
                },
                labelLine: {
                    show: false,
                },
            },
        ],
    };

    pieChart.setOption(option);

    pieChart.on('click', async (params) => {
        const selectedZone = params.name;
        const year = document.getElementById('yearSelect').value;
        const month = document.getElementById('monthSelect').value;
    
        // Selecciona los elementos y verifica su existencia
        const loadingBarGas = document.getElementById('loadingGas');
        const loadingBarDistance = document.getElementById('loadingDistance');
        const barChartGas = document.getElementById('barChartGas');
        const barChartDistance = document.getElementById('barChartDistance');
    
        // Si los elementos existen, aplica las modificaciones de estilo
        if (loadingBarGas) loadingBarGas.style.display = 'block';
        if (loadingBarDistance) loadingBarDistance.style.display = 'block';
        if (barChartGas) barChartGas.style.display = 'none';
        if (barChartDistance) barChartDistance.style.display = 'none';
    
        // Llama al endpoint y actualiza las gráficas de barras
        const data = await fetchLocationBarData(year, month, selectedZone);
    
        // Oculta los indicadores de carga y muestra las gráficas si los elementos existen
        if (loadingBarGas) loadingBarGas.style.display = 'none';
        if (loadingBarDistance) loadingBarDistance.style.display = 'none';
        if (barChartGas) barChartGas.style.display = 'block';
        if (barChartDistance) barChartDistance.style.display = 'block';
    
        // Actualiza las gráficas con los datos recibidos
        updateFuelConsumptionChart(data);
        updateDistanceChart(data);
    });
    
    
}



