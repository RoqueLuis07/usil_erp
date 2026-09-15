@can('ver_tutorias')
    @extends('layouts.master')
    @section('title') Ver Tutoría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tutorías @endslot
            @slot('title') Ver Tutoría  @endslot
        @endcomponent

        @include('tutorias.modals.show-modals')
        @include('tutorias.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar tutoría</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
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
                                    <label class="form-label" for="cantidad_clases">Cant. Clases</label>
                                    <input type="text" class="form-control text-center" id="cantidad_clases" value="{{$tutoria->cantidad_clases}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold @if ($tutoria->estado == 'AC') text-success @elseif ($tutoria->estado == 'EC') text-warning @elseif ($tutoria->estado == 'FI') text-danger @endif" id="estado" @if ($tutoria->estado == 'AC') value="ACTIVO" @elseif ($tutoria->estado == 'EC') value="EN CURSO" @elseif ($tutoria->estado == 'FI') value="FINALIZADO" @endif readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Horario</h4>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        @can('editar_horarios_tutorias')
                                            @if ($tutoria->estado == 'AC')
                                            <a class="btn @if ($tutoria->horarios->count() != 0) btn-warning @else btn-success @endif" href="{{route('tutorias.edit_horario', $tutoria->id)}}">@if ($tutoria->horarios->count() != 0) Editar @else Agregar @endif Horario</a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @forelse ($tutoria->horarios as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="dia">Día</label> @endif
                                                    <input type="text" class="form-control text-center" id="dia" value="{{$detalle->diaSemana->nombre}}" readonly>
                                                </div>
                                                <div class="col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="hora_inicio">Hora Inicio</label> @endif
                                                    <input type="text" class="form-control text-center" id="hora_inicio" value="{{\Carbon\Carbon::parse($detalle->hora_inicio)->format('H:i')}}" readonly>
                                                </div>
                                                <div class="col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="hora_fin">Hora Fin</label> @endif
                                                    <input type="text" class="form-control text-center" id="hora_fin" value="{{\Carbon\Carbon::parse($detalle->hora_fin)->format('H:i')}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row">
                                            <div class="col-lg-12 text-center">
                                                <p>Debe cargar el horario para poder visualizarlo.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Alumnos</h4>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        @can('editar_alumnos_tutorias')
                                            @if ($tutoria->estado == 'AC')
                                                <a class="btn @if ($tutoria->alumnos->count() != 0) btn-warning @else btn-success @endif" href="{{route('tutorias.edit_alumnos', $tutoria->id)}}">@if ($tutoria->alumnos->count() != 0) Editar @else Agregar @endif Alumnos</a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @forelse ($tutoria->alumnos as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="numero_documento">N° Documento</label> @endif
                                                    <input type="text" class="form-control text-center" id="numero_documento" value="{{number_format($detalle->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-3 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="carrera">Carrera</label> @endif
                                                    <input type="text" class="form-control text-center" id="carrera" value="{{$detalle->carrera->nombre_fantasia}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="estado">Estado</label> @endif
                                                    <input type="text" class="form-control text-center fw-bold
                                                        @if ($detalle->estado == 'IN')
                                                            text-warning"
                                                        @elseif ($detalle->estado == 'PA')
                                                            text-success"
                                                        @elseif ($detalle->estado == 'EC')
                                                            text-warning"
                                                        @elseif ($detalle->estado == 'AN')
                                                            text-danger"
                                                        @elseif ($detalle->estado == 'RE')
                                                            text-danger"
                                                        @elseif ($detalle->estado == 'AP')
                                                            text-success"
                                                        @endif
                                                    id="estado"
                                                        @if ($detalle->estado == 'IN')
                                                            value="INSCRIPTO"
                                                        @elseif ($detalle->estado == 'PA')
                                                            value="PAGADO"
                                                        @elseif ($detalle->estado == 'EC')
                                                            value="EN CURSO"
                                                        @elseif ($detalle->estado == 'AN')
                                                            value="ANULADO"
                                                        @elseif ($detalle->estado == 'RE')
                                                            value="REPROBADO"
                                                        @elseif ($detalle->estado == 'AP')
                                                            value="APROBADO"
                                                        @endif
                                                    readonly>
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="acciones">Acciones</label> @endif
                                                    @can('ver_puntajes_alumnos_tutorias')
                                                        <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#showDetalleAlumnoModal-{{$detalle->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Detalle"><i class="ri-eye-fill"></i></button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row">
                                            <div class="col-lg-12 text-center">
                                                <p>La tutoría no cuenta con alumnos inscriptos.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$tutoria->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($tutoria->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($tutoria->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$tutoria->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($tutoria->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tutorias.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('tutorias.scripts.show-scripts')
    @endsection
@endcan
