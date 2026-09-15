@can('ver_rubricas_alumnos_defensas_tesis')
    @extends('layouts.master')
    @section('title') Ver Rúbrica de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Rúbricas de TFG @endslot
            @slot('title') Ver Rúbrica de TFG  @endslot
        @endcomponent

        @include('tesis.cargar_rubricas.defensa.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar rúbrica de trabajos finales de grado</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$rubrica->inscripcion->alumno->primer_nombre}} {{$rubrica->inscripcion->alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ci_alumno">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="ci_alumno" value="{{number_format($rubrica->inscripcion->alumno->numero_documento, 0, ',', '.')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3"></div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tutor">Tutor</label>
                                    <input type="text" class="form-control" id="tutor" value="{{$rubrica->inscripcion->tutor->primer_nombre}} {{$rubrica->inscripcion->tutor->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ci_tutor">N° Documento</label>
                                    <input type="text" class="form-control text-center" id="ci_tutor" value="{{number_format($rubrica->inscripcion->tutor->numero_documento, 0, ',', '.')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5 mb-5">
                                    <label class="form-label" for="tema">Tema</label>
                                    <input type="text" class="form-control" id="tema" value="{{$rubrica->inscripcion->tema}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-5"></div>
                                <div class="col-lg-3 mb-5">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" value="{{$rubrica->inscripcion->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-5">
                                    <label class="form-label" for="fecha_defensa">Fecha Defensa</label>
                                    <input type="text" class="form-control text-center" id="fecha_defensa" value="{{\Carbon\Carbon::parse($rubrica->inscripcion->fecha_defensa)->format('d/m/Y H:i')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="puntaje_obtenido">Puntos Obtenidos</label>
                                    <input type="text" class="form-control text-center" id="puntaje_obtenido" value="{{$rubrica->puntaje_total}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="calificacion">Calificación</label>
                                    <input type="text" class="form-control text-center" id="calificacion" value="{{$rubrica->inscripcion->calificacion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="puntaje_obtenido">Acta de Evaluación</label>
                                    <div class="text-center">
                                        <a class="btn btn-primary" href="{{asset($rubrica->url_acta)}}" target="_blank">Ver Acta</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-4">
                                        <label class="form-label" for="total_posible">Total Posible</label>
                                        <input class="form-control text-center" type="text" id="total_posible" value="{{$total_posible}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$rubrica->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($rubrica->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($rubrica->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$rubrica->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($rubrica->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('inscripciones_temas_tesis.show', $rubrica->inscripcion_id)}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('tesis.cargar_rubricas.defensa.scripts.show-scripts')
    @endsection
@endcan
