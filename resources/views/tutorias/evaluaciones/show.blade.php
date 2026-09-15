@can('ver_evaluaciones_tutorias')
    @extends('layouts.master')
    @section('title') Ver Evaluación @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Materias por Semestres @endslot
            @slot('title') Ver Evaluación  @endslot
        @endcomponent

        @include('materias_semestres.evaluaciones.scripts.messages-scripts')
        @include('materias_semestres.evaluaciones.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar evaluación</h4>
                            </div>
                            @if ($alumnos->count() != 0)
                                <div class="col-lg-6 text-end">
                                    <div class="d-flex justify-content-end">
                                        @can('ver_actas_tutorias')
                                                <a type="button" class="btn btn-info me-2" href="{{route('actas_evaluaciones.show_actas', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id])}}">Ver Acta</a>
                                        @endcan
                                        @can('crear_evaluaciones_tutorias')
                                            <a type="button" class="btn btn-success" href="{{route('materias_evaluaciones.create', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id])}}">Agregar Evaluación</a>
                                        @endcan
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}} - {{$materia->nombre_real}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="materia">Docente</label>
                                    <input type="text" class="form-control" id="materia" @if($semestre_malla_materia->docente_id) value="{{$semestre_malla_materia->docente->primer_nombre}} {{$semestre_malla_materia->docente->primer_apellido}}" @endif readonly>
                                </div>
                                <div class="col-lg-4 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-6">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control" id="semestre" value="{{$semestre->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Evaluaciones de Alumnos</h4>
                                </div>
                                <div class="card-body">
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-12">
                                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-info" id="acordeon">
                                                        @forelse ($alumnos as $alumno)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header">
                                                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acordeon-{{$alumno->id}}" aria-expanded="false" aria-controls="acordeon-{{$alumno->id}}" id="alumno-{{$alumno->id}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}} <small class="text-muted">&nbsp;{{$alumno->numero_documento}}</small></button>
                                                                </h2>
                                                                <div class="accordion-collapse collapse" id="acordeon-{{$alumno->id}}" aria-labelledby="{{$alumno->id}}-acordeon" data-bs-parent="#acordeon">
                                                                    <div class="accordion-body">
                                                                        <div class="row">
                                                                            @foreach ($alumno->alumnoPuntajes as $key => $puntaje)
                                                                                @if ($puntaje->puntos_obtenidos != 0 || $puntaje->evaluacion_id == 6)
                                                                                    <div class="col-lg-3 border d-flex justify-content-center align-items-center me-3 mb-3">
                                                                                        <table class="table table-sm table-borderless">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <th class="ps-0 fs-md" scope="row">Evaluación:</th>
                                                                                                    <td class="text-muted fs-sm">{{$puntaje->evaluacion->nombre}}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <th class="ps-0 fs-md" scope="row">Tipo:</th>
                                                                                                    <td class="text-muted fs-sm">{{$puntaje->evaluacion->tipoEvaluacion->nombre}}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <th class="ps-0 fs-md" scope="row">Puntos Obtenidos:</th>
                                                                                                    <td class="text-muted fs-sm">{{$puntaje->puntos_obtenidos}} - Real: {{($puntaje->puntos_obtenidos / 100) * $puntaje->evaluacion->valor_porcentual }}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <th class="ps-0 fs-md" scope="row">Puntos Posibles:</th>
                                                                                                    <td class="text-muted fs-sm">{{$puntaje->evaluacion->puntos}} - Real: {{($puntaje->evaluacion->valor_porcentual / $puntaje->evaluacion->puntos) * 100}}</td>
                                                                                                </tr>
                                                                                                <tr style="text-align: center">
                                                                                                    <td colspan="2">
                                                                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEvaluacionModal-{{$puntaje->id}}">Editar</button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="row d-flex flex-wrap justify-content-center">
                                                                <div class="col-lg-12 col-sm-12 mb-2 text-center">
                                                                    @if ($alumnos->count() != 0)
                                                                        <p>La materia no cuenta con evaluaciones realizadas.</p>
                                                                    @else
                                                                    <p>La materia no cuenta con alumnos inscriptos.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$escala->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($escala->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($escala->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$escala->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($escala->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div> --}}
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
        @include('materias_semestres.evaluaciones.scripts.show-scripts')
    @endsection
@endcan
