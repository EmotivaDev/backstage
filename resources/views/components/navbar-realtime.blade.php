<nav class="navbar navbar-main navbar-expand-lg mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky"
    id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2 color-opacity border-radius-lg ">
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
            <div class="d-flex align-items-center">
                <div class="input-group input-group-outline flex-grow-1">
                    <label class="form-label label-search-bar">Search Device</label>
                    <input type="text" class="search-bar form-control form-control-sm m-0 " id="searchBar" oninput="filterCards()">
                </div>
                <a href="" class="px-1 py-0 nav-link line-height-0 device-accordion-button collapsed" data-bs-toggle="collapse" 
                   data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="material-symbols-rounded arrow-icon">
                        arrow_downward
                    </i>
                </a>
            </div>
            
            {{-- <input type="text" class="form-control search-bar mb-0" id="searchBar" oninput="filterCards()"  
            placeholder="Search devices..."> --}}
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">

            </div>
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline" data-bs-toggle="tooltip"
                        title="@lang('labels.texts.logout')">
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
