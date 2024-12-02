@extends('layouts.app', ['class' => 'g-sidenav-show  bg-gray-100', 'activePage' => 'super-admin', 'folderPage' => trans('labels.navs.superadmin'), 'titlePage' => trans('labels.navs.accounts_devices')])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">@lang('labels.texts.accounts_traccar_list')</h5>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <a href="{{ route('companies.create') }}" class="btn bg-gradient-dark btn-sm mb-0">+&nbsp;
                                    @lang('labels.texts.create_company')</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive">
                        <table class="table table-flush" id="tc-users-list">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID Traccar</th>
                                    <th>@lang('labels.texts.name')</th>
                                    <th>@lang('labels.texts.email')</th>
                                    <th>@lang('labels.texts.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tcUsers as $key => $value)
                                    <tr>
                                        <td class="text-sm py-2">{{ $value->id }}</td>
                                        <td class="text-sm py-2">{{ ucwords($value->name) }}</td>
                                        <td class="text-sm py-2">{{ $value->email }}
                                        </td>
                                        <td class="py-2">
                                            <a href="javascript:;" class="mx-2" data-bs-toggle="tooltip"
                                                data-bs-html="true"
                                                title="@foreach ($value->devices as $key => $device) {{ ucwords(strtolower($device)) }}</br> @endforeach">
                                                <i
                                                    class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>
                                            </a>
                                            @if ($value->id != 1)
                                                <a href="{{ route('companies.edit', $value->id) }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="@lang('labels.texts.edit_company')">
                                                    <i
                                                        class="material-symbols-rounded text-secondary position-relative text-lg">drive_file_rename_outline</i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID Traccar</th>
                                    <th>@lang('labels.texts.name')</th>
                                    <th>@lang('labels.texts.email')</th>
                                    <th>@lang('labels.texts.actions')</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">@lang('labels.texts.devices_traccar_list')</h5>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <a href="{{ route('devices.create') }}" class="btn bg-gradient-dark btn-sm mb-0">+&nbsp;
                                    @lang('labels.texts.create_device')</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive">
                        <table class="table table-flush" id="tc-devices-list">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('labels.texts.unique_id')</th>
                                    <th>@lang('labels.texts.name')</th>
                                    <th>@lang('labels.texts.last_updated_at')</th>
                                    <th>@lang('labels.texts.status')</th>
                                    <th>@lang('labels.texts.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tcDevices as $key => $value)
                                    <tr>
                                        <td class="text-sm py-2">{{ $value['id'] }}</td>
                                        <td class="text-sm py-2">{{ $value['uniqueId'] }}</td>
                                        <td class="text-sm py-2">
                                            <div class="d-flex align-items-center">
                                                <img src='{{ asset('assets/images/' . $value['category']) . '.png' }}'
                                                    class="avatar avatar-xs me-2" alt="user image">
                                                <span>{{ ucwords(strtolower($value['name'])) }}</span>
                                            </div>
                                        <td class="text-sm py-2">{{ $value['lastUpdate'] }}
                                        <td class="text-xs font-weight-normal">
                                            <div class="d-flex align-items-center">
                                                <button
                                                    class="btn btn-icon-only btn-rounded btn-outline-{{ $value['status'] == 'online' ? 'success' : 'danger' }} mb-0 me-2 btn-sm d-flex align-items-center justify-content-center"><i
                                                        class="material-symbols-rounded text-sm"
                                                        aria-hidden="true">{{ $value['status'] == 'online' ? 'done' : 'clear' }}</i></button>
                                                <span>{{ ucwords(strtolower($value['status'])) }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <a href="{{ route('devices.edit', $value['id']) }}" class="mx-2"
                                                data-bs-toggle="tooltip" data-bs-original-title="@lang('labels.texts.edit_device')">
                                                <i
                                                    class="material-symbols-rounded text-secondary position-relative text-lg">drive_file_rename_outline</i>
                                            </a>
                                            <a href="#" data-bs-toggle="tooltip"
                                                data-bs-original-title="@lang('labels.texts.destroy_device')"
                                                onclick="confirmDeleteDevice(event, '{{ route('devices.destroy', $value['id']) }}')">
                                                <i
                                                    class="material-symbols-rounded text-secondary position-relative text-lg">delete</i>
                                            </a>
                                            <form id="deleteFormDevice" method="POST">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>@lang('labels.texts.unique_id')</th>
                                    <th>@lang('labels.texts.name')</th>
                                    <th>@lang('labels.texts.last_updated_at')</th>
                                    <th>@lang('labels.texts.status')</th>
                                    <th>@lang('labels.texts.actions')</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('template/assets/js/plugins/datatables.js') }}"></script>
    <script>
        const translations = {
            confirm_delete: @json(__('messages.confirm_delete'))
        };
    </script>
    <script src="{{ asset('assets/js/superadmin.js') }}"></script>
@endsection
