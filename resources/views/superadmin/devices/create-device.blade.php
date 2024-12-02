@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'super-admin', 'folderPage' => trans('labels.navs.superadmin'), 'titlePage' => trans('labels.navs.accounts_devices') . ' / ' . trans('labels.navs_second.create_device')])

@section('content')
    <div class="row mb-8">
        <div class="col-lg-8 mt-lg-0 mt-4 mx-auto my-auto position-relative">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bolder">@lang('labels.texts.new_device')</h5>
                    <form action="{{ route('devices.store') }}" class="form-control" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div
                                    class="input-group input-group-dynamic @if ($errors->has('name')) is-filled is-invalid focused is-focused @endif">
                                    <label class="form-label">@lang('labels.texts.name')*</label>
                                    <input type="text" name="name" class="form-control w-100"
                                        @if (!empty(old('name'))) value="{{ old('name') }}" @endif>
                                </div>
                                @error('name')
                                    <span class="text-danger text-sm"> {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <div
                                    class="input-group input-group-dynamic @if ($errors->has('unique_id')) is-filled is-invalid focused is-focused @endif">
                                    <label class="form-label">@lang('labels.texts.unique_id')*</label>
                                    <input type="text" name="unique_id" class="form-control w-100"
                                        @if (!empty(old('unique_id'))) value="{{ old('unique_id') }}" @endif>
                                </div>
                                @error('unique_id')
                                    <span class="text-danger text-sm"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="input-group input-group-dynamic">
                                    <label class="ms-0 font-weight-bolder">@lang('labels.texts.type_device')*</label>
                                    <select class="selectpicker" id="select-type-device" name="type_device"
                                        data-live-search="true" data-size="5" data-width="100%" title="@lang('labels.texts.choose_an_option')">
                                        @foreach ($typeDevices as $key => $value)
                                            <option value="{{ $key }}">
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_device')
                                        <span class="text-danger text-sm"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-dynamic">
                                    <label class="ms-0 font-weight-bolder">@lang('labels.texts.choose_engines')</label>
                                    <select class="selectpicker" id="select-engines" name="engines[]"
                                        data-live-search="true" multiple data-actions-box="true" data-size="5"
                                        data-width="100%" title="@lang('labels.texts.choose_several_options')">
                                        @foreach ($engines as $key => $value)
                                            <option value="{{ $key }}">
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('engines')
                                        <span class="text-danger text-sm"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="button-row d-flex mt-4">
                            <a class="btn bg-gradient-light mb-0 js-btn-prev" type="button"
                                href="{{ url('/super-admin') }}" title="@lang('labels.buttons.back')">@lang('labels.buttons.back')</a>
                            <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" type="submit"
                                title="@lang('labels.buttons.save')">@lang('labels.buttons.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/assets/js/plugins/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/superadmin.js') }}"></script>
@endsection
