<!doctype html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{config('app.name')}} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap">
    <style>
        :root{
            --ac-accent:#0f4c5c; --ac-accent-soft:#dbe7ea; --ac-bg:#f7f9f9; --ac-surface:#ffffff;
            --ac-text:#1b2427; --ac-text-muted:#8b989c; --ac-border:#e4eaec; --ac-border-strong:#d3dade;
            --ac-ok-bg:#e2efe9; --ac-ok-fg:#1f6b4f; --ac-warn-bg:#f8ecdd; --ac-warn-fg:#8a4d13;
            --ac-info-bg:#e3eef1; --ac-info-fg:#0f4c5c; --ac-danger-bg:#f4e1e1; --ac-danger-fg:#8a2b2b;
            --ac-neutral-bg:#eef2f3; --ac-neutral-fg:#33454a;
        }
        body{margin:0;font-family:'Open Sans','Segoe UI',system-ui,-apple-system,sans-serif !important;color:var(--ac-text);background:var(--ac-bg);}
        .ac-mono{font-family:'IBM Plex Mono',monospace;}

        /* ---- Reskin de Bootstrap: mismas clases, look de la maqueta académica ---- */
        .btn{border-radius:6px !important;font-family:inherit;}
        .btn-success, .btn-primary{background-color:var(--ac-accent) !important;border-color:var(--ac-accent) !important;}
        .btn-success:hover, .btn-primary:hover{background-color:#0c3b47 !important;border-color:#0c3b47 !important;}
        .btn-warning{background-color:#fff !important;border-color:var(--ac-border-strong) !important;color:var(--ac-text) !important;}
        .btn-danger{border-radius:6px !important;}
        .card{border-radius:8px !important;border:1px solid var(--ac-border) !important;box-shadow:0 1px 2px rgba(15,76,92,0.06) !important;}
        .card-header{background:var(--ac-surface) !important;border-bottom:1px solid var(--ac-border) !important;}
        .table thead th{font-family:'IBM Plex Mono',monospace;font-size:10.5px;font-weight:600;color:var(--ac-text-muted);text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--ac-border-strong) !important;}
        .table td{font-size:12.5px;vertical-align:middle;}
        .table-light{background:transparent !important;}
        .form-control, .form-select, .selectpicker + .dropdown-toggle{border-radius:6px !important;border-color:var(--ac-border-strong) !important;background:#fff !important;font-size:12.5px !important;}
        .form-control:focus, .form-select:focus{border-color:var(--ac-accent) !important;box-shadow:0 0 0 1px var(--ac-accent) !important;}
        .badge{border-radius:4px !important;font-weight:600;font-size:11px;}
        /* Chips de estado: mismo mapeo de colores que la maqueta original */
        .bg-success-subtle, .bg-success-subtle.text-success{background:var(--ac-ok-bg) !important;color:var(--ac-ok-fg) !important;}
        .bg-warning-subtle, .bg-warning-subtle.text-warning{background:var(--ac-warn-bg) !important;color:var(--ac-warn-fg) !important;}
        .bg-info-subtle, .bg-info-subtle.text-info{background:var(--ac-info-bg) !important;color:var(--ac-info-fg) !important;}
        .bg-danger-subtle, .bg-danger-subtle.text-danger{background:var(--ac-danger-bg) !important;color:var(--ac-danger-fg) !important;}
        .bg-secondary-subtle, .bg-secondary-subtle.text-secondary{background:var(--ac-neutral-bg) !important;color:var(--ac-neutral-fg) !important;}
        .breadcrumb{font-size:12px;}
        .page-title-box{margin-bottom:16px;}
        .page-title-box h4{font-size:18px;font-weight:700;color:var(--ac-text);}
        .page-title-box .breadcrumb-item a{color:var(--ac-text-muted);}
        .page-title-box .breadcrumb-item.active{color:var(--ac-accent);font-weight:600;}
        a{color:var(--ac-accent);}
        a:hover{color:#0c3b47;}

        /* ---- Estructura de la maqueta académica ---- */
        .ac-shell{display:flex;min-height:100vh;}
        .ac-nav{width:250px;flex:0 0 250px;background:var(--ac-surface);border-right:1px solid var(--ac-border);padding:18px 10px;box-sizing:border-box;}
        .ac-nav-brand{display:flex;align-items:center;gap:10px;padding:0 8px 18px;}
        .ac-nav-brand svg{flex:0 0 auto;}
        .ac-nav-brand span{font-size:13px;font-weight:700;color:var(--ac-text);line-height:1.25;}
        .ac-nav-group{font-family:'IBM Plex Mono',monospace;font-size:10px;color:var(--ac-text-muted);padding:14px 10px 6px;text-transform:uppercase;letter-spacing:0.05em;}
        .ac-nav-item{display:flex;align-items:center;gap:10px;height:36px;padding:0 10px;border-radius:6px;font-size:13px;color:var(--ac-text);text-decoration:none;}
        .ac-nav-item:hover{background:rgba(15,76,92,0.06);color:var(--ac-text);}
        .ac-nav-item.active{background:var(--ac-accent-soft);color:var(--ac-accent);font-weight:600;}
        .ac-nav-item .ac-num{margin-left:auto;font-family:'IBM Plex Mono',monospace;font-size:10px;color:var(--ac-text-muted);}
        .ac-nav-item.active .ac-num{color:var(--ac-accent);}
        .ac-content-col{flex:1;display:flex;flex-direction:column;min-width:0;background:var(--ac-bg);}
        .ac-topbar{flex:0 0 auto;background:var(--ac-surface);border-bottom:1px solid var(--ac-border);padding:14px 24px 0;}
        .ac-topbar-row{display:flex;align-items:center;gap:14px;padding-bottom:12px;}
        .ac-topbar-row .ac-title{font-size:16px;font-weight:700;color:var(--ac-text);}
        .ac-tabs{display:flex;gap:4px;}
        .ac-tab{padding:8px 16px 10px;font-size:12.5px;font-weight:600;color:var(--ac-text-muted);background:var(--ac-border);border-radius:6px 6px 0 0;text-decoration:none;}
        .ac-tab.active{background:var(--ac-bg);color:var(--ac-text);}
        .ac-avatar{width:28px;height:28px;border-radius:50%;background:var(--ac-accent);display:flex;align-items:center;justify-content:center;font-size:10.5px;color:#fff;font-weight:700;flex:0 0 28px;font-family:'IBM Plex Mono',monospace;}
        .ac-user{font-size:12.5px;color:var(--ac-text-muted);}
        .ac-user b{color:var(--ac-text);font-weight:600;}
        .ac-page{flex:1;overflow:auto;padding:22px 24px;}
        .ac-statusbar{height:26px;flex:0 0 26px;background:var(--ac-accent);display:flex;align-items:center;padding:0 14px;color:#fff;font-size:11px;font-family:'IBM Plex Mono',monospace;}
    </style>
</head>

<body>
    <div class="ac-shell">
        <div class="ac-nav">
            <div class="ac-nav-brand">
                <svg width="22" height="22" viewBox="0 0 28 28" fill="none"><path d="M14 3L26 9.5V18.5L14 25L2 18.5V9.5L14 3Z" stroke="#0f4c5c" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3V14M14 14L26 9.5M14 14L2 9.5M14 14V25" stroke="#0f4c5c" stroke-width="1.6" stroke-linejoin="round"/></svg>
                <span>Sistema Académico<br>Extensión Universitaria</span>
            </div>

            @if (Auth::user()->hasRole('ALUMNO'))
                <div class="ac-nav-group">Inicio</div>
                <a href="{{route('pantallas_alumnos.index', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_alumnos.index') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="3" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
                    Panel
                    <span class="ac-num">01</span>
                </a>
                @can('ver_extensiones_alumnos_pantalla')
                    <div class="ac-nav-group">Extensión Universitaria</div>
                    <a href="{{route('pantallas_alumnos.extensiones_universitarias', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_alumnos.extensiones_universitarias') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.3"/><path d="M10 6.3v3.9l2.6 1.6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Mi Extensión
                        <span class="ac-num">02</span>
                    </a>
                    @can('ver_catalogo_extensiones_alumnos_pantalla')
                        <a href="{{route('pantallas_alumnos.catalogo_extensiones_universitarias', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_alumnos.catalogo_extensiones_universitarias') ? 'active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Catálogo de Proyectos
                            <span class="ac-num">03</span>
                        </a>
                    @endcan
                @endcan
                @can('ver_noticias_avisos_alumnos_pantalla')
                    <div class="ac-nav-group">Noticias</div>
                    <a href="{{route('pantallas_alumnos.noticias', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_alumnos.noticias') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="12" rx="1" stroke="currentColor" stroke-width="1.2"/></svg>
                        Noticias y Avisos
                        <span class="ac-num">03</span>
                    </a>
                @endcan

            @elseif (Auth::user()->hasAnyRole(['DOCENTE', 'ENCARGADO_DOCENTE']))
                <div class="ac-nav-group">Inicio</div>
                <a href="{{route('pantallas_docentes.index', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_docentes.index') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="3" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
                    Panel
                    <span class="ac-num">01</span>
                </a>
                @can('ver_extensiones_docentes_pantalla')
                    <div class="ac-nav-group">Extensión Universitaria</div>
                    <a href="{{route('pantallas_docentes.extensiones_universitarias', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_docentes.extensiones_universitarias') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.3"/><path d="M10 6.3v3.9l2.6 1.6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Mis Extensiones
                        <span class="ac-num">02</span>
                    </a>
                    @can('crear_extensiones_docentes_pantalla')
                        <a href="{{route('pantallas_docentes.create_extensiones_universitarias', Auth::id())}}" class="ac-nav-item {{ request()->routeIs('pantallas_docentes.create_extensiones_universitarias') ? 'active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                            Nueva Extensión
                            <span class="ac-num">03</span>
                        </a>
                    @endcan
                @endcan

            @else
                <div class="ac-nav-group">Inicio</div>
                <a href="{{route('root')}}" class="ac-nav-item {{ request()->routeIs('root') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="3" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
                    Panel
                    <span class="ac-num">01</span>
                </a>
                @if (Auth::user()->can('ver_alumnos') || Auth::user()->can('ver_docentes'))
                    <div class="ac-nav-group">Gestión académica</div>
                    @can('ver_alumnos')
                        <a href="{{route('alumnos.index')}}" class="ac-nav-item {{ request()->routeIs('alumnos.*') ? 'active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M2 6.5L10 3l8 3.5-8 3.5-8-3.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                            Alumnos
                            <span class="ac-num">02</span>
                        </a>
                    @endcan
                    @can('ver_docentes')
                        <a href="{{route('docentes.index')}}" class="ac-nav-item {{ request()->routeIs('docentes.*') ? 'active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="6.5" r="2.6" stroke="currentColor" stroke-width="1.2"/><path d="M4.5 16c0-3 2.5-4.6 5.5-4.6s5.5 1.6 5.5 4.6" stroke="currentColor" stroke-width="1.2"/></svg>
                            Docentes
                            <span class="ac-num">03</span>
                        </a>
                    @endcan
                @endif
                @can('ver_extensiones_universitarias')
                    <div class="ac-nav-group">Extensión Universitaria</div>
                    <a href="{{route('extensiones_universitarias.index')}}" class="ac-nav-item {{ request()->routeIs('extensiones_universitarias.*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.3"/><path d="M10 6.3v3.9l2.6 1.6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Actividades
                        <span class="ac-num">04</span>
                    </a>
                    @can('ver_tipos_extensiones_universitarias')
                        <a href="{{route('tipos_extensiones_universitarias.index')}}" class="ac-nav-item {{ request()->routeIs('tipos_extensiones_universitarias.*') ? 'active' : '' }}" style="padding-left:22px;font-size:12px;">
                            Tipos de Actividad
                            <span class="ac-num">05</span>
                        </a>
                    @endcan
                    @can('ver_requerimientos_extensiones_universitarias')
                        <a href="{{route('requerimientos_extensiones_universitarias.show')}}" class="ac-nav-item {{ request()->routeIs('requerimientos_extensiones_universitarias.*') ? 'active' : '' }}" style="padding-left:22px;font-size:12px;">
                            Requerimientos
                            <span class="ac-num">06</span>
                        </a>
                    @endcan
                @endcan
                @can('ver_usuarios')
                    <div class="ac-nav-group">Administración</div>
                    <a href="{{route('usuarios.index')}}" class="ac-nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="6.5" r="2.6" stroke="currentColor" stroke-width="1.2"/><path d="M4.5 16c0-3 2.5-4.6 5.5-4.6s5.5 1.6 5.5 4.6" stroke="currentColor" stroke-width="1.2"/></svg>
                        Usuarios
                        <span class="ac-num">07</span>
                    </a>
                @endcan
            @endif
        </div>

        <div class="ac-content-col">
            <div class="ac-topbar">
                <div class="ac-topbar-row">
                    <span class="ac-title">@yield('title')</span>
                    <div style="flex:1;"></div>
                    <div class="ac-avatar">{{ Str::upper(Str::substr(Auth::user()->name ?? '?', 0, 2)) }}</div>
                    <span class="ac-user"><b>{{ Auth::user()->name }}</b> — {{ Auth::user()->roles->first()->name ?? '' }}</span>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('ac-logout-form').submit();" style="font-size:12px;margin-left:4px;">Salir</a>
                    <form id="ac-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
            <div class="ac-page">
                @yield('content')
            </div>
            <div class="ac-statusbar">
                <span>Conectado como {{ Auth::user()->name }}</span>
                <div style="flex:1;"></div>
                <span>{{ config('app.name') }}</span>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @include('layouts.theme-scripts')
    @yield('script')
</body>

</html>
