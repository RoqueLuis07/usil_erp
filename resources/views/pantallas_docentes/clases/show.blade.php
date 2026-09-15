@can('ver_clases_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Ver Clase @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('clases_materias.modals.show-modals')

        <div class="row">
            <form id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar clase</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$clase->materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Docente</label>
                                    <input type="text" class="form-control" id="docente" value="{{$clase->docente->primer_nombre}} {{$clase->docente->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$clase->semestre->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Datos de la Clase</h5>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistDatosClase" role="tab" aria-selected="true">Datos de la Clase</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistAsistencias" role="tab" aria-selected="false">Lista de Asistencias</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistDatosClase" role="tabpanel">
                                            <input type="hidden" id="clase_id" value="{{$clase->id}}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-4 mb-3 text-center">
                                                            <label class="form-label" for="fecha_hora">Fecha y Hora</label>
                                                            <input type="text" class="form-control text-center" id="fecha_hora" value="{{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i')}}" readonly>
                                                        </div>
                                                        <div class="col-lg-5 mb-3 text-center">
                                                            <label class="form-label" for="tema_desarrollado">Tema Desarrollado</label>
                                                            <input type="text" class="form-control text-center" id="tema_desarrollado" value="{{$clase->tema_desarrollado}}" readonly>
                                                        </div>
                                                        <div class="col-lg-3 mb-3 text-center">
                                                            <label class="form-label" for="horas_desarrollo">Horas de Desarrollo</label>
                                                            <input type="text" class="form-control text-center" id="horas_desarrollo" value="{{$clase->horas_desarrollo}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4 mb-3 text-center">
                                                            <label class="form-label" for="modalidad">Modalidad</label>
                                                            <input type="text" class="form-control text-center" id="modalidad" value="{{$clase->modalidad->nombre}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="col-lg-12 mb-3">
                                                        <label class="form-label" for="observaciones-clase">Observaciones</label>
                                                        <span>{!! $clase->observaciones !!}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistAsistencias" role="tabpanel">
                                            <div class="row">
                                                <div class="table-responsive mb-1">
                                                    <table class="table align-middle table-nowrap">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>N° Documento</th>
                                                                <th>Alumno</th>
                                                                <th>Asistencia</th>
                                                                <th>Observaciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @forelse ($clase->alumnoAsistencias as $key => $asistencia)
                                                                <tr>
                                                                    <td>{{number_format($asistencia->alumno->numero_documento, 0, ',', '.')}}</td>
                                                                    <td>{{$asistencia->alumno->primer_nombre}} {{$asistencia->alumno->primer_apellido}}</td>
                                                                    <td>
                                                                        <button type="button" class="btn
                                                                            @if ($asistencia->estado == 'AU') btn-outline-danger
                                                                            @elseif ($asistencia->estado == 'PR') btn-success
                                                                            @elseif ($asistencia->estado == 'AJ') btn-outline-warning
                                                                            @endif" disabled>
                                                                            @if ($asistencia->estado == 'AU') Ausente
                                                                            @elseif ($asistencia->estado == 'PR') Presente
                                                                            @elseif ($asistencia->estado == 'AJ') Justificado
                                                                            @endif
                                                                        </button>
                                                                    </td>
                                                                    <td>
                                                                        @if ($asistencia->observaciones)
                                                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#observacionModal-{{$asistencia->id}}">Ver</button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4">
                                                                        <p>No hay alumnos inscriptos en su materia para poder tomar asistencia.</p>
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('pantallas_docentes.clases', Auth::id())}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
