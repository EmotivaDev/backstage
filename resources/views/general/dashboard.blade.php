@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'dashboard', 'folderPage' => '', 'titlePage' => trans('labels.navs.dashboard')])

@section('content')
    <script src="{{ asset('template/assets/js/plugins/datatables.js') }}"></script>
    <script src="{{ asset('template/assets/js/plugins/choices.min.js') }}"></script>
    <style>
        .boat-image {
            width: 400px;
        }

        .avatar-fleet {
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border-radius: 50rem;
            height: 48px;
            width: 63px;
            transition: all .2s ease-in-out;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="col-lg-12">
            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill p-1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 active " data-bs-toggle="tab" href="#navigation-tab" role="tab"
                            aria-selected="true">
                            <i class="material-symbols-rounded text-lg position-relative">directions_boat</i>
                            <span class="ms-1">Navigation</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 " data-bs-toggle="tab" href="#fleet-tab" role="tab"
                            aria-selected="false" id="fleet_position">
                            <i class="material-symbols-rounded text-lg position-relative">navigation</i>
                            <span class="ms-1">Fleet Position</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 disabled" data-bs-toggle="tab" href="#fuel-tab" role="tab"
                            aria-selected="false">
                            <i class="material-symbols-rounded text-lg position-relative">public</i>
                            <span class="ms-1">Eco-Friendly Fuel</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 disabled" data-bs-toggle="tab" href="#bi-tab" role="tab"
                            aria-selected="false">
                            <i class="material-symbols-rounded text-lg position-relative">dashboard</i>
                            <span class="ms-1">Power Bi</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="navigation-tab" role="tabpanel">
                <!-- Grafica de RPM por Dispositivo -->
                <div class="card mt-4">
                    {{-- <div class="card-body"> --}}
                    <div class="row">





                        <div class="row align-items-start"> <!-- Alinear en la parte superior -->
                            <!-- Parte Superior Izquierda: Gráfica de Pie para Localizaciones -->
                            <div class="col-lg-6 position-relative z-index-2 resizable">
                                <h5>Gráfica de Pie para Localizaciones</h5>
                                <div id="loadingPie" class="loading-icon" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </div>
                                <div id="pieChart" class="echarts-chart" style="display: none;"></div>
                            </div>
                            <!-- Parte Superior Derecha: Datos y Selects -->
                            <div class="col-lg-6">
                                <div class="row justify-content-end">
                                    <div class="col-lg-6 position-relative z-index-2">
                                        <div class="data-section">
                                            <h5>Datos <span id="selectedDayDisplay"></span></h5>
                                            <div class="data-cards">
                                                <div class="data-card">
                                                    <span>Consumo Total:</span> <span id="totalConsumption"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Promedio RPM de Motores:</span> <span id="avgRPM"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Galones por Kilómetro:</span> <span id="gpk"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Velocidad Promedio:</span> <span id="avgSpeed"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Total de Km Recorrido:</span> <span id="totalKm"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Tiempo Transcurrido (h):</span> <span id="elapsedTime"></span>
                                                </div>
                                                <div class="data-card">
                                                    <span>Galones por Hora:</span> <span id="gph"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selects para años, meses y rangos de RPM -->
                                    <div class="col-lg-6">
                                        <h5>Filtros</h5>
                                        <div class="mb-3">
                                            <select id="yearSelect" class="form-select">
                                                <!-- Opciones se llenarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <select id="monthSelect" class="form-select">
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Parte Inferior -->
                        <div class="row align-items-start">
                            <!-- Parte Inferior Izquierda: Gráficas de Barras -->
                            <div class="col-lg-6 position-relative z-index-2 resizable">
                                <h5>Consumo Total de Gasolina por Día</h5>
                                <div id="loadingGas" class="loading-icon" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </div>
                                <div id="barChartGas" class="echarts-barras" style="display: none;"></div>
                                <h5>Distancia Total Recorrida por Día</h5>
                                <div id="loadingDistance" class="loading-icon" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </div>
                                <div id="barChartDistance" class="echarts-barras" style="display: none;"></div>
                            </div>
                            <!-- Parte Inferior Derecha: Filtros y Tabla -->
                            <div class="col-lg-6 position-relative z-index-2 resizable">
                                <h5>Filtros de Velocidad y Metros Recorridos</h5>
                                <div class="row">
                                    <!-- Velocidad Mínima y Máxima -->
                                    <div class="col-lg-6">
                                        <label for="speedRange">Velocidad Km/h:</label>
                                        <div class="d-flex">
                                            <input type="number" id="minSpeed" placeholder="Mínimo"
                                                class="form-control" readonly>
                                            <input type="number" id="maxSpeed" placeholder="Máximo"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                    <!-- Metros Recorridos Mínimos y Máximos -->
                                    <div class="col-lg-6">
                                        <label for="metersRange">Metros Recorridos:</label>
                                        <div class="d-flex">
                                            <input type="number" id="minMeters" placeholder="Mínimo"
                                                class="form-control" readonly>
                                            <input type="number" id="maxMeters" placeholder="Máximo"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de Registros -->
                                <h5>Registros</h5>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Rango de RPM</th>
                                            <th>Tiempo Transcurrido</th>
                                            <th>Km Recorridos</th>
                                            <th>Consumo Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recordsTable">
                                        <!-- Filas de registros dinámicamente añadidas por JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>






                    </div>
                    {{-- </div> --}}
                </div>
            </div>
            <div class="tab-pane fade" id="fuel-tab" role="tabpanel">
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="fleet-tab" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="row pb-2">
                        <div class="col-sm-3">
                            <div class="card">
                                <div class="card-body py-2 position-relative">
                                    <div class="row">
                                        <div class="input-group input-group-static">
                                            <label>@lang('labels.texts.date')*</label>
                                            <input class="form-control datetimepicker" type="text"
                                                id="date_select_fleet_position" data-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 mt-sm-0 mt-4">
                            <div class="card">
                                <div class="card-body pt-2 pb-3 position-relative">
                                    <div class="row">
                                        <div class="col-12 text-start">
                                            <p class="text-sm mb-1 text-capitalize font-weight-bold">Fleet in
                                                Motion</p>
                                            <h5 class="font-weight-bolder mb-0" id="fleet_position_motion"></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 mt-sm-0 mt-4">
                            <div class="card">
                                <div class="card-body pt-2 pb-3 position-relative">
                                    <div class="row">
                                        <div class="col-12 text-start">
                                            <p class="text-sm mb-1 text-capitalize font-weight-bold">Fleet at Rest
                                            </p>
                                            <h5 class="font-weight-bolder mb-0" id="fleet_position_rest"></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 mt-sm-0 mt-4">
                            <div class="card">
                                <div class="card-body pt-2 pb-3 position-relative">
                                    <div class="row">
                                        <div class="col-12 text-start">
                                            <p class="text-sm mb-1 text-capitalize font-weight-bold">Avg Speed</p>
                                            <h5 class="font-weight-bolder mb-0" id="fleet_position_avg_speed">
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header pb-0">
                                <h6>Vessel Fleet Position</h6>
                            </div>
                            <div class="card-body px-0 pt-0 pb-2">
                                <div class="table-responsive p-0">
                                    <table class="table table-flush" id="table_device_fleet_position">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Vessel </th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Speed</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Course</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Altitude</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Location</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="bi-tab" role="tabpanel">
    </div>
    <script src="{{ asset('template/assets/js/plugins/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@latest/dist/chartjs-adapter-moment.umd.js"></script>
    <script src="{{ asset('assets/js/dashboard/api.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/pieChart.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/fuelConsumptionChart.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/distanceChart.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dashboard.js') }}"></script>
@endsection
