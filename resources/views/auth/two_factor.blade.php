<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('template/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Rivertech') }}</title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('template/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('template/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('template/assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/two-factor.js') }}"></script>

</head>

<body class="">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            <div class="position-relative h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                                style="background-image:  url('{{ asset('assets/images/login.png') }}');  background-size: cover;">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                            <div class="card-plain">
                                <div class="card" id="div1" style="display: none;">
                                    <div class="card-header text-center">
                                        <img src="{{ asset('assets/images/phone.png') }}" class="w-30"
                                            alt="image">
                                    </div>
                                    <div class="card-body">
                                        <h4 class="font-weight-bolder mb-0 text-center">@lang('labels.texts.step_verification')</h4>
                                        <label class="text-center">@lang('labels.texts.two_factor_enter_code')</label>
                                        <form method="POST" action="{{ route('two-factor.login') }}">
                                            @csrf
                                            <div class="card-body px-lg-0 py-lg-2 text-center">
                                                <div class="gx-2 gx-sm-3 mb-4">
                                                    <div
                                                        class="input-group input-group-dynamic @error('code') is-invalid is-filled mb-2 @enderror">
                                                        <label class="form-label">@lang('labels.texts.insert_code')</label>
                                                        <input name="code" type="text" class="form-control">
                                                        @error('code')
                                                            <small>{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit"
                                                        class="btn bg-gradient-info w-100">@lang('labels.buttons.send')</button>
                                                </div>
                                                <div class="text-center">
                                                    <h6 class="mb-0">@lang('labels.texts.two_factor_for_recovery_title_one')</h6>
                                                    <a onclick="toggleDiv('div1', 'div2')"
                                                        class="text-info">@lang('labels.texts.two_factor_for_recovery_title_two')</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card py-4" id="div2"
                                    style="display: none; padding-top: 16px !important; padding-bottom: 16px !important;">
                                    <div class="card-header text-center">
                                        <img src="{{ asset('assets/images/recovery.png') }}" class="w-35"
                                            alt="image">
                                    </div>
                                    <div class="card-body">
                                        <h4 class="font-weight-bolder mb-0 text-center">@lang('labels.texts.recovery_enter_code')</h4>
                                        <label class="text-center">@lang('labels.texts.two_factor_title_one')</label>
                                        <form method="POST" action="{{ route('two-factor.login') }}">
                                            @csrf
                                            <div class="card-body px-lg-0 py-lg-2 text-center">
                                                <div class="gx-2 gx-sm-3 mb-4">
                                                    <div
                                                        class="input-group input-group-dynamic @error('recovery_code') is-invalid is-filled mb-2 @enderror">
                                                        <label class="form-label">@lang('labels.texts.recovery_enter_code')</label>
                                                        <input name="recovery_code" type="text" class="form-control ">
                                                        @error('recovery_code')
                                                            <script>
                                                                toggleDiv('div2', 'div1')
                                                            </script>
                                                            <small>{{ $message }}</small>
                                                        @else
                                                            <script>
                                                                toggleDiv('div1', 'div2')
                                                            </script>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit"
                                                        class="btn bg-gradient-info w-100">@lang('labels.buttons.send')</button>
                                                        <a onclick="toggleDiv('div2', 'div1')"
                                                        class="text-info">@lang('labels.buttons.back')</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!--   Core JS Files   -->
    <script src="{{ asset('template/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <!-- Kanban scripts -->
    <script src="{{ asset('template/assets/js/plugins/dragula/dragula.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/plugins/jkanban/jkanban.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('template/assets/js/material-dashboard.min.js?v=3.1.0') }}"></script>

</body>

</html>
