@can('editar_mallas')
    @extends('layouts.master')
    @section('title') Editar Malla @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas @endslot
            @slot('title') Editar Malla  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('mallas.update', $malla->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar malla</h4>
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
                                            <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id) || $malla->carrera_id == strval($carrera->id)) selected @endif data-subtext="{{$carrera->nombre_real}} - {{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
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
                                            <option value="{{$tipo_malla->id}}" @if (old('tipo_malla') == strval($tipo_malla->id) || $malla->tipo_malla_id == strval ($tipo_malla->id)) selected @endif>{{$tipo_malla->nombre}}</option>
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
                                        <input class="form-control text-center" type="text" id="cantidad_materias" value="{{number_format($malla->mallaDetalles->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="carga_horaria_total">Carga Horaria Total</label>
                                        <input class="form-control text-center" type="text" id="carga_horaria_total" value="{{number_format($malla->mallaDetalles->sum('carga_horaria'), 0, ',', '.')}}" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_creditos_total">Cant. Créditos Total</label>
                                        <input class="form-control text-center" type="text" id="cantidad_creditos_total" value="{{number_format($malla->mallaDetalles->sum('cantidad_creditos'), 0, ',', '.')}}" readonly>
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
                                    @foreach ($malla->mallaDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row">
                                                <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-materia-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control materia-{{$key}} materia @error('detalles.'. $key . '.materia') is-invalid @enderror" id="materia-{{$key}}" name="detalles[{{$key}}][materia]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias as $materia)
                                                            <option value="{{$materia->id}}" @if (old('detalles.{{$key}}.materia') == strval($materia->id) || $detalle->materia_id == strval($materia->id)) selected @endif data-subtext="{{$materia->nombre_real}}">{{$materia->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.materia')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-semestre-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-semestre">Semestre <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center semestre-{{$key}} semestre @error('detalles.'. $key . '.semestre') is-invalid @enderror" id="detalles[{{$key}}][semestre]" name="detalles[{{$key}}][semestre]" value="{{old('detalles.' . $key . '.semestre', $detalle->semestre)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.semestre')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-carga_horaria-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-carga_horaria">Horas <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center carga_horaria-{{$key}} carga_horaria @error('detalles.'. $key . '.carga_horaria') is-invalid @enderror" id="detalles[{{$key}}][carga_horaria]" name="detalles[{{$key}}][carga_horaria]" value="{{old('detalles.' . $key . '.carga_horaria', $detalle->carga_horaria)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.carga_horaria')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-cantidad_creditos-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-cantidad_creditos">Créditos <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center cantidad_creditos-{{$key}} cantidad_creditos @error('detalles.'. $key . '.cantidad_creditos') is-invalid @enderror" id="detalles[{{$key}}][cantidad_creditos]" name="detalles[{{$key}}][cantidad_creditos]" value="{{old('detalles.' . $key . '.cantidad_creditos', $detalle->cantidad_creditos)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.cantidad_creditos')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center" id="div-area_curricular-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-area_curricular">Área <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control area_curricular-{{$key}} area_curricular @error('detalles.' . $key . '.area_curricular') is-invalid @enderror" id="area_curricular-{{$key}}" name="detalles[{{$key}}][area_curricular]" data-live-search="true" data-live-search-normalize="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        <option value="B" @if (old('detalles.' . $key . '.area_curricular') == 'B' || $detalle->area_curricular == 'B') selected @endif>BÁSICO</option>
                                                        <option value="C" @if (old('detalles.' . $key . '.area_curricular') == 'C' || $detalle->area_curricular == 'C') selected @endif>COMPLEMENTARIO</option>
                                                        <option value="P" @if (old('detalles.' . $key . '.area_curricular') == 'P' || $detalle->area_curricular == 'P') selected @endif>PROFESIONAL</option>
                                                    </select>
                                                    @error('detalles.' . $key . '.area_curricular')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-doble_grado-{{$key}}">
                                                    @if ($key == 0)
                                                        <div class="div-label-doble_grado-{{$key}}">
                                                            <label class="form-label label-doble_grado">Doble Grado ? <span class="text-danger">(*)</span></label>
                                                        </div>
                                                    @endif
                                                    <div class="btn-group @error('detalles.'. $key . '.doble_grado') is-invalid @enderror" role="group">
                                                        <input type="radio" class="btn-check doble_grado1-{{$key}}" id="detalles[{{$key}}][doble_grado1]" name="detalles[{{$key}}][doble_grado]" value="false" @if (old('detalles.' . $key . '.doble_grado') == 'false' || $detalle->doble_grado == false) checked @endif data-id="{{$key}}">
                                                        <label class="btn btn-outline-danger" for="detalles[{{$key}}][doble_grado1]">No</label>
                                                        <input type="radio" class="btn-check doble_grado2-{{$key}}" id="detalles[{{$key}}][doble_grado2]" name="detalles[{{$key}}][doble_grado]" value="true" @if (old('detalles.' . $key . '.doble_grado') == 'true' || $detalle->doble_grado == true) checked @endif data-id="{{$key}}">
                                                        <label class="btn btn-outline-success" for="detalles[{{$key}}][doble_grado2]">Sí</label>
                                                    </div>
                                                    <input type="hidden" class="doble_grado-{{$key}}" @if ($errors->any()) value="{{old('detalles.' . $key . '.doble_grado')}}" @elseif ($detalle->doble_grado == false) value="false" @elseif ($detalle->doble_grado == true) value="true" @endif>
                                                    @error('detalles.'. $key . '.doble_grado')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 col-sm-2 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                    <div class="align-middle" id="acciones-{{$key}}">
                                                        @if ($key == 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                        @elseif ($key > 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                        @elseif ($key > 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                        @elseif ($key == 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('mallas.scripts.edit-scripts')
        @include('mallas.scripts.edit-detalles-scripts')
    @endsection
@endcan
