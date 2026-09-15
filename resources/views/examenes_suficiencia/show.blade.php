@can('ver_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Ver Examen de Suficiencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Exámenes de Suficiencia @endslot
            @slot('title') Ver Examen de Suficiencia  @endslot
        @endcomponent

        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-8">
                            <h4 class="card-title mb-0">Visualizar examen de suficiencia</h4>
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 mb-3 text-center">
                                <label class="form-label" for="materia">Materia</label>
                                <input type="text" class="form-control" id="materia" value="{{$examen->materia->nombre_fantasia}}" readonly>
                            </div>
                            <div class="col-lg-3 mb-3 text-center">
                                <label class="form-label" for="docente">Docente</label>
                                <input type="text" class="form-control" id="docente" value="{{$examen->docente->primer_nombre}} {{$examen->docente->primer_apellido}} - {{$examen->docente->numero_documento}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3 text-center">
                                <label class="form-label" for="modalidad">Modalidad</label>
                                <input type="text" class="form-control" id="modalidad" value="{{$examen->modalidad->nombre}}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3 text-center">
                                <label class="form-label" for="fecha_examen">Fecha de Examen</label>
                                <input type="text" class="form-control text-center" id="fecha_examen" value="{{\Carbon\Carbon::parse($examen->fecha_examen)->format('d/m/Y H:i')}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3 text-center">
                                <label class="form-label" for="aula_examen">Aula</label>
                                <input type="text" class="form-control text-center" id="aula_examen" value="{{$examen->aula_examen}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3 text-center">
                                <label class="form-label" for="semestre">Semestre</label>
                                <input type="text" class="form-control text-center" id="semestre" value="{{$examen->semestre->nombre}}" readonly>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-lg-3 mb-3 text-center">
                                <label class="form-label" for="alumno">Alumno</label>
                                <input type="text" class="form-control" id="alumno" value="{{$examen->alumno->primer_nombre}} {{$examen->alumno->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3">
                                <label class="form-label" for="alumno_documento">N° Documento</label>
                                <input type="text" class="form-control text-center" id="alumno_documento" value="{{$examen->alumno->numero_documento}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3 text-center">
                                <label class="form-label" for="puntos_obtenidos_show">Puntos Examen</label>
                                <input type="text" class="form-control text-center" id="puntos_obtenidos_show" value="{{$examen->puntos_obtenidos}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3 text-center">
                                <label class="form-label" for="calificacion_show">Calificación</label>
                                <input type="text" class="form-control text-center" id="calificacion_show" value="{{$examen->calificacion}}" readonly>
                            </div>
                            <div class="col-lg-6 d-flex justify-content-end">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" id="estado" class="form-control text-center fw-bold
                                    @if ($examen->estado == 'AC')
                                        text-primary" value="ACTIVO"
                                    @elseif ($examen->estado == 'GE')
                                        text-primary" value="ACTA GEN."
                                    @elseif ($examen->estado == 'AP')
                                        text-success" value="APROBADO"
                                    @elseif ($examen->estado == 'RE')
                                        text-danger" value="REPROBADO"
                                    @endif
                                    readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($examen->actualizado_por_id)
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="actualizado_por">Última actualización hecha por:</label>
                            <br>
                            {{$examen->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($examen->updated_at)->format('d/m/Y H:i:s')}}
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-12 text-end">
                        <a type="button" class="btn btn-danger me-2" href="{{route('examenes_suficiencia.index')}}">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js')}}"></script>
        @include('examenes_suficiencia.scripts.show-scripts')
    @endsection
@endcan
