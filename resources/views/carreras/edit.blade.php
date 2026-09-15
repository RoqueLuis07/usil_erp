@can('crear_carreras')
    @extends('layouts.master')
    @section('title') Editar Carreras @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Carreras @endslot
            @slot('title') Editar Carreras  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('carreras.update', $carrera->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar carrera</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code>para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre_fantasia">Nombre Fantasía <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_fantasia') is-invalid @enderror" id="nombre_fantasia" name="nombre_fantasia" placeholder="Escriba un nombre de fantasía" value="{{old('nombre_fantasia', $carrera->nombre_fantasia)}}">
                                    @error('nombre_fantasia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre_real">Nombre Real <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_real') is-invalid @enderror" id="nombre_real" name="nombre_real" placeholder="Escriba un nombre real" value="{{old('nombre_real', $carrera->nombre_real)}}">
                                    @error('nombre_real')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="abreviatura">Abreviatura <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('abreviatura') is-invalid @enderror" id="abreviatura" name="abreviatura" placeholder="Escriba una abreviatura" value="{{old('abreviatura', $carrera->abreviatura)}}">
                                    @error('abreviatura')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa" name="programa" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{$programa->id}}" @if(old('programa') == strval($programa->id) || $carrera->programa_id == $programa->id) selected @endif>{{$programa->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="facultad">Facultad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('facultad') is-invalid @enderror" id="facultad" name="facultad" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($facultades as $facultad)
                                            <option value="{{$facultad->id}}" @if(old('facultad') == strval($facultad->id) || $carrera->facultad_id == $facultad->id) selected @endif>{{$facultad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('facultad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tipo_carrera">Tipo de Carrera <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_carrera') is-invalid @enderror" id="tipo_carrera" name="tipo_carrera" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_carreras as $tipo_carrera)
                                            <option value="{{$tipo_carrera->id}}" @if(old('tipo_carrera') == strval($tipo_carrera->id) || $carrera->tipo_carrera_id == $tipo_carrera->id) selected @endif>{{$tipo_carrera->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="modalidad">Modalidad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($modalidades as $modalidad)
                                            <option value="{{$modalidad->id}}" @if(old('modalidad') == strval($modalidad->id) || $carrera->modalidad_id == $modalidad->id) selected @endif>{{$modalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('modalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_semestres">Cantidad de Semestres <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('cantidad_semestres') is-invalid @enderror" id="cantidad_semestres" name="cantidad_semestres" placeholder="Escriba una cantidad de semestres" value="{{old('cantidad_semestres', $carrera->cantidad_semestres)}}">
                                    @error('cantidad_semestres')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="doble_grado">Doble Grado <span class="text-danger">(*)</span></label>
                                    <div class="text-center">
                                        <div class="btn-group @error('doble_grado') is-invalid @enderror" role="group">
                                            <input type="radio" class="btn-check" id="doble_grado1" name="doble_grado" value="false" @if (old('doble_grado', $carrera->doble_grado) == false) checked @endif>
                                            <label class="btn btn-outline-danger" for="doble_grado1">No</label>
                                            <input type="radio" class="btn-check" id="doble_grado2" value="true" name="doble_grado" @if (old('doble_grado', $carrera->doble_grado) == true) checked @endif>
                                            <label class="btn btn-outline-success" for="doble_grado2">Sí</label>
                                        </div>
                                        @error('doble_grado')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_ley">N° de Ley <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_ley') is-invalid @enderror" id="numero_ley" name="numero_ley" value="{{old('numero_ley', $carrera->numero_ley)}}">
                                    @error('numero_ley')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_acta">N° de Acta <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_acta') is-invalid @enderror" id="numero_acta" name="numero_acta" value="{{old('numero_acta', $carrera->numero_acta)}}">
                                    @error('numero_acta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_resolucion_cones">N° de Res. del CONES <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_resolucion_cones') is-invalid @enderror" id="numero_resolucion_cones" name="numero_resolucion_cones" value="{{old('numero_resolucion_cones', $carrera->numero_resolucion_cones)}}">
                                    @error('numero_resolucion_cones')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('carreras.scripts.edit-scripts')
    @endsection
@endcan
