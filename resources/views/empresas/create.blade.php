@can('editar_empresa')
    @extends('layouts.master')
    @section('title') La Empresa @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') La Empresa  @endslot
        @endcomponent

        @include('empresas.scripts.messages-scripts')
        @include('empresas.modals.create-modals')

        <div class="row">
            <form action="{{route('empresas.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Cargar datos de la Empresa</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class=" col-lg-8 col-sm-9">
                                    <div class="row mb-3">
                                        <div class="col-lg-4">
                                            <label class="form-label" for="razon_social">Razón Social <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('razon_social') is-invalid @enderror" id="razon_social" name="razon_social" placeholder="Escriba la razón social de la empresa" value="{{old('razon_social')}}">
                                            @error('razon_social')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="nombre_fantasia">Nombre Fantasía <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('nombre_fantasia') is-invalid @enderror" id="nombre_fantasia" name="nombre_fantasia" placeholder="Escriba el nombre fantasía de la empresa" value="{{old('nombre_fantasia')}}">
                                            @error('nombre_fantasia')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="ruc">R.U.C. <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" placeholder="Escriba el N° de R.U.C de la empresa" value="{{old('ruc')}}">
                                            @error('ruc')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-8">
                                            <label class="form-label" for="direccion">Dirección <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" placeholder="Escriba la dirección de la empresa" value="{{old('direccion')}}">
                                            @error('direccion')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="ciudad">Ciudad</label>
                                            <div class="input-group">
                                                <select class="selectpicker form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($ciudades as $ciudad)
                                                        <option value="{{$ciudad->id}}" @if(old('ciudad') == strval($ciudad->id)) selected @endif>{{$ciudad->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#createCiudadModal"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                                @error('ciudad')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-4">
                                            <div class="d-flex flex-wrap gap-2">
                                                <label class="form-label" for="telefono">Teléfono <span class="text-danger">(*)</span></label>
                                                <div class="form-check form-check-info form-check-inline ms-3">
                                                    <input type="checkbox" class="form-check-input" id="celular" name="check_telefono" value="1" @if (old('check_telefono') == 1) checked @endif>
                                                    <label for="celular">Cel.</label>
                                                </div>
                                                <div class="form-check form-check-info form-check-inline">
                                                    <input type="checkbox" class="form-check-input" id="linea_baja" name="check_telefono" value="2" @if (old('check_telefono') == 2) checked @endif>
                                                    <label for="linea_baja">Fijo</label>
                                                </div>
                                                <input type="text" id="check_telefono" value="{{old('check_telefono')}}" hidden>
                                            </div>
                                            <input type="text" class="form-control @error('telefono_celular') is-invalid @enderror" id="telefono-celular" name="telefono_celular" placeholder="Escriba el N° de teléfono de la empresa" value="{{old('telefono')}}" disabled>
                                            <input type="text" class="form-control @error('telefono_linea_baja') is-invalid @enderror" id="telefono-linea-baja" name="telefono_linea_baja" placeholder="Escriba el N° de teléfono de la empresa" value="{{old('telefono')}}" hidden>
                                            @error('telefono_celular')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                            @error('telefono_linea_baja')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="email">Correo Electrónico <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Escriba el correo electrónico de le empresa" value="{{old('email')}}">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="pais">País</label>
                                            <div class="input-group">
                                                <select class="selectpicker form-control @error('pais') is-invalid @enderror" id="pais" name="pais" data-live-search="true">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($paises as $pais)
                                                        <option value="{{$pais->id}}" @if(old('pais') == strval($pais->id)) selected @endif>{{$pais->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#createPaisModal"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                                @error('pais')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="form-label" for="actividad">Actividad <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('actividad') is-invalid @enderror" id="actividad" name="actividad" placeholder="Escriba la actividad de la empresa" value="{{old('actividad')}}">
                                            @error('actividad')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">
                                    <div class="row mb-3">
                                        <label class="form-label" for="imagen">Logo (JPG o PNG)</label>
                                        <div class="col-lg-12" id="vista-imagen-div">
                                            <img id="vista-imagen" class="hidden img-fluid img-circle"
                                                    src="{{asset('storage/empresa/no_image.png')}}"
                                                    alt="Logo"
                                                    style="width: 154px; height: 154px">

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-group custom-file-button">
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg, image/png">
                                                <div class="input-group-append">
                                                    <abbr title="Eliminar logo">
                                                        <button type="button" class="btn btn-danger" id="delete-logo"><i class="ri-delete-bin-fill"></i></button>
                                                    </abbr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('empresas.scripts.create-scripts')
    @endsection
@endcan
