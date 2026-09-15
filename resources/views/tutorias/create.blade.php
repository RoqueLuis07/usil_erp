@can('crear_tutorias')
    @extends('layouts.master')
    @section('title') Agregar Tutoría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tutorías @endslot
            @slot('title') Agregar Tutoría  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('tutorias.store')}}" method="post" id="store-form">
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
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('materia') is-invalid @enderror" id="materia" name="materia" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($materias as $materia)
                                            <option value="{{$materia->id}}" @if (old('materia') == strval($materia->id)) selected @endif>{{$materia->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('materia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('docente')
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
                                            <option value="{{$modalidad->id}}" @if (old('modalidad') == strval($modalidad->id)) selected @endif>{{$modalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('modalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 mb-3 me-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="hidden" class="form-control" name="semestre" value="{{$semestre->id}}">
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$semestre->nombre}}">
                                    </div>
                                    <div class="col-lg-3 mb-3 text-center">
                                        <label class="form-label" for="cantidad_horas">Cant. Horas</label>
                                        <input type="text" class="form-control text-center @error('cantidad_horas') is-invalid @enderror" id="cantidad_horas" name="cantidad_horas" value="{{old('cantidad_horas', 0)}}" readonly>
                                        @error('cantidad_horas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha Inicio <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_inicio"></i>
                                        @error('fecha_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha Fin <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon-fecha_fin"></i>
                                        @error('fecha_fin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_clases">Cant. Clases</label>
                                    <input type="text" class="form-control text-center @error('cantidad_clases') is-invalid @enderror" id="cantidad_clases" name="cantidad_clases" value="{{old('cantidad_clases')}}">
                                    @error('cantidad_clases')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Horario de la Tutoría</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-dia-0">
                                                <label class="form-label label-dia">Día <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control dia-0 dia @error('detalles.0.dia') is-invalid @enderror" id="dia-0" name="detalles[0][dia]" data-live-search="true" data-live-search-normalize="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($dias as $dia)
                                                        <option value="{{$dia->id}}" @if (old('detalles.0.dia') == strval($dia->id)) selected @endif>{{$dia->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.dia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-hora_inicio-0">
                                                <label class="form-label label-hora_inicio">Hora Inicio<span class="text-danger">(*)</span></label>
                                                <input type="text" class="timepickr form-control text-center hora_inicio-0 hora_inicio @error('detalles.0.hora_inicio') is-invalid @enderror" id="detalles[0][hora_inicio]" name="detalles[0][hora_inicio]" value="{{old('detalles.0.hora_inicio')}}" data-id="0">
                                                @error('detalles.0.hora_inicio')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-hora_fin-0">
                                                <label class="form-label label-hora_fin">Hora Fin<span class="text-danger">(*)</span></label>
                                                <input type="text" class="timepickr form-control text-center hora_fin-0 hora_fin @error('detalles.0.hora_fin') is-invalid @enderror" id="detalles[0][hora_fin]" name="detalles[0][hora_fin]" value="{{old('detalles.0.hora_fin')}}" data-id="0">
                                                @error('detalles.0.hora_fin')
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
                                    <div id="dia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        @include('tutorias.scripts.create-scripts')
        @include('tutorias.scripts.create-detalles-scripts')
    @endsection
@endcan
