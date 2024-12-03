@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'realtime', 'folderPage' => '', 'titlePage' => trans('labels.texts.navs.realtime')])

@section('content')
    </script>
    <link id="pagestyle" href="{{ asset('assets/css/realtime.css') }}?v={{ time() }}" rel="stylesheet" />

    <div class="div-devices border-radius-lg ">
        <div class="device-tracker-menu">
            <div class="accordion" id="deviceTrackerAccordion">
                <!-- Most Tracked Devices Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        {{-- <button class="device-accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <h6 class="text-sm mb-0 title-device-selected" id="title_device_selected"></h6>
                            <i class="material-symbols-rounded arrow-icon">arrow_downward</i>
                        </button> --}}
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#deviceTrackerAccordion">
                        <div class="accordion-body">
                            <!-- Search bar -->
                            {{-- <input type="text" class="search-bar" id="searchBar" oninput="filterCards()"  
                                placeholder="Search devices..."> --}}
                            <div class="scrollable-list" id="deviceList">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="div-navigation border-radius-lg mb-2">
        <div class="div-button-arrow-bottom">
            <button class="device-accordion-button-two btn btn-icon btn-3 btn-info mb-1 " type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                <i class="material-symbols-rounded arrow-icon-two">arrow_downward</i>
            </button>
        </div>
        <div class="panel-navigation-menu py-0">
            <div class="accordion" id="panelNavigationAccordion">
                <!-- Most Tracked Devices Section -->
                <div class="accordion-item">
                    <div class="accordion-header-navigation " id="headingTwo">
                        <nav>
                            <div class="nav nav-tabs justify-content-center py-1" id="nav-tab-principal" role="tablist">
                                <button class="nav-link  active title-nav ms-2" id="nav-navigation-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-navigation" type="button" role="tab"
                                    aria-controls="nav-navigation" aria-selected="true"><i
                                        class="material-symbols-rounded arrow-icon nav-icons">navigation</i>
                                    <br>{{ trans('labels.texts.navigation') }}</button>
                                <button class="nav-link title-nav" id="nav-telemetry-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-telemetry" type="button" role="tab"
                                    aria-controls="nav-telemetry" aria-selected="false"><i
                                        class="material-symbols-rounded arrow-icon nav-icons">engineering</i><br>{{ trans('labels.texts.telemetry') }}</button>
                                <button class="nav-link title-nav disabled" id="nav-ai-assistant-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-ai-assistant" type="button" role="tab"
                                    aria-controls="nav-ai-assistant" aria-selected="false"><i
                                        class="material-symbols-rounded arrow-icon nav-icons">forum</i><br>{{ trans('labels.texts.quick_message') }}</button>
                                <button class="nav-link title-nav disabled" id="nav-track-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-track" type="button" role="tab" aria-controls="nav-track"
                                    aria-selected="false"><i
                                        class="material-symbols-rounded arrow-icon nav-icons">query_stats</i><br>{{ trans('labels.texts.track_historic') }}</button>
                                <button class="nav-link title-nav" id="nav-voyage-plan-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-voyage-plan" type="button" role="tab"
                                    aria-controls="nav-voyage-plan" aria-selected="false"><i
                                        class="material-symbols-rounded arrow-icon nav-icons">location_on</i><br>{{ trans('labels.texts.voyage_plan') }}</button>
                            </div>
                        </nav>
                    </div>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                        data-bs-parent="#panelNavigationAccordion">
                        <div class="accordion-body">
                            <div class="tab-content pt-0" style="display: block;">
                                <div class="row">
                                    <div class="col-4 col-name-device">
                                        <h6 class="text-sm mb-1 text-capitalize font-weight-bold" id="name_device_nav">-
                                        </h6>
                                    </div>
                                    <div class="col-8 pt-0 col-engines">
                                        <nav id="nav_engines">
                                            <div class="nav nav-tabs" id="nav-tab-secondary" role="tablist">
                                                <button class="nav-link active title-nav-two px-1" id="nav-engine1-tab"
                                                    data-bs-toggle="tab" type="button" role="tab"
                                                    aria-selected="true">{{ ucwords(substr(trans('labels.texts.engine1'), 0, 10)) }}</button>
                                                <button class="nav-link title-nav-two px-1" id="nav-engine2-tab"
                                                    data-bs-toggle="tab" type="button" role="tab"
                                                    aria-selected="false">{{ ucwords(substr(trans('labels.texts.engine2'), 0, 10)) }}</button>
                                                <button class="nav-link title-nav-two px-1" id="nav-engine3-tab"
                                                    data-bs-toggle="tab" type="button" role="tab"
                                                    aria-selected="false">{{ ucwords(substr(trans('labels.texts.engine3'), 0, 10)) }}</button>
                                                <button class="nav-link title-nav-two px-1" id="nav-engine4-tab"
                                                    data-bs-toggle="tab" type="button" role="tab"
                                                    aria-selected="false">{{ ucwords(substr(trans('labels.texts.engine4'), 0, 10)) }}</button>
                                                <button class="nav-link title-nav-two px-1" id="nav-engine5-tab"
                                                    data-bs-toggle="tab" type="button" role="tab"
                                                    aria-selected="false">{{ ucwords(substr(trans('labels.texts.engine5'), 0, 10)) }}</button>
                                            </div>
                                        </nav>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active pb-2 px-2" id="nav-navigation" role="tabpanel"
                                    aria-labelledby="nav-navigation-tab" tabindex="0">
                                    <div class="row" style="margin-top: 1px">
                                        <div class="col-6 col-lg-5 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/date.png') }}"
                                                            class="img-fluid-custom ms-1">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.date'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="date_device_navigation">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/odometer.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.odometer'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="odometer_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/fuel.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.fuel_today'), 0, 12)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="fuel_device_navigation">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/Fuel Tank.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.fuel_tank'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="fuel_tank_device_navigation">
                                                        -</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/ETA-TTG.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.eta'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="eta_device_navigation">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/battery.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.power'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="power_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/Depth.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.depth'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="depth_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/20.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.speed'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="speed_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-4 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/course2.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.course'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="course_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-8 col-md-4 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/event.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.event'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="event_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/location.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.location'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="location_device_navigation">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade pb-2 px-2" id="nav-telemetry" role="tabpanel"
                                    aria-labelledby="nav-telemetry-tab" tabindex="0">
                                    <div class="row" style="margin-bottom: 1px">
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/rpm.png') }}"
                                                            class="img-fluid-custom ms-1">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.rpm'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="rpm_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/ECT.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.coolant'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="coolant_device_telemetry">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/oil.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.oil'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="oil_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/Fuel Tank.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.total_fuel'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="total_fuel_device_telemetry">
                                                        -</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/horometro.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.hours'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="hours_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/battery.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.batt'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="batt_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/odometer.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.odometer'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="odometer_device_telemetry">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/fuel rate.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.fuel_rate'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="fuel_rate_device_telemetry">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/20.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.speed'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="speed_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/course2.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.course'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="course_device_telemetry">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-4 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/event.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.event'), 0, 10)) }}</h6>
                                                    <p class="m-0 me-1 description-card" id="event_device_telemetry">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-bordered p-0">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="img-container">
                                                        <img src="{{ asset('assets/images/icons/location.png') }}"
                                                            class="img-fluid-custom">
                                                    </div>
                                                </div>
                                                <div class="col-9 text-end">
                                                    <h6 class="m-0 me-1 title-card">
                                                        {{ ucwords(substr(trans('labels.texts.location'), 0, 10)) }}
                                                    </h6>
                                                    <p class="m-0 me-1 description-card" id="location_device_telemetry">-
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade pb-2 px-2" id="nav-ai-assistant" role="tabpanel"
                                    aria-labelledby="nav-ai-assistant-tab" tabindex="0">
                                    <div class="row pt-0 pb-2" style="margin-top: 0px; min-height: 170px;">
                                    </div>
                                </div>
                                <div class="tab-pane fade pb-2 px-2" id="nav-track" role="tabpanel"
                                    aria-labelledby="nav-track-tab" tabindex="0">
                                </div>
                                <div class="tab-pane fade pb-2 px-2" id="nav-voyage-plan" role="tabpanel"
                                    aria-labelledby="nav-quick-voyage-plan" tabindex="0">
                                    <div class="row justify-content-center">
                                        <div class="col-6 col-lg-6 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card ">
                                                            {{ trans('labels.texts.number') }}</h6>
                                                        <p class="description-card mb-0" id="view_number_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.origin') }}</h6>
                                                        <p class="description-card mb-0" id="view_origin_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ substr(trans('labels.texts.destination'), 0, 11) }} </h6>
                                                        <p class="description-card mb-0" id="view_destination_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.draft') }}</h6>
                                                        <p class="description-card mb-0" id="view_draft_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.bargues') }}</h6>
                                                        <p class="description-card mb-0" id="view_bargues_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.load_type') }}</h6>
                                                        <p class="description-card mb-0" id="view_load_type_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-3 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.tonnes') }}</h6>
                                                        <p class="description-card mb-0" id="view_tonnes_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-12 p-0">
                                            <div class="card">
                                                <div class="card-body p-0 position-relative">
                                                    <div class="row text-center">
                                                        <h6 class="m-0 me-1 title-card">
                                                            {{ trans('labels.texts.description') }}</h6>
                                                        <p class="description-card mb-0" id="view_description_trip">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pb-2 pt-2 button-panel-voyage-plan">
                                        <div class="text-center">
                                            <button type="button" class="btn btn-sm bg-gradient-dark ms-auto mb-0"
                                                name="submit" id="button_create_trip" data-bs-toggle="modal"
                                                data-bs-target="#modal-form-create-trip">{{ trans('labels.texts.create') }}</button>
                                            <button type="button" class="btn btn-sm bg-gradient-dark ms-auto mb-0"
                                                onclick="editTrip()" name="submit" id="button_update_trip"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-form-update-trip">{{ trans('labels.texts.update') }}</button>
                                            <button type="button" class="btn btn-sm bg-gradient-dark ms-auto mb-0"
                                                name="submit" id="button_finish_trip" data-bs-toggle="modal"
                                                data-bs-target="#modal-form-finish-trip">{{ trans('labels.texts.finish') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="map-container">
        <div id="map"></div>
    </div>

    <div class="row div-buttons">
        <button class="btn btn-info w-80 mb-2 custom-collapse-toggle" type="button"
            data-bs-target="#collapseDevicesList" data-bs-toggle="tooltip" data-bs-trigger="hover"
            data-bs-placement="left" title="Devices">
            <span class="btn-inner--icon"><i class="material-symbols-rounded">directions_boat</i></span>
        </button>
        <button class="btn btn-info w-80 mb-2 custom-collapse-toggle" type="button" data-bs-target="#collapseLayers"
            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Layers">
            <span class="btn-inner--icon"><i class="material-symbols-rounded">layers</i></span>
        </button>
        <button class="btn btn-info w-80 mb-2 custom-collapse-toggle" type="button" data-bs-target="#collapseTracks"
            data-bs-toggle="tooltip" data-bs-trigger="hover"" data-bs-placement="left" title="Tracks">
            <span class="btn-inner--icon"><i class="material-symbols-rounded">stacked_line_chart</i></span>
        </button>
        <button class="btn btn-info w-80 mb-2 custom-collapse-toggle" type="button" data-bs-target="#collapseGeofences"
            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Geofences">
            <span class="btn-inner--icon"><i class="material-symbols-rounded">crop</i></span>
        </button>
    </div>

    <div class="collapse collapse-buttons collapse-horizontal" id="collapseDevicesList">
        <div class="card card-body" style="width: 300px;">
            <div class="container">
                <div class="col-md-6 d-flex align-items-center pb-2">
                    <i class="material-symbols-rounded me-2">directions_boat</i>
                    <small class="font-weight-bold text-dark">Devices</small>
                </div>
                <div class="row">
                    <div class="input-group input-group-outline flex-grow-1">
                        <label class="form-label label-search-bar">Search Device</label>
                        <input type="text" class="search-bar form-control form-control-sm m-0 " id="searchBarLayer"
                            oninput="filterCardsLayer()">
                    </div>
                    <div class="scrollable-list-layer" id="deviceListLayer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse collapse-buttons collapse-horizontal" id="collapseLayers">
        <div class="card card-body" style="width: 300px;">
            <div class="container">
                <div class="col-md-6 d-flex align-items-center pb-2">
                    <i class="material-symbols-rounded me-2">layers</i>
                    <small class="font-weight-bold text-dark">{{ trans('labels.texts.layers') }}</small>
                </div>
                <div class="row ">
                    <div class="col-md-6">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox" id="checkbox_ais" checked>
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.ais'), 0, 10)) }}
                            </label>
                        </div>
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox" id="checkbox_enc">
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.enc'), 0, 10)) }}
                            </label>
                        </div>
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox"
                                id="checkbox_cellular_coverage">
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.cellular_coverage'), 0, 9)) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox" id="checkbox_ideam"
                                checked>
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.ideam'), 0, 10)) }}
                            </label>
                        </div>
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox" id="checkbox_mile" checked>
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.mile'), 0, 10)) }}
                            </label>
                        </div>

                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto me-2 mt-2" type="checkbox"
                                id="checkbox_points_attention" checked>
                            <label class="form-check-label">
                                {{ ucwords(substr(trans('labels.texts.points_attention'), 0, 8)) }}
                            </label>
                        </div>
                    </div>
                    {{-- <form class="align-items-center">
                        <div class="input-group input-group-outline d-flex">
                            <label class="form-label">{{ ucwords(substr(trans('labels.texts.fuel'), 0, 10)) }}</label>
                            <input type="text" class="form-control">
                            <button class="btn btn-icon btn-2 bg-gradient-info mb-0">
                                <i class="material-symbols-rounded">send</i>
                            </button>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="collapse collapse-buttons collapse-horizontal" id="collapseTracks">
        <div class="card card-body" style="width: 300px;">
            <div class="container">
                <div class="col-md-6 d-flex  align-items-center pb-2">
                    <i class="material-symbols-rounded me-2">stacked_line_chart</i>
                    <small class="font-weight-bold text-dark">Tracks</small>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row text-center">
                        <div class="pb-2">
                            <input type="file" name="gpx_file" id="gpx_file">
                        </div>
                        <div class="pb-2">
                            <button class="btn btn-sm bg-gradient-dark ms-auto mb-0" type="button"
                                onclick="uploadFile()">Upload</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-flush" id="tracks-list1">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('labels.texts.name')</th>
                                <th>@lang('labels.texts.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('labels.texts.name')</th>
                                <th>@lang('labels.texts.actions')</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse collapse-buttons collapse-horizontal" id="collapseGeofences">
        <div class="card card-body" style="width: 300px;">
            <div class="container">
                <form method="POST" id="formGeofence">
                    @csrf
                    <div class="col-md-6 d-flex  align-items-center pb-2">
                        <i class="material-symbols-rounded me-2">crop</i>
                        <small class="font-weight-bold text-dark">Geofences</small>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group-static">
                                <label style="margin-bottom: 2px">{{ trans('labels.texts.name') }}*</label>
                                <input type="text" class="form-control py-0" name="name" id="name_geofence">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-static">
                                <label style="margin-bottom: 2px">{{ trans('labels.texts.description') }}</label>
                                <input type="text" class="form-control py-0" name="description"
                                    id="name_description">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="mb-0 ms-0 pt-1">Type Geofence*</label>
                            <select class="selectpicker" name="geofence" id="select-geofence" data-size="3"
                                data-width="100%" placeholder="Choose" data-live-search="true">
                                <option value="POLYGON" selected>Polygon</option>
                                <option value="POLYLINE">PoLyline</option>
                            </select>
                            @error('geofence')
                                <div class="text-danger ms-1"><small> {{ $message }}</small></div>
                            @enderror
                        </div>
                    </div>
                    <p class="text-xs mb-1 mt-2">@lang('labels.texts.fields_required')</p>
                    <div class="button-row d-flex ">
                        <a class="btn btn-sm bg-gradient-light mb-0 js-btn-prev" title="Cancel"
                            id="cancelButton">Cancel</a>
                        <button class="btn btn-sm bg-gradient-dark ms-auto mb-0 js-btn-next" type="button"
                            title="Add" id="activatePolygonButton">Add</button>
                        <button class="btn btn-info btn-sm ms-auto mb-0 js-btn-next" type="button" title="save"
                            onclick="saveButton()">Save</button>
                    </div>
                </form>
                <div class="table-responsive pt-3">
                    <table class="table table-flush" id="tracks-list2">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('labels.texts.name')</th>
                                <th>@lang('labels.texts.description')</th>
                                <th>@lang('labels.texts.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('labels.texts.name')</th>
                                <th>@lang('labels.texts.description')</th>
                                <th>@lang('labels.texts.actions')</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="container-bar" class="">
        <img class="pb-1" src="{{ asset('assets/images/speed.png') }}" class="img-fluid" width="32"
            height="40" data-bs-toggle="tooltip" title="{{ trans('labels.texts.speed_moment') }}">
        <div class="color-bar">
            <div class="segment segment-0" data-bs-toggle="tooltip" title="20 km/h">20</div>
            <div class="segment segment-1" data-bs-toggle="tooltip" title="17.5 km/h">17.5</div>
            <div class="segment segment-2" data-bs-toggle="tooltip" title="15 km/h">15</div>
            <div class="segment segment-3" data-bs-toggle="tooltip" title="12.5 km/h">12.5</div>
            <div class="segment segment-4" data-bs-toggle="tooltip" title="10 km/h">10</div>
            <div class="segment segment-5" data-bs-toggle="tooltip" title="7.5 km/h">7.5</div>
            <div class="segment segment-6" data-bs-toggle="tooltip" title="5 km/h">5</div>
            <div class="segment segment-7" data-bs-toggle="tooltip" title="2.5 km/h">2.5</div>
            <div class="segment segment-8" data-bs-toggle="tooltip" title="0 km/h">0</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="modal fade" id="modal-form-create-trip" tabindex="1000" role="dialog"
            aria-labelledby="modal-form-create-trip" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-left">
                                <h5 class="">New Trip</h5>
                            </div>
                            <div class="card-body">
                                <form id="createTripForm" class="pb-2" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.number')*</label>
                                                <input id="number_trip" name="number" type="text"
                                                    class="form-control pt-0">
                                                @error('number')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.origin')</label>
                                                <select class="selectpicker" name="origin" id="choices-origin-trip"
                                                    data-size="3" data-width="100%" placeholder="Choose"
                                                    data-live-search="true">
                                                    @foreach ($reverseGeocodes as $key => $value)
                                                        <option value="{{ $value->location }} - {{ $value->milemark }}">
                                                            {{ strtoupper($value->location) }} -
                                                            {{ strtoupper($value->milemark) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('origin')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div
                                                class="input-group input-group-static @error('destination') is-invalid is-filled mb-4 @enderror">
                                                <label class="pt-1">@lang('labels.texts.destination')</label>
                                                <select class="selectpicker" name="destination"
                                                    id="choices-destination-trip" placeholder="Choose" data-width="100%"
                                                    data-size="3" data-live-search="true">
                                                    @foreach ($reverseGeocodes as $key => $value)
                                                        <option value="{{ $value->location }} - {{ $value->milemark }}">
                                                            {{ strtoupper($value->location) }} -
                                                            {{ strtoupper($value->milemark) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('destination')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.draft')</label>
                                                <input id="draft_trip" name="draft" type="text"
                                                    class="form-control pt-0">
                                                @error('draft')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.bargues')</label>
                                                <input id="bargues_trip" name="bargues" type="text"
                                                    class="form-control pt-0">
                                                @error('bargues')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.load_type')</label>
                                                <select class="selectpicker" name="load_type" id="choices-load_type-trip"
                                                    data-size="3" placeholder="Choose" data-live-search="true"
                                                    data-width="100%">
                                                    @foreach ($loadTypes as $key => $value)
                                                        <option value="{{ $value }}">
                                                            {{ strtoupper($value) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('load_type')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.tonnes')</label>
                                                <input id="tonnes_trip" name="tonnes" type="text"
                                                    class="form-control pt-0">
                                                @error('tonnes')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-12">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.description')</label>
                                                <input id="description_trip" name="description" type="text"
                                                    class="form-control pt-0">
                                                @error('description')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs mb-1 mt-2">@lang('labels.texts.fields_required')</p>
                                    <button type="submit" onclick="createTrip(event)"
                                        class="btn bg-gradient-dark btn-sm float-end mt-1 mb-0">@lang('labels.buttons.save')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="modal fade" id="modal-form-update-trip" tabindex="1000" role="dialog"
            aria-labelledby="modal-form-update-trip" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-left">
                                <h5 class="">Update Trip</h5>
                            </div>
                            <div class="card-body">
                                <form id="updateTripForm" class="pb-2" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.number')*</label>
                                                <input id="update_number_trip" name="number" type="text"
                                                    class="form-control pt-0">
                                                @error('number')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.origin')</label>
                                                <select class="selectpicker" name="origin"
                                                    id="choices-update_origin-trip" data-size="3" data-width="100%"
                                                    placeholder="Choose" data-live-search="true">
                                                    @foreach ($reverseGeocodes as $key => $value)
                                                        <option
                                                            value="{{ $value->location }} - {{ $value->milemark }}">
                                                            {{ strtoupper($value->location) }} -
                                                            {{ strtoupper($value->milemark) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('origin')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div
                                                class="input-group input-group-static @error('destination') is-invalid is-filled mb-4 @enderror">
                                                <label class="pt-1">@lang('labels.texts.destination')</label>
                                                <select class="selectpicker" name="destination"
                                                    id="choices-update_destination-trip" data-size="3" data-width="100%"
                                                    placeholder="Choose" data-live-search="true">
                                                    @foreach ($reverseGeocodes as $key => $value)
                                                        <option
                                                            value="{{ $value->location }} - {{ $value->milemark }}">
                                                            {{ strtoupper($value->location) }} -
                                                            {{ strtoupper($value->milemark) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('destination')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.draft')</label>
                                                <input id="update_draft_trip" name="draft" type="text"
                                                    class="form-control pt-0">
                                                @error('draft')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.bargues')</label>
                                                <input id="update_bargues_trip" name="bargues" type="text"
                                                    class="form-control pt-0">
                                                @error('bargues')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.load_type')</label>
                                                <select class="selectpicker" name="load_type"
                                                    id="choices-update_load_type-trip" data-size="3" data-width="100%"
                                                    placeholder="Choose" data-live-search="true">
                                                    @foreach ($loadTypes as $key => $value)
                                                        <option value="{{ $value }}">
                                                            {{ strtoupper($value) }}</option>
                                                    @endforeach
                                                </select>
                                                @error('load_type')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.tonnes')</label>
                                                <input id="update_tonnes_trip" name="tonnes" type="text"
                                                    class="form-control pt-0">
                                                @error('tonnes')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-12">
                                            <div class="input-group input-group-static is-filled">
                                                <label class="pt-1">@lang('labels.texts.description')</label>
                                                <input id="update_description_trip" name="description" type="text"
                                                    class="form-control pt-0">
                                                @error('description')
                                                    <small>{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-xs mb-1 mt-2">@lang('labels.texts.fields_required')</p>
                                    <button type="submit" onclick="updateTrip(event)"
                                        class="btn bg-gradient-dark btn-sm float-end mt-1 mb-0">@lang('labels.buttons.save')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="modal fade" id="modal-form-finish-trip" tabindex="1000" role="dialog"
            aria-labelledby="modal-form-finish-trip" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-left">
                                <h5 class="">Do you want to complete the trip?</h5>
                            </div>
                            <div class="card-body">
                                <button type="submit" onclick="finishTrip()"
                                    class="btn bg-gradient-dark btn-sm float-end mt-1 mb-0">@lang('labels.buttons.save')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var urlWebSockets = "{{ env('URL_TRACCAR') }}";
        var timeZone = "{{ auth()->user()->time_zone }}";
        var listReverseGeocodes = {!! json_encode($reverseGeocodes) !!};
        var listTrips = {!! json_encode($trips) !!};
        var PositionsLog = {!! json_encode($PositionsLog) !!};
        var geofence_url = "{{ route('geofence') }}";
        var geofence_url_select = "{{ route('geofence.select') }}";
    </script>
    <script src="{{ asset('template/assets/js/plugins/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/realtime/general.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('template/assets/js/plugins/markerclusterer.js') }}"></script>
    <script src="{{ asset('assets/js/realtime/realtime.js') }}?v={{ time() }}"></script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('KEY_MAP') }}&libraries=drawing&callback=initMap">
    </script>

    <script>
        // var table = new simpleDatatables.DataTable("#tracks-list1");
        var checkboxes;
        var dataTableSearchtrack1;
        var dataTableSearchtrack2;
        var trails;

        function uploadFile(params) {

            var formData = new FormData();
            var fileField = $('#gpx_file')[0].files[0];

            // Verificar si se seleccionó un archivo
            if (!fileField) {
                alert('Por favor selecciona un archivo GPX.');
                return;
            }

            formData.append('file', fileField);

            // Realizar la solicitud AJAX
            $.ajax({
                url: '{{ route('trails.upload') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (dataTableSearchtrack1) {
                        dataTableSearchtrack1.destroy();
                        dataTableSearchtrack1 = null;
                    }
                    createTable();


                },
                error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                }
            });
        }

        document.querySelector('#tracks-list1').addEventListener('click', function(event) {
            if (event.target.classList.contains("btn-delete")) {
                var tr = event.target.closest('tr');
                var cells = tr.querySelectorAll('td');
                let id = cells[0].querySelector('input').id.replace(/^TrackCheck/, '');
                $.ajax({
                    url: `/trails/${parseInt(id)}`,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response == true) {
                            if (dataTableSearchtrack1) {
                                dataTableSearchtrack1.destroy();
                                dataTableSearchtrack1 = null;
                            }
                            createTable();
                            tripToast("Tracker Deleted", "success", "check");
                        } else {
                            console.log(response)
                            tripToast(response.message, "danger", "campaign");
                        }
                    }
                });
            }
            if (event.target.classList.contains('my-checkbox')) {
                let checkbox = document.getElementById(event.target.id);

                let id = checkbox.id.replace(/^TrackCheck/, '');
                let trail = trails.find(trail => trail.id === parseInt(id));

                if (checkbox.checked) {
                    cargarKML(trail);
                } else {
                    quitarKML();
                }
            }
            // if (event.target.classList.contains('btn-delete')) {
            //     const row = event.target.closest('tr'); // Encuentra la fila correspondiente
            //     if (row) {
            //         // Elimina la fila de la tabla
            //         dataTableSearchtrack1.rows().remove(row);
            //     }
            // }
        });



        document.querySelector('#tracks-list2').addEventListener('click', function(event) {

            if (event.target.classList.contains("btn-delete")) {

                // var tr = event.target.closest('tr');
                // var cells = tr.querySelectorAll('td');
                // console.log(cells)
                // console.log( cells[2].querySelector('label'))
                // let id = cells[2].querySelector('label').id.replace(/^GeofenceItem/, '');
                const itemId = event.target.id;


                const extractedId = itemId.replace('GeofenceItem', '');

                $.ajax({
                    url: `/geofences/${parseInt(extractedId)}`,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response == true) {
                            if (dataTableSearchtrack2) {
                                dataTableSearchtrack2.destroy();
                                dataTableSearchtrack2 = null;
                            }
                            clearGeofences()
                            createTableGeofence();
                            tripToast("Geofence Deleted", "success", "check");
                        } else {
                            console.log(response)
                            tripToast(response.message, "danger", "campaign");
                        }
                    }
                });
            }

        });

        function createTable() {
            $.ajax({
                url: '{{ route('trails.select') }}',
                type: 'GET',
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {

                    trails = response;

                    const newDataArray = response.map(item => ({
                        "Name": `
                                        <div class="d-flex">
                        <div class="form-check my-auto">
                            <input class="form-check-input my-checkbox" type="checkbox" id="TrackCheck${item.id}">
                        </div>
                        <label class="ms-3 my-auto name-label" data-bs-toggle="tooltip" title="${item.name.toUpperCase()} - ${item.created_at}" style="cursor: pointer">
                            ${item.name.length > 15 ? item.name.substring(0, 15).toUpperCase() + '...' : item.name.toUpperCase()}
                        </label>
                    </div>`,

                        "Actions": `
          <div class="text-center">
        <a href="#" data-bs-toggle="tooltip" title="Delete" class="ext-center">
            <i class="material-symbols-rounded text-secondary position-relative text-lg btn-delete">delete</i>
        </a> </div>`
                    }));

                    dataTableSearchtrack1 = new simpleDatatables.DataTable("#tracks-list1", {
                        searchable: true,

                        perPage: 3,
                    });

                    dataTableSearchtrack1.insert(newDataArray);

                },
                error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                }
            });


        }


        function createTableGeofence() {

            $.ajax({
                url: '{{ route('geofence.select') }}',
                type: 'GET',
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {

                    const formatText = (text) => {
                        if (text && text.length > 0) {
                            return text.length > 15 ? text.substring(0, 15).toUpperCase() + '...' : text
                                .toUpperCase();
                        }
                        return '';
                    };

                    const newDataArray = response.map(item => ({
                        "Name": `
                                     
                        <label class="ms-3 my-auto name-label" >
                            ${item.name.length > 15 ? item.name.substring(0, 15).toUpperCase() + '...' : item.name.toUpperCase()}
                        </label>
                    `,
                        "Description": `
                        <label class="ms-3 my-auto name-label" >
                             ${formatText(item.description)}
                        </label>
                    `,

                        "Actions": `
                         <div class="text-center">
        <a href="#" data-bs-toggle="tooltip" title="Delete" >
            <i class="material-symbols-rounded text-secondary position-relative text-lg btn-delete" id="GeofenceItem${item.id}">delete</i>
        </a> </div>`
                    }));

                    dataTableSearchtrack2 = new simpleDatatables.DataTable("#tracks-list2", {
                        searchable: true,

                        perPage: 3,
                    });
                    dataTableSearchtrack2.insert(newDataArray);
                    drawGeofence(response);
                },
                error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    @endsection
