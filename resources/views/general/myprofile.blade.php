@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'myprofile', 'folderPage' => '', 'titlePage' => trans('labels.navs.myprofile')])

@section('content')
    <div class="modal fade" id="qrModalProfile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="qrModalProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form role="form" action="{{ route('two-factor.confirm') }}" class="ms-auto mb-0" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-center" id="qrModalProfileLabel">@lang('labels.texts.scan_qr')</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-center"> @lang('labels.texts.scan_qr_title')</h6>
                        <div class="text-center my-3">
                            @if (auth()->user()->two_factor_secret)
                                {!! auth()->user()->twoFactorQrCodeSvg() !!}
                            @endif
                        </div>
                        <div class="bg-gradient-dark border-radius-lg py-1 pe-1">
                            <div class="container">
                                <div class="row">
                                    <label class="text-white text-center font-weight-bolder mb-0">
                                        @lang('labels.texts.view_recovery_title_2')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-body pt-0" id="contentToPrint">
                                <h6 class="text-center pb-2">@lang('labels.texts.recovery_codes')</h6>
                                <div class="row">
                                    <div class="col">
                                        <ul class="list-group">
                                            @if (auth()->user()->two_factor_secret)
                                                @php
                                                    $recoveryCodes = auth()->user()->recoveryCodes();
                                                    $halfCount = ceil(count($recoveryCodes) / 2);
                                                    $recoveryCodesSet1 = array_slice($recoveryCodes, 0, $halfCount);
                                                @endphp
                                                @foreach ($recoveryCodesSet1 as $code)
                                                    <li
                                                        class="list-group-item text-center border-0 mb-1 bg-gray-100 border-radius-lg printable">
                                                        <label class="mb-0">{{ $code }}</label>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col">
                                        <ul class="list-group">
                                            @if (auth()->user()->two_factor_secret)
                                                @php
                                                    $recoveryCodesSet2 = array_slice($recoveryCodes, $halfCount);
                                                @endphp
                                                @foreach ($recoveryCodesSet2 as $code)
                                                    <li
                                                        class="list-group-item text-center border-0 mb-1 bg-gray-100 border-radius-lg printable">
                                                        <label class="mb-0">{{ $code }}</label>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a onclick="copyAllCodes()" class="btn btn-dark mx-2"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">content_copy</i>@lang('labels.texts.copy_codes')</a>
                                <a onclick="saveAsPDF()" class="btn btn-dark me-2"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">download</i>@lang('labels.texts.download_codes')</a>
                                <a onclick="printCodes()" class="btn btn-dark me-2"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">print</i>@lang('labels.texts.print_codes')</a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-3"></div>
                            <div class="col-md-6 align-items-center">
                                <div
                                    class="input-group input-group-static @error('code') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.insert_code')*</label>
                                    <input name="code" type="text" class="form-control"
                                        @if (!empty(old('code'))) value="{{ old('code') }}" @endif>
                                    @error('code')
                                        {{ dd('ola') }}
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                            class="btn bg-gradient-success">{{ __('labels.texts.two_factor_activate') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if (session('status') == 'two-factor-authentication-enabled')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('qrModalProfile'));
                myModal.show();
            });
        </script>
    @endif
    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <ul class="nav flex-column bg-white border-radius-lg p-3">
                    <li class="nav-item">
                        <a class="nav-link text-dark d-flex" data-scroll="" href="#profile">
                            <i class="material-symbols-rounded text-lg me-2">person</i>
                            <span class="text-sm">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll="" href="#basic-info">
                            <i class="material-symbols-rounded text-lg me-2">receipt_long</i>
                            <span class="text-sm">Basic Info</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll="" href="#password">
                            <i class="material-symbols-rounded text-lg me-2">lock</i>
                            <span class="text-sm">Change Password</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-dark d-flex" data-scroll="" href="#2fa">
                            <i class="material-symbols-rounded text-lg me-2">security</i>
                            <span class="text-sm">2FA</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-0 mt-4">
            <!-- Card Profile -->
            <div class="card card-body" id="profile">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-auto col-4">
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset('assets/images/avatar.png') }}" alt="bruce"
                                class="w-100 rounded-circle shadow-sm">
                        </div>
                    </div>
                    <div class="col-sm-auto col-8 my-auto">
                        <div class="h-100">
                            <h5 class="mb-1 font-weight-bolder">
                                {{ ucwords(auth()->user()->name) }}
                            </h5>
                            <p class="mb-0 font-weight-normal text-sm">
                                {{ ucwords(auth()->user()->roles()->first()->name) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                        <label class="form-check-label mb-0">
                            <small id="profileVisibility">
                                @lang('labels.texts.created_account'):
                                {{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F j, Y') }}
                            </small>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Card Basic Info -->
            <div class="card mt-4" id="basic-info">
                <div class="card-header">
                    <h5>Basic Info</h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('myprofile.update', auth()->user()) }}" class="pb-2" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div
                                    class="input-group input-group-static @error('name') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.name')*</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                        value="{{ auth()->user()->name }}">
                                    @error('name')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-sm-6">
                                <div
                                    class="input-group input-group-static @error('email') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.email')*</label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ auth()->user()->email }}">
                                    @error('email')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <div
                                    class="input-group input-group-static @error('email_confirmation') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.email_confirmation')*</label>
                                    <input id="email_confirmation" name="email_confirmation" type="email"
                                        class="form-control" value="{{ auth()->user()->email }}">
                                    @error('email_confirmation')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-sm-6">
                                <div
                                    class="input-group input-group-static @error('location') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.location')</label>
                                    <input id="location" name="location" type="text" class="form-control"
                                        value="{{ auth()->user()->location }}">
                                    @error('location')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <div
                                    class="input-group input-group-static @error('phone') is-invalid is-filled mb-4 @enderror">
                                    <label class="form-label">@lang('labels.texts.phone')</label>
                                    <input id="phone" name="phone" type="text" class="form-control focus"
                                        value="{{ auth()->user()->phone }}">
                                    @error('phone')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <p class="text-xs mb-1 mt-2">@lang('labels.texts.fields_required')</p>
                        <button type="submit"
                            class="btn bg-gradient-dark btn-sm float-end mt-1 mb-0">@lang('labels.buttons.update_info')</button>
                    </form>
                </div>
            </div>
            <!-- Card Change Password -->
            <div class="card mt-4" id="password">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('myprofile.changePassword', auth()->user()) }}" class="pb-2"
                        method="POST">
                        @csrf
                        <div
                            class="input-group input-group-outline @error('current_password') is-invalid is-filled mb-5 @enderror">
                            <label class="form-label">@lang('labels.texts.current_password')</label>
                            <input name="current_password" type="password" class="form-control"
                                @if (!empty(old('current_password'))) value="{{ old('current_password') }}" @endif>
                            @error('current_password')
                                <small>{{ $message }}</small>
                            @enderror
                        </div>
                        <div
                            class="input-group input-group-outline my-3 @error('new_password') is-invalid is-filled mb-5 @enderror">
                            <label class="form-label">@lang('labels.texts.new_password')</label>
                            <input name="new_password" type="password" class="form-control"
                                @if (!empty(old('new_password'))) value="{{ old('new_password') }}" @endif>
                            @error('new_password')
                                <small>{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="input-group input-group-outline">
                            <label class="form-label">@lang('labels.texts.new_password_confirmation')</label>
                            <input name="new_password_confirmation" type="password" class="form-control"
                                @if (!empty(old('new_password_confirmation'))) value="{{ old('new_password_confirmation') }}" @endif>
                        </div>
                        <h5 class="mt-5">@lang('labels.texts.password_requirements')</h5>
                        <p class="text-muted mb-2">
                            @lang('labels.texts.details_password_requirements')
                        </p>
                        <ul class="text-muted ps-4 mb-0 float-start">
                            <li>
                                <span class="text-sm">@lang('labels.texts.one_requirement')</span>
                            </li>
                            <li>
                                <span class="text-sm">@lang('labels.texts.two_requirement')</span>
                            </li>
                            <li>
                                <span class="text-sm">@lang('labels.texts.three_requirement')</span>
                            </li>
                            <li>
                                <span class="text-sm">@lang('labels.texts.four_requirement')</span>
                            </li>
                        </ul>
                        <button type="submit"
                            class="btn bg-gradient-dark btn-sm float-end mt-6 mb-0">@lang('labels.buttons.update_password')</button>
                    </form>
                </div>
            </div>
            <!-- Card Change Password -->
            <div class="card mt-4" id="2fa">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">
                            @lang('labels.texts.two_factor_authentication')
                            @if (auth()->user()->two_factor_confirmed_at)
                                <span class="badge badge-sm bg-gradient-success">@lang('labels.texts.enabled') </span>
                            @endif
                        </h5>
                        @if (auth()->user()->two_factor_secret)
                            <form role="form" action="{{ route('two-factor.disable') }}" class="ms-auto mb-0"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm bg-gradient-danger">@lang('labels.texts.two_factor_disable')</button>
                            </form>
                        @else
                            <form role="form" id="miFormulario" action="{{ route('two-factor.enable') }}"
                                class="ms-auto mb-0" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm bg-gradient-dark">@lang('labels.texts.two_factor_activate')</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    @if (auth()->user()->two_factor_confirmed_at)
                        <div class="mb-4 font-medium text-sm">
                            @lang('labels.texts.two_factor_activated')
                        </div>
                    @elseif (auth()->user()->two_factor_secret)
                        <div class="card">
                            <div class="card-body border-radius-lg bg-gradient-dark p-3">
                                <h6 class="mb-0 text-white">
                                    @lang('labels.texts.two_factor_active_critical')
                                </h6>
                                <p class="text-white text-sm mb-4">
                                    @lang('labels.texts.two_factor_active_critical_description')
                                </p>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#qrModalProfile">
                                    @lang('labels.texts.view_qr')
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-sm">
                            @lang('labels.texts.two_factor_description')
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if (session('panel') == 'basic-info' || session('panel') == 'password')
        <script>
            var profileLink = document.getElementById("{{ session('panel') }}" + '-link');
            if (profileLink) {
                profileLink.click();
            }
        </script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script>
        const translations = {
            title: @json(__('labels.texts.recovery_codes'))
        };
    </script>
    <script src="{{ asset('assets/js/myprofile.js') }}"></script>
@endsection
