<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{route('root')}}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{asset('storage/logo-sm.png')}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset('storage/logo-dark.png')}}" alt="" height="22">
                        </span>
                    </a>

                    <a href="{{route('root')}}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{asset('storage/logo-sm.png')}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset('storage/logo-light.png')}}" alt="" height="22">
                        </span>
                    </a>
                </div>
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
            <div class="d-flex align-items-center">
                <div class="ms-1 header-item d-none d-sm-flex" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Pantalla Completa">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text" data-toggle="fullscreen">
                        <i class='ti ti-arrows-maximize fs-3xl'></i>
                    </button>
                </div>
                <div class="dropdown topbar-head-dropdown ms-1 header-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modo claro/oscuro">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text mode-layout" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($configuracion->data_bs_theme == 'light')
                            <i class="ti ti-sun align-middle fs-3xl"></i>
                        @else
                            <i class="ti ti-moon align-middle fs-3xl"></i>
                        @endif
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" id="light-dark-mode">
                        <form id="light-mode-form">
                            <button type="button" class="dropdown-item light-mode-btn" data-mode="light" data-id="{{Auth::id()}}" @if($configuracion->data_bs_theme == 'light') style="pointer-events: none" @endif><i class="bi bi-sun align-middle me-2"></i>@if($configuracion->data_bs_theme == 'light')<strong>Claro</strong>@else Claro @endif</button>
                        </form>
                        <form id="dark-mode-form">
                            <button type="button" class="dropdown-item dark-mode-btn" data-mode="dark" data-id="{{Auth::id()}}" @if($configuracion->data_bs_theme == 'dark') style="pointer-events: none" @endif><i class="bi bi-moon align-middle me-2"></i>@if($configuracion->data_bs_theme == 'dark')<strong>Oscuro</strong>@else Oscuro @endif</button>
                        </form>
                    </div>
                </div>
                <div class="dropdown ms-sm-3 topbar-head-dropdown dropdown-hover-end header-item topbar-user">
                    <button type="button" class="btn shadow-none btn-icon" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{asset('storage/usuarios/'. Auth::user()->avatar)}}" alt="Header Avatar">
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Mi Perfil">
                        <!-- item-->
                        <h6 class="dropdown-header">Hola, {{Auth::user()->name}}</h6>
                        <a class="dropdown-item fs-sm" href="{{route('perfiles.show', Auth::id())}}"><i class="bi bi-person-circle text-muted align-middle me-1"></i> <span class="align-middle">Mi Perfil</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item fs-sm" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right text-muted align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Cerrar Sesión</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="wrapper"></div>
