<!doctype html>
<html
lang="@if(Auth::check()){{$configuracion->lang}}@else{{$configuracion['lang']}}@endif"
data-layout="@if(Auth::check()){{$configuracion->data_layout}}@else{{$configuracion['data_layout']}}@endif"
data-layout-style="@if(Auth::check()){{$configuracion->data_layout_style}}@else{{$configuracion['data_layout_style']}}@endif"
data-layout-width="@if(Auth::check()){{$configuracion->data_layout_width}}@else{{$configuracion['data_layout_width']}}@endif"
data-layout-position="@if(Auth::check()){{$configuracion->data_layout_position}}@else{{$configuracion['data_layout_position']}}@endif"
data-topbar="@if(Auth::check()){{$configuracion->data_topbar}}@else{{$configuracion['data_topbar']}}@endif"
data-sidebar="@if(Auth::check()){{$configuracion->data_sidebar}}@else{{$configuracion['data_sidebar']}}@endif"
data-sidebar-size="@if(Auth::check()){{$configuracion->data_sidebar_size}}@else{{$configuracion['data_sidebar_size']}}@endif"
data-sidebar-image="@if(Auth::check()){{$configuracion->data_sidebar_image}}@else{{$configuracion['data_sidebar_image']}}@endif"
card-layout="@if(Auth::check()){{$configuracion->card_layout}}@else{{$configuracion['card_layout']}}@endif"
data-bs-theme="@if(Auth::check()){{$configuracion->data_bs_theme}}@else{{$configuracion['data_bs_theme']}}@endif"
data-preloader="@if(Auth::check()){{$configuracion->data_preloader}}@else{{$configuracion['data_preloader']}}@endif"
>

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{config('app.name')}} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
    @include('layouts.head-css')
</head>

{{-- @section('body') --}}

<body>
    {{-- @show --}}
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    {{-- @include('layouts.customizer') --}}
    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

</html>
