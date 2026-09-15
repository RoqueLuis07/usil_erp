@can('editar_alumnos_tutorias')
    @extends('layouts.master')
    @section('title') Editar Alumnos de la Tutoría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ver Tutoría @endslot
            @slot('title') Editar Alumnos de la Tutoría @endslot
        @endcomponent

        @include('tutorias.clases.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('tutorias.update_alumnos', $tutoria->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">@if ($tutoria->alumnos->count() != 0) Actualizar @else Agregar @endif Alumnos</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$tutoria->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" @if ($tutoria->docente_id) value="{{$tutoria->docente->primer_nombre}} {{$tutoria->docente->primer_apellido}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="modalidad">Modalidad</label>
                                    <input type="text" class="form-control" id="modalidad" value="{{$tutoria->modalidad->nombre}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 mb-3 me-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$tutoria->semestre->nombre}}" readonly>
                                    </div>
                                    <div class="col-lg-3 mb-3 text-center">
                                        <label class="form-label" for="cantidad_horas">Cant. Horas</label>
                                        <input type="text" class="form-control text-center" id="cantidad_horas" value="{{$tutoria->cantidad_horas}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha Inicio</label>
                                    <input type="text" class="form-control text-center" id="fecha_inicio" @if ($tutoria->fecha_inicio) value="{{\Carbon\Carbon::parse($tutoria->fecha_inicio)->format('d/m/Y')}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha Fin</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin" @if ($tutoria->fecha_fin) value="{{\Carbon\Carbon::parse($tutoria->fecha_fin)->format('d/m/Y')}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Cant. Clases</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin" value="{{$tutoria->cantidad_clases}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Alumnos de la Tutoría</h4>
                                </div>
                                <div class="card-body">
                                    @forelse ($tutoria->alumnos as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flez flex-wrap justify-content-center">
                                                <div class="col-lg-4 mb-2 text-center" id="div-alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control alumno-{{$key}} alumno @error('detalles.'. $key . '.alumno') is-invalid @enderror" id="alumno-{{$key}}" name="detalles[{{$key}}][alumno]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($alumnos as $alumno)
                                                            <option value="{{$alumno->id}}" @if (old('detalles.{{$key}}.alumno') == strval($alumno->id) || $detalle->alumno_id == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documenot}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.alumno')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-3 mb-2 me-3 text-center" id="div-carrera-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-carrera">Carrera <span class="text-danger">(*)</span></label> @endif
                                                    <input type="hidden" name="detalles[{{$key}}][carrera]" value="{{$detalle->carrera_id}}">
                                                    <input type="text" class="form-control text-center carrera_nombre-{{$key}} carrera_nombre @error('detalles.'. $key . '.carrera_nombre') is-invalid @enderror" id="detalles[{{$key}}][carrera_nombre]" name="detalles[{{$key}}][carrera_nombre]" value="{{old('detalles.' . $key . '.carrera', $detalle->carrera->nombre_fantasia)}}" data-id="{{$key}}" readonly>
                                                    @error('detalles.'. $key . '.carrera_nombre')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 text-center">
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
                                    @empty
                                        <div class="mb-2 fila" id="fila-0">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 mb-2 text-center" id="div-alumno-0">
                                                    <label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control alumno-0 alumno @error('detalles.0.alumno') is-invalid @enderror" id="alumno-0" name="detalles[0][alumno]" data-live-search="true" data-id="0">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($alumnos as $alumno)
                                                            <option value="{{$alumno->id}}" @if (old('detalles.0.alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.0.alumno')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-3 mb-2 me-3 text-center" id="div-carrera-0">
                                                    <label class="form-label label-carrera">Carrera <span class="text-danger">(*)</span></label>
                                                    <input type="hidden" class="carrera carrera-0" name="detalles[0][carrera]" value="{{old('detalles.0.carrera')}}">
                                                    <input type="text" class="form-control text-center carrera_nombre-0 carrera_nombre @error('detalles.0.carrera_nombre') is-invalid @enderror" id="detalles[0][carrera_nombre]" id="detalles[0][carrera_nombre]" value="{{old('detalles.0.carrera_nombre')}}" data-id="0" readonly>
                                                    @error('detalles.0.carrera_nombre')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 text-center">
                                                    <label class="form-label label-acciones">Acciones</label>
                                                    <div class="align-middle" id="acciones-0">
                                                        <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>
                                                        <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                    <div id="alumno-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        @include('tutorias.scripts.edit_alumnos-scripts')
        @include('tutorias.scripts.edit_alumnos-detalles-scripts')
    @endsection
@endcan
