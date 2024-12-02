@if (session()->has('success'))
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="successToast" aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-success me-2">
                    check
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.success')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                {{ session()->get('success') }}
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('successToast')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif



@if (session()->has('errors') && $errors->getBag('confirmTwoFactorAuthentication')->has('code'))
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="dangerToast"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-danger me-2">
                    campaign
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.error')</span>
                <small class="text-body">@lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                {{ $errors->getBag('confirmTwoFactorAuthentication')->first('code') }}
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('dangerToast')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif

@if (session()->has('errors'))
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="dangerToast"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-danger me-2">
                    campaign
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.error')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                <ul class="m-0 p-0" style="list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('dangerToast')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif

@if (session()->has('error'))
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="dangerToast"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-danger me-2">
                    campaign
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.error')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                <ul class="m-0 p-0" style="list-style: none;">
                    {{ session('error') }}
                </ul>
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('dangerToast')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif


@if (session('status') == 'two-factor-authentication-disabled')
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="successToast2fa"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-success me-2">
                    check
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.success')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                <label>{{ trans('messages.two_factor_activate') }}</label>
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('successToast2fa')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif


@if (session('status') == 'two-factor-authentication-enabled')
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="successToast2fa"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-success me-2">
                    check
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.success')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                <label>{{ trans('messages.two_factor_activated_qr') }}</label>
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('successToast2fa')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif
{{-- 
{{ dd(session()) }} --}}


@if (session('status') == 'two-factor-authentication-confirmed')
    <div class="position-fixed top-1 end-1" style="z-index: 9999">
        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="successToast2fa"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-success me-2">
                    check
                </i>
                <span class="me-auto font-weight-bold">@lang('labels.texts.success')</span>
                <small class="text-body"> @lang('labels.texts.just_now')</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                <label>{{ trans('messages.two_factor_activated') }}</label>
            </div>
        </div>
    </div>
    <script type="module">
        const toastLiveExample = document.getElementById('successToast2fa')
        const toast = new bootstrap.Toast(toastLiveExample)
        toast.show()
    </script>
@endif
