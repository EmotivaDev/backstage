@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'history', 'folderPage' => '', 'titlePage' => trans('labels.navs.history')])

@section('content')
    <div id="loading"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only text-dark"> </span>
            </div>
            <h4>@lang('labels.texts.loading')...</h4>
        </div>
    </div>
    <div class="row mb-8">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="input-group input-group-static">
                                <label>@lang('labels.texts.start_date')*</label>
                                <input class="form-control datetimepicker" type="text" id="date_start" data-input>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="input-group input-group-static">
                                <label>@lang('labels.texts.end_date')*</label>
                                <input class="form-control datetimepicker" type="text" id="date_end" data-input>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div
                                class="input-group input-group-static @error('device') is-invalid is-filled mb-4 @enderror">
                                <label>@lang('labels.texts.choose_device')*</label>
                                <select class="selectpicker" id="select-devices" name="type_device" data-live-search="true"
                                    data-size="5" data-width="100%" title="@lang('labels.texts.choose_an_option')">
                                    @foreach ($tcDevices as $key => $value)
                                        <option value="{{ $value['id'] }}">
                                            {{ strtoupper($value['name']) }}</option>
                                    @endforeach
                                </select>
                                @error('device')
                                    <div class="text-danger ms-1"><small> {{ $message }}</small></div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12 my-auto ms-auto text-center mb-1">
                            <div class="align-items-center pt-2">
                                <button onclick="fetchDeviceData()" type="button" class="btn bg-gradient-dark shadow mb-0">
                                    <i class="material-symbols-rounded text-lg position-relative me-1">
                                        search
                                    </i>
                                    @lang('labels.texts.search')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 py-0">
                    <div id="map-history">
                    </div>
                    <div class="col-md-4 col-md-2"
                        style="width: 100%;border-style: none; position: relative; top: -497px; left: 5px; z-index: 100;">
                        <div class="map-card-history">
                            <div class="row text-center">
                                <div class="col-2 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.date')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_one">-</h6>
                                </div>
                                <div class="col-1 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.speed')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_two">-</h6>
                                </div>
                                <div class="col-2 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.location')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_three">-</h6>
                                </div>
                                <div class="col-1 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.depth')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_four">-</h6>
                                </div>
                                <div class="col-2 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.rpm')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_five">-</h6>
                                </div>
                                <div class="col-2 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.ect')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_six">-</h6>
                                </div>
                                <div class="col-2 px-0">
                                    <p class="text-xs font-weight-bold mb-0">@lang('labels.texts.eop')</p>
                                    <h6 class="text-xs font-weight-normal mb-0" id="info_seven">-</h6>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="card-footer pt-0">
                    <hr class="horizontal light">
                    <div class="row">
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.travel_milemark')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="travel_milemark">-</p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.travel_distance')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="travel_distance">-</p>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.avg_rpm')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="avg_rpm">-</p>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.max_min_ect')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="max_min_ect">-</p>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.max_min_eop')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="max_min_eop">-</p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.total_time')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="total_time">-</p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6 col-12">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.avg_cog')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="avg_cog">-</p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6 col-12 ms-lg-auto">
                            <div class="text-center position-relative">
                                <h6 class="mb-0 text-sm">@lang('labels.texts.avg_speed')</h6>
                                <p class="text-sm font-weight-normal text-secondary mb-0" id="avg_speed">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <div class="avatar avatar-lg">
                                        <img src="{{ asset('assets/images/boat.png') }}">
                                    </div>
                                </div>
                                <div class="px-3">
                                    <h6 class=" text-sm font-weight-bold mb-0" id="name">
                                        -
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-8 my-auto ms-auto text-center">
                            <div class="mb-2" id="sliderRegular"></div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-4 col-8 my-auto ms-auto text-center">
                            <div class="dropleft ms-0">
                                <select class="selectpicker" id="select-files" name="type_device"
                                    data-live-search="true" data-size="5" data-width="100%" title="@lang('labels.texts.download_files')">
                                    <option value="rose_point_track"> @lang('labels.texts.rose_point_track')</option>
                                    <option value="open_cpn_track"> @lang('labels.texts.open_cpn_track')</option>
                                    <option value="kml_track"> @lang('labels.texts.kml_track')</option>
                                    <option value="excel_csv"> @lang('labels.texts.excel_csv')</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('template/assets/js/plugins/flatpickr.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/plugins/nouislider.min.js') }}"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('KEY_MAP') }}&libraries=drawing&callback=initMap"></script>
    <script src="{{ asset('assets/js/history.js') }}"></script>
@endsection
