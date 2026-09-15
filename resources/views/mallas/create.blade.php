@can('crear_mallas')
    @extends('layouts.master')
    @section('title') Agregar Malla @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas @endslot
            @slot('title') Agregar Malla  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('mallas.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva malla</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id)) selected @endif data-subtext="{{$carrera->nombre_real}} - {{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tipo_malla">Tipo de Malla <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_malla') is-invalid @enderror" id="tipo_malla" name="tipo_malla">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_mallas as $tipo_malla)
                                            <option value="{{$tipo_malla->id}}" @if (old('tipo_malla') == strval($tipo_malla->id)) selected @endif>{{$tipo_malla->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_malla')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_materias">Cant. de Materias</label>
                                        <input class="form-control text-center" type="text" id="cantidad_materias" value="0" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="carga_horaria_total">Carga Horaria Total</label>
                                        <input class="form-control text-center" type="text" id="carga_horaria_total" value="0" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_creditos_total">Cant. Créditos Total</label>
                                        <input class="form-control text-center" type="text" id="cantidad_creditos_total" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias de la Malla</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-materia-0">
                                                <label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control materia-0 materia @error('detalles.0.materia') is-invalid @enderror" id="materia-0" name="detalles[0][materia]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($materias as $materia)
                                                        <option value="{{$materia->id}}" @if (old('detalles.0.materia') == strval($materia->id)) selected @endif data-subtext="{{$materia->nombre_real}}">{{$materia->nombre_fantasia}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.materia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-semestre-0">
                                                <label class="form-label label-semestre">Semestre <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center semestre-0 semestre @error('detalles.0.semestre') is-invalid @enderror" id="detalles[0][semestre]" name="detalles[0][semestre]" value="{{old('detalles.0.semestre')}}" data-id="0">
                                                @error('detalles.0.semestre')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-carga_horaria-0">
                                                <label class="form-label label-carga_horaria">Horas <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center carga_horaria-0 carga_horaria @error('detalles.0.carga_horaria') is-invalid @enderror" id="detalles[0][carga_horaria]" name="detalles[0][carga_horaria]" value="{{old('detalles.0.carga_horaria')}}" data-id="0">
                                                @error('detalles.0.carga_horaria')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-cantidad_creditos-0">
                                                <label class="form-label label-cantidad_creditos">Créditos <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center cantidad_creditos-0 cantidad_creditos @error('detalles.0.cantidad_creditos') is-invalid @enderror" id="detalles[0][cantidad_creditos]" name="detalles[0][cantidad_creditos]" value="{{old('detalles.0.cantidad_creditos')}}" data-id="0">
                                                @error('detalles.0.cantidad_creditos')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 text-center" id="div-area_curricular-0">
                                                <label class="form-label label-area_curricular">Área <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control area_curricular-0 area_curricular @error('detalles.0.area_curricular') is-invalid @enderror" id="area_curricular-0" name="detalles[0][area_curricular]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    <option value="B" @if (old('detalles.0.area_curricular') == 'B') selected @endif>BÁSICO</option>
                                                    <option value="C" @if (old('detalles.0.area_curricular') == 'C') selected @endif>COMPLEMENTARIO</option>
                                                    <option value="P" @if (old('detalles.0.area_curricular') == 'P') selected @endif>PROFESIONAL</option>
                                                    <option value="O" @if (old('detalles.0.area_curricular') == 'O') selected @endif>OPTATIVO</option>
                                                </select>
                                                @error('detalles.0.area_curricular')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-doble_grado-0">
                                                <div class="div-label-doble_grado-0">
                                                    <label class="form-label label-doble_grado">Doble Grado ? <span class="text-danger">(*)</span></label>
                                                </div>
                                                <div class="btn-group @error('detalles.0.doble_grado') is-invalid @enderror" role="group">
                                                    <input type="radio" class="btn-check doble_grado1-0" id="detalles[0][doble_grado1]" name="detalles[0][doble_grado]" value="false" @if (old('detalles.0.doble_grado') == 'false') checked @endif data-id="0">
                                                    <label class="btn btn-outline-danger" for="detalles[0][doble_grado1]">No</label>
                                                    <input type="radio" class="btn-check doble_grado2-0" id="detalles[0][doble_grado2]" name="detalles[0][doble_grado]" value="true" @if (old('detalles.0.doble_grado') == 'true') checked @endif data-id="0">
                                                    <label class="btn btn-outline-success" for="detalles[0][doble_grado2]">Sí</label>
                                                </div>
                                                <input type="hidden" class="doble_grado-0" value="{{old('detalles.0.doble_grado')}}">
                                                @error('detalles.0.doble_grado')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-2 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="materia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('mallas.scripts.create-scripts')
        @include('mallas.scripts.create-detalles-scripts')
    @endsection
@endcan
