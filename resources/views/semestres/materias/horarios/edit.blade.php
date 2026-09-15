@can('editar_horarios_materias_periodos')
    @extends('layouts.master')
    @section('title') @if ($semestre_malla_materia->semestreMalla->semestre->estado == 'IN') Ver @else Editar @endif Horario @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <style>
            .flatpickr-calendar {
                transform: translateX(-20%) !important;
                margin-top: 0.5em;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') {{$semestre_malla_materia->materia->nombre_fantasia}} - <small class="text-muted">{{$semestre_malla->malla->tipoMalla->nombre}}</small>@endslot
            @slot('title') @if ($semestre_malla_materia->semestreMalla->semestre->estado == 'IN') Ver @else Editar @endif Horario  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('semestres_mallas_materias_horarios.update', $semestre_malla_materia->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">@if ($semestre_malla_materia->semestreMalla->semestre->estado == 'IN') Visualizar @else Actualizar @endif Horario</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$semestre_malla_materia->materia->nombre_fantasia}} - {{$semestre_malla_materia->materia->nombre_real}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_dias">Cant. de Días</label>
                                        <input class="form-control text-center" type="text" id="cantidad_dias" value="{{number_format($semestre_malla_materia->SemestreMallaMateriaHorarios->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Horarios de la Materia</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($semestre_malla_materia->SemestreMallaMateriaHorarios as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row">
                                                <div class="d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-dia-{{$key}}">
                                                        @if ($key == 0) <label class="form-label label-dia">Día de la Semana</label> @endif
                                                        @if ($detalle->semestreMallaMateria->semestreMalla->semestre->estado == 'IN')
                                                            <input type="text" class="form-control" id="dia-{{$key}}" @if ($detalle->dia_semana_id) value="{{$detalle->diaSemana->nombre}}" @endif readonly>
                                                        @else
                                                            <select class="selectpicker form-control dia-{{$key}} dia @error('detalles.'. $key . '.dia') is-invalid @enderror" id="dia-{{$key}}" name="detalles[{{$key}}][dia]" data-live-search="false" data-id="{{$key}}">
                                                                <option value="" selected disabled>Seleccionar...</option>
                                                                @foreach ($dias_semana as $dia)
                                                                    <option value="{{$dia->id}}" @if (old('detalles.{{$key}}.dia') == strval($dia->id) || $detalle->dia_semana_id == strval($dia->id)) selected @endif>{{$dia->nombre}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('detalles.'. $key . '.dia')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{$message}}</strong>
                                                                </span>
                                                            @enderror
                                                        @endif
                                                    </div>
                                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-hora_inicio-{{$key}}">
                                                        @if ($key == 0) <label class="form-label label-hora_inicio">Hora de Inicio @if ($semestre_malla_materia->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label> @endif
                                                        <input type="text" class="timepickr form-control text-center hora_inicio-{{$key}} hora_inicio @error('detalles.'. $key . '.hora_inicio') is-invalid @enderror" id="detalles[{{$key}}][hora_inicio]" name="detalles[{{$key}}][hora_inicio]" value="{{old('detalles.' . $key . '.hora_inicio', $detalle->hora_inicio)}}" data-id="{{$key}}" @if ($detalle->semestreMallaMateria->semestreMalla->semestre->estado != 'IN') readonly @endif>
                                                        @error('detalles.'. $key . '.hora_inicio')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-hora_fin-{{$key}}">
                                                        @if ($key == 0) <label class="form-label label-hora_fin">Hora de Fin @if ($semestre_malla_materia->semestreMalla->semestre->estado != 'IN') <span class="text-danger">(*)</span> @endif</label> @endif
                                                        <input type="text" class="timepickr form-control text-center hora_fin-{{$key}} hora_fin @error('detalles.'. $key . '.hora_fin') is-invalid @enderror" id="detalles[{{$key}}][hora_fin]" name="detalles[{{$key}}][hora_fin]" value="{{old('detalles.' . $key . '.hora_fin', $detalle->hora_fin)}}" data-id="{{$key}}" @if ($detalle->semestreMallaMateria->semestreMalla->semestre->estado != 'IN') readonly @endif>
                                                        @error('detalles.'. $key . '.hora_fin')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    @if ($detalle->semestreMallaMateria->semestreMalla->semestre->estado != 'IN')
                                                        <div class="col-lg-1 col-sm-2 text-center">
                                                            @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                            <div class="align-middle" id="acciones-{{$key}}">
                                                                @if ($key == 0 && !$loop->last)
                                                                    <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                                @elseif ($key > 0 && !$loop->last)
                                                                    <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                                @elseif ($key > 0 && $loop->last)
                                                                    <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                                @elseif ($key == 0 && $loop->last)
                                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="dia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            @if ($detalle->semestreMallaMateria->semestreMalla->semestre->estado == 'IN')
                                <a type="button" class="btn btn-danger me-2" href="{{route('semestres_mallas_materias.show', $semestre_malla_materia->semestre_malla_id)}}">Volver</a>
                            @else
                                <button type="button" class="btn btn-danger me-2" id="cancel-btn" data-url="{{route('semestres_mallas_materias.show', $semestre_malla_materia->semestre_malla_id)}}">Cancelar</button>
                                <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
                            @endif
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
        @include('semestres.materias.horarios.edit-scripts')
        @include('semestres.materias.horarios.edit-detalles-scripts')
    @endsection
@endcan
