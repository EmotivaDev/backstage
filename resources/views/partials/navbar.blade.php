<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href=" https://www.emotiva.co " target="_blank">
            <img class="mb-2 ms-1" src="{{ asset('assets/images/logo-rivertech.png') }}" alt="logo" width="25"
                height="120">
            <span class="text-dark logo-rivertech">
                <span class="dark-blue">@lang('labels.texts.sidebar_title_one')</span><span class="light-blue">@lang('labels.texts.sidebar_title_two')</span>
            </span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @if (auth()->user()->canany(config('nav_permissions.my_profile')))
                <li class="nav-item clps mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav"
                        class="nav-link text-dark {{ isActive('myprofile', $activePage) }}" aria-controls="ProfileNav"
                        role="button" aria-expanded="false">
                        <img src="{{ asset('assets/images/avatar.png') }}" class="avatar">
                        <span class="nav-link-text ms-2 ps-1">{{ ucwords(substr(auth()->user()->name, 0, 15)) }}</span>
                    </a>
                    <div class="collapse {{ isShow('myprofile', $activePage) }}" id="ProfileNav">
                        <ul class="nav">
                            <li class="nav-item {{ isActive('myprofile', $activePage) }}">
                                <a class="nav-link text-dark {{ isActive('myprofile', $activePage) }}"
                                    href="{{ url('/myprofile') }}">
                                    <span class="sidenav-mini-icon"> @lang('labels.navs.abbr_myprofile') </span>
                                    <span class="sidenav-normal ms-3 ps-1"> @lang('labels.navs.myprofile')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            <hr class="horizontal dark mt-0">
            @if (auth()->user()->canany(config('nav_permissions.dashboard')))
                <li class="nav-item {{ isActive('dashboard', $activePage) }} ">
                    <a class="nav-link  {{ isActive('dashboard', $activePage) }} " href="{{ url('/') }}">
                        <i class="material-symbols-rounded opacity-5">space_dashboard</i>
                        <span class="sidenav-normal ms-3 ps-1"> @lang('labels.navs.dashboard') </span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasRole('super administrator'))
                <li class="nav-item clps">
                    <a data-bs-toggle="collapse" href="#superAdminNav"
                        class="nav-link text-dark {{ isActive('super-admin', $activePage) }}"
                        aria-controls="superAdminNav" role="button" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">shield_person</i>
                        <span class="nav-link-text ms-1 ps-1">@lang('labels.navs.superadmin')</span>
                    </a>
                    <div class="collapse {{ isShow('super-admin', $activePage) }}" id="superAdminNav">
                        <ul class="nav ">
                            {{-- <li class="nav-item">
                                <a class="nav-link text-dark" href="">
                                    <span class="sidenav-mini-icon"> @lang('labels.navs.abbr_analytics') </span>
                                    <span class="sidenav-normal  ms-1  ps-1"> @lang('labels.navs.analytics') </span>
                                </a>
                            </li> --}}
                            <li class="nav-item {{ isActive('super-admin', $activePage) }}">
                                <a class="nav-link text-dark {{ isActive('super-admin', $activePage) }}"
                                    href="{{ url('/super-admin') }}">
                                    <span class="sidenav-mini-icon"> @lang('labels.navs.abbr_accounts_devices') </span>
                                    <span class="sidenav-normal  ms-1  ps-1"> @lang('labels.navs.accounts_devices') </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            @if (auth()->user()->canany(config('nav_permissions.ais')))
                <li class="nav-item clps">
                    <a data-bs-toggle="collapse" href="#aisNav"
                        class="nav-link text-dark {{ isActive(['ais', 'aishistory'], $activePage) }}"
                        aria-controls="aisNav" role="button" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">directions_boat</i>
                        <span class="nav-link-text ms-1 ps-1">@lang('labels.navs.ais')</span>
                    </a>

                    <div class="collapse {{ isShow(['ais', 'aishistory'], $activePage) }}" id="aisNav">
                        <ul class="nav ">
                            <li class="nav-item {{ isActive('ais', $activePage) }}">
                                <a class="nav-link text-dark {{ isActive('ais', $activePage) }}"
                                    href="{{ url('/ais') }}">
                                    <span class="sidenav-mini-icon"> @lang('labels.navs.abbr_ais_realtime') </span>
                                    <span class="sidenav-normal  ms-1  ps-1"> @lang('labels.navs.ais_realtime') </span>
                                </a>
                            </li>
                            <li class="nav-item {{ isActive('aishistory', $activePage) }}">
                                <a class="nav-link text-dark {{ isActive('aishistory', $activePage) }}"
                                    href="{{ url('/aishistory') }}">
                                    <span class="sidenav-mini-icon"> @lang('labels.navs.abbr_aishistory') </span>
                                    <span class="sidenav-normal  ms-1  ps-1"> @lang('labels.navs.history') </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            @if (auth()->user()->canany(config('nav_permissions.realtime')))
                <li class="nav-item {{ isActive('realtime', $activePage) }} ">
                    <a class="nav-link  {{ isActive('realtime', $activePage) }} " href="{{ url('/realtime') }}">
                        <i class="material-symbols-rounded opacity-5">map</i>
                        <span class="sidenav-normal ms-3 ps-1"> @lang('labels.navs.realtime') </span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->canany(config('nav_permissions.history')))
                <li class="nav-item {{ isActive('history', $activePage) }} ">
                    <a class="nav-link  {{ isActive('history', $activePage) }} " href="{{ url('/history') }}">
                        <i class="material-symbols-rounded opacity-5">history</i>
                        <span class="sidenav-normal ms-3 ps-1"> @lang('labels.navs.history') </span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    @if (Route::currentRouteName() != 'realtime')
        <nav class="navbar navbar-main navbar-expand-lg {{ Route::currentRouteName() !== 'myprofile' ? 'position-sticky' : '' }} mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky"
            id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-2">
                <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none ">
                    <a href="javascript:;" class="nav-link text-body p-0">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </div>
                <nav aria-label="breadcrumb" class="ps-2">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                href="javascript:;">{{ $folderPage }}</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">
                            {{ $titlePage }}</li>
                    </ol>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    </div>
                    <ul class="navbar-nav  justify-content-end">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline"
                                data-bs-toggle="tooltip" title="@lang('labels.texts.logout')">
                                @csrf
                                <button type="submit" class="px-1 py-0 nav-link line-height-0"
                                    style="background: none; border: none; cursor: pointer;">
                                    <i class="material-symbols-rounded">
                                        logout
                                    </i>
                                </button>
                            </form>
                        </li>
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @else
        <x-navbar-realtime />
    @endif
