@extends('layouts.master')
@section('title') Mi Perfil @endsection
@section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @component('components.breadcrumb')
        @slot('li_1') Inicio @endslot
        @slot('title') Mi Perfil @endslot
    @endcomponent

    <div class="card">
        <div class="profile-foreground position-relative" style="height: 200px">
            <div class="profile-wid-bg position-static" style="height: 200px">
                <img src="{{asset('storage/portadas/' . $usuario->portada)}}" alt="" class="profile-wid-img card-img-top">
                <div>
                    <form id="update-portada-form">
                        <input type="text" name="tipo" value="portada" hidden>
                        <input type="file" accept="image/jpg, image/png" class="profile-foreground-img-file-input d-none" id="profile-foreground-img-file-input" name="portada" data-id="{{$usuario->id}}">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light btn-sm position-absolute end-0 top-0 m-3 z-1">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Editar Portada
                        </label>
                    </form>
                </div>
            </div>
            <div class="bg-overlay bg-primary bg-opacity-75 card-img-top"></div>
        </div>

        <div class="card-body mt-n5">
            <div class="position-relative mt-n3">
                <div class="avatar-lg position-relative">
                    <img src="{{asset('storage/usuarios/' . $usuario->avatar)}}" alt="user-img" class="img-thumbnail rounded-circle user-profile-image" style="z-index: 1;">
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit position-absolute end-0 bottom-0">
                        <form id="update-avatar-form">
                            <input type="text" name="tipo" value="avatar" hidden>
                            <input type="file" accept="image/jpg, image/png" class="profile-img-file-input d-none" id="profile-img-file-input" name="avatar" data-id="{{$usuario->id}}">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cambiar foto de perfil">
                                    <i class="bi bi-camera"></i>
                                </span>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="mt-3">
                    <h3 class="fs-xl mb-1">{{$usuario->name}}</h3>
                    <p class="fs-md text-muted mb-0">
                        @php
                            foreach ($usuario->roles as $rol) {
                                echo $rol->name;
                            }
                        @endphp
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4 pb-2">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Mi Información</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="ps-0 fs-md" scope="row">Nombre:</th>
                                            <td class="text-muted fs-sm">{{$usuario->name}}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0 fs-md" scope="row">Correo:</th>
                                            <td class="text-muted fs-sm">{{$usuario->email}}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0 fs-md" scope="row">Rol:</th>
                                            <td class="text-muted fs-sm">
                                                @php
                                                    foreach ($usuario->roles as $rol) {
                                                        echo $rol->name;
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0 fs-md" scope="row">Usuario desde:</th>
                                            <td class="text-muted fs-sm">{{\Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y H:i:s')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-custom-outline nav-info gap-2 flex-grow-1 mb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#changeInformation" role="tab" aria-selected="true" id="change-information">
                                Cambiar Información
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab" aria-selected="false" tabindex="-1" id="change-password">
                                Cambiar Contraseña
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#customizer" role="tab" aria-selected="false" tabindex="-1" id="change-password">
                                Diseño de Vista
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="tab-content">
                    <div class="tab-pane active" id="changeInformation" role="tabpanel">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Información Personal</h6>
                        </div>
                        <div class="card-body">
                            <form id="update-form">
                                @csrf
                                <input type="text" name="tipo" value="update" hidden>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label class="form-label" for="name">Nombre (*)</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Escriba su nombre" @if ($errors->any()) value="{{old('name')}}" @else value="{{$usuario->name}}" @endif>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <label class="form-label" for="email">Correo Electrónico (*)</label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Esciba su correo electrónico" @if ($errors->any()) value="{{old('email')}}" @else value="{{$usuario->email}}" @endif>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <button type="button" class="btn btn-success" id="update-info-btn" data-id="{{$usuario->id}}">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="changePassword" role="tabpanel">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Cambiar Contraseña</h6>
                        </div>
                        <div class="card-body">
                            <form id="change-password-form">
                                @csrf
                                <input type="text" name="tipo" value="change_password" hidden>
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <label for="current_password" class="form-label">Contraseña Actual (*)</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Escriba su contraseña actual">
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="new_password" class="form-label">Nueva Contraseña (*)</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Escriba su nueva contraseña">
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="new_password_confirmation" class="form-label">Confirmar Contraseña (*)</label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirme su nueva contraseña">
                                    </div>
                                    <!--end col-->
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <div class="">
                                            <button type="button" class="btn btn-success" id="update-password-btn" data-id="{{$usuario->id}}">Cambiar Contraseña</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="customizer" role="tabpanel">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Diseño de Vista</h6>
                        </div>
                        <div class="card-body">
                            <form id="customizer-form">
                                @csrf
                                <input type="text" id="tipo_customizer" name="tipo" value="customizer" hidden>
                                <div class="row">
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md mb-1">Disposiciones</h6>
                                        <p class="text-muted fs-sm">Elige tu disposición</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input id="customizer-layout01" name="data-layout" type="radio" value="vertical" class="form-check-input">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout01">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Vertical</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check card-radio">
                                                    <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal" class="form-check-input">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout02">
                                                        <span class="d-flex h-100 flex-column gap-1">
                                                            <span class="bg-light d-flex p-1 gap-1 align-items-center">
                                                                <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                                                            </span>
                                                            <span class="bg-light d-block p-1"></span>
                                                            <span class="bg-light d-block p-1 mt-auto"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Horizontal</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input id="customizer-layout03" name="data-layout" type="radio" value="twocolumn" class="form-check-input">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout03">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1">
                                                                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Dos Columnas</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Color del tema</h6>
                                        <p class="text-muted fs-sm">Elige entre claro u oscuro.</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-light" value="light">
                                                    <label class="form-check-label p-3 bg-white text-body text-center" for="layout-mode-light">
                                                        <i class="ti ti-sun align-middle fs-3xl"></i>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Claro</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-dark" value="dark">
                                                    <label class="form-check-label p-3 bg-dark text-white text-center" for="layout-mode-dark">
                                                        <i class="ti ti-moon align-middle fs-3xl"></i>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Oscuro</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Ancho de la disposición</h6>
                                        <p class="text-muted fs-sm">Elige entre el predeterminado o encajonado.</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-layout-width" id="layout-width-fluid" value="fluid">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="layout-width-fluid">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Predeterminado</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-layout-width" id="layout-width-boxed" value="boxed">
                                                    <label class="form-check-label p-0 avatar-md w-100 px-2" for="layout-width-boxed">
                                                        <span class="d-flex gap-1 h-100 border-start border-end">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Encajonado</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Posición de la disposición</h6>
                                        <p class="text-muted fs-sm">Elige entre fijo o desplazable.</p>
                                        <div class="justify-content-center d-flex me-3 text-center">
                                            <div class="col-xxl-12 col-lg-6 me-2">
                                                <div class="btn-group radio mt-4" role="group">
                                                    <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed" value="fixed">
                                                    <label class="btn btn-light w-sm" for="layout-position-fixed">Fijo</label>

                                                    <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-scrollable" value="scrollable">
                                                    <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Desplazable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Color de la barra superior</h6>
                                        <p class="text-muted fs-sm">Elige el color de la barra superior.</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-warning" value="warning">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-warning">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-warning-subtle d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Amarillo</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-danger" value="danger">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-danger">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-danger-subtle d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Rojo</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-success" value="success">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-success">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-success-subtle d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Verde</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Tamaño de la barra lateral</h6>
                                        <p class="text-muted fs-sm">Elige el tamaño de la barra lateral</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-default" value="lg">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-default">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Predeterminado</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-compact" value="md">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-compact">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Compacto</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-small-hover" value="sm-hover">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-small-hover">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1">
                                                                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Pequeño (íconos)</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Vista de la barra lateral</h6>
                                        <p class="text-muted fs-sm">Elige entre predeterminado o separado.</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-layout-style" id="sidebar-view-default" value="default">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-default">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Predeterminado</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-layout-style" id="sidebar-view-detached" value="detached">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-detached">
                                                        <span class="d-flex h-100 flex-column">
                                                            <span class="bg-light d-flex p-1 gap-1 align-items-center px-2">
                                                                <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                                                            </span>
                                                            <span class="d-flex gap-1 h-100 p-1 px-2">
                                                                <span class="flex-shrink-0">
                                                                    <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    </span>
                                                                </span>
                                                            </span>
                                                            <span class="bg-light d-block p-1 mt-auto px-2"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Separado</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body col-lg-5 me-2">
                                        <h6 class="fs-md">Color de la barra lateral</h6>
                                        <p class="text-muted fs-sm">Elige el color de la barra lateral.</p>
                                        <div class="justify-content-center d-flex align-items-center me-3">
                                            <div class="col-xxl-3 col-lg-6 me-2">
                                                <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse" data-bs-target="#collapseBgGradient.show">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-light" value="light">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-light">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-white border-end d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Claro</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse" data-bs-target="#collapseBgGradient.show">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-dark" value="dark">
                                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-dark">
                                                        <span class="d-flex gap-1 h-100">
                                                            <span class="flex-shrink-0">
                                                                <span class="bg-primary d-flex h-100 flex-column gap-1 p-1">
                                                                    <span class="d-block p-1 px-2 bg-light-subtle rounded mb-2"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                                    <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                                </span>
                                                            </span>
                                                            <span class="flex-grow-1">
                                                                <span class="d-flex h-100 flex-column">
                                                                    <span class="bg-light d-block p-1"></span>
                                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Oscuro</h5>
                                            </div>
                                            <div class="col-xxl-3 col-lg-5 me-2">
                                                <button class="btn btn-link avatar-md w-100 p-0 overflow-hidden border collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBgGradient" aria-expanded="false" aria-controls="collapseBgGradient">
                                                    <span class="d-flex gap-1 h-100">
                                                        <span class="flex-shrink-0">
                                                            <span class="bg-vertical-gradient d-flex h-100 flex-column gap-1 p-1">
                                                                <span class="d-block p-1 px-2 bg-light-subtle rounded mb-2"></span>
                                                                <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                                <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                                <span class="d-block p-1 px-2 pb-0 bg-light-subtle"></span>
                                                            </span>
                                                        </span>
                                                        <span class="flex-grow-1">
                                                            <span class="d-flex h-100 flex-column">
                                                                <span class="bg-light d-block p-1"></span>
                                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </button>
                                                <h5 class="fs-sm text-center fw-medium mt-2">Degradado</h5>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapseBgGradient">
                                            <div class="d-flex gap-2 flex-wrap img-switch p-2 px-3 bg-light rounded">
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-gradient" value="gradient">
                                                    <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient">
                                                        <span class="avatar-title rounded-circle bg-vertical-gradient"></span>
                                                    </label>
                                                </div>
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-gradient-2" value="gradient-2">
                                                    <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-2">
                                                        <span class="avatar-title rounded-circle bg-vertical-gradient-2"></span>
                                                    </label>
                                                </div>
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-gradient-3" value="gradient-3">
                                                    <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-3">
                                                        <span class="avatar-title rounded-circle bg-vertical-gradient-3"></span>
                                                    </label>
                                                </div>
                                                <div class="form-check sidebar-setting card-radio">
                                                    <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-gradient-4" value="gradient-4">
                                                    <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-4">
                                                        <span class="avatar-title rounded-circle bg-vertical-gradient-4"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <button type="button" class="btn btn-success" id="update-customizer-btn" data-id="{{$usuario->id}}">Actualizar</button>
                                        <button type="button" class="btn btn-danger" id="reset-customizer-btn" data-id="{{$usuario->id}}">Restablecer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end tab-pane-->
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @include('perfiles.scripts')
@endsection
