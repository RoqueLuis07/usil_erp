@can('editar_inscripciones_matriculaciones')
    @extends('layouts.master')
    @section('title') Editar Inscripción @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Matriculaciones @endslot
            @slot('title') Editar Inscripción  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('inscripciones.update', $matriculacion->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar inscripción</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control" id="fecha" value="{{\Carbon\Carbon::parse($matriculacion->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$matriculacion->alumno->primer_nombre}} {{$matriculacion->alumno->primer_apellido}} - {{$matriculacion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="semestre">Semestre</label>
                                    <input type="text" class="form-control" id="semestre" value="{{$matriculacion->semestre->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$matriculacion->programa->nombre}}" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="carrera">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" value="{{$matriculacion->carrera->nombre_fantasia}}" readonly>
                                    </div>
                                    @if ($matriculacion->programa_id == 1)
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="carrera_siu">Carrera SIU</label>
                                            <input type="text" class="form-control" id="carrera_siu" @if ($matriculacion->carrera_siu_id) value="{{$matriculacion->carreraSiu->nombre_fantasia}}" @endif readonly>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias Inscriptas</h4>
                                </div>
                                <div class="card-body">
                                    @forelse ($matriculacion->inscripciones as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-6 col-sm-12 mb-2 text-center" id="div-materia-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia" for="materia-{{$key}}">Materia <span class="text-danger">(*)</span></label> @endif
                                                    @if ($detalle->estado == 'MA')
                                                        <select class="selectpicker form-control materia-{{$key}} materia @error('detalles.' . $key . '.materia') is-invalid @enderror" id="materia-{{$key}}" name="detalles[{{$key}}][materia]" data-live-search="true" data-id="{{$key}}">
                                                            <option value="" selected disabled>Seleccionar...</option>
                                                            @foreach ($materias as $materia_smm)
                                                                <option 
                                                                    value="{{$materia_smm->materia->id}}" 
                                                                    @if (
                                                                        (isset($detalle->materia_id) && $detalle->materia_id == $materia_smm->materia->id) ||
                                                                        (isset($detalle->is_suggested) && $detalle->materia_id == $materia_smm->materia->id)
                                                                    ) 
                                                                        selected 
                                                                    @endif
                                                                    @if ($materia_smm->tiene_conflicto)
                                                                        data-content='<div>{{ $materia_smm->materia->nombre_fantasia }} <span class="badge badge-danger" style="background: red; color: white;">Solapado: {{ $materia_smm->conflicto }}</span></div>'
                                                                    @else
                                                                        data-subtext="Semestre {{$materia_smm->semestre_materia}}"
                                                                    @endif
                                                                >
                                                                    {{$materia_smm->materia->nombre_fantasia}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('detalles.' . $key . '.materia')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    @else
                                                        <input type="text" class="form-control" id="materia-{{$key}}" value="{{$detalle->materia->nombre_fantasia}} ({{$detalle->estado}})" readonly>
                                                        <input type="hidden" name="detalles[{{$key}}][materia]" value="{{$detalle->materia->id}}">
                                                    @endif
                                                </div>
                                                <div class="col-lg-1 col-sm-2 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones" >Acciones</label> @endif
                                                    @if ($detalle->estado == 'MA')
                                                        <div class="align-middle" id="acciones-{{$key}}">
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}">
                                                                <i class="ri-subtract-fill"></i>
                                                            </button>
                                                            @if ($loop->last)
                                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}">
                                                                    <i class="ri-add-fill"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="mb-2 fila" id="fila-0">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-6 col-sm-12 mb-2 text-center">
                                                    <label class="form-label label-materia" for="detalles[0][materia]">Materia <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control" id="materia-0" name="detalles[0][materia]" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias as $smm)
                                                            <option 
                                                                value="{{ $smm->materia->id }}"
                                                                @if ($smm->tiene_conflicto)
                                                                    data-content='<div>{{ $smm->materia->nombre_fantasia }} <span class="badge badge-danger" style="background: red; color: white;">Solapado: {{ $smm->conflicto }}</span></div>'
                                                                @else
                                                                    data-subtext="Semestre {{$smm->semestre_materia}}"
                                                                @endif
                                                            >
                                                                {{ $smm->materia->nombre_fantasia }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-1 col-sm-2 text-center">
                                                    <label class="form-label label-acciones">Acciones</label>
                                                    <div class="align-middle" id="acciones-0">
                                                        <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse

                                    <div id="materia-fila">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <a type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('inscripciones.show', $matriculacion->id)}}">Cancelar</a>
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
        {{-- Estos scripts deben contener la lógica de inicialización del contador de filas --}}
        @include('matriculaciones.inscripciones.scripts.edit-scripts')
        @include('matriculaciones.inscripciones.scripts.edit-detalles-scripts')
    @endsection
@endcan