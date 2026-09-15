@can('ver_rubricas_alumnos_tesis')
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

        @include('tesis.cargar_rubricas.proceso.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar rúbrica de trabajos finales de grado</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @can('editar_rubricas_alumnos_tesis')
                                    @if ($rubrica->inscripcion->estado == 'FE' || $rubrica->inscripcion->estado == 'PA')
                                        <a type="button" class="btn btn-warning me-2" href="{{route('cargar_rubricas_tesis.edit_proceso', $rubrica->inscripcion_id)}}">Editar Rúbrica</a>
                                    @endif
                                @endcan
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
                                <div class="col-lg-12 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-1 me-3">
                                        <label class="form-label" for="total_posible">Total Posible</label>
                                        <input class="form-control text-center" type="text" id="total_posible" value="{{$total_posible}}" readonly>
                                    </div>
                                    <div class="col-lg-1">
                                        <label class="form-label" for="puntaje_total">Puntos Obtenidos</label>
                                        <input class="form-control text-center" type="text" id="puntaje_total" value="{{$rubrica->detalles->sum('puntos_obtenidos')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Puntajes de la Rúbrica</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle" id="inscripciones-list">
                                            <tbody class="list form-check-all">
                                                @foreach ($rubrica->detalles as $key => $detalle)
                                                    @if ($key == 0)
                                                    <tr class="fw-bold text-center">
                                                        <td class="border-end" style="width: 50%">Descripción</td>
                                                        <td style="width: 25%">Puntos Posibles</td>
                                                        <td style="width: 25%">Puntos Obtenidos</td>
                                                    </tr>
                                                        @endif
                                                    <tr>
                                                        <td class="table-info fw-bold" colspan="3">{{$key + 1}}. {{$detalle->rubricaDetalle->nivel->nombre}}</td>
                                                    </tr>
                                                    <tr class="align-middle">
                                                        <td class="border-end">{{$detalle->rubricaDetalle->descripcion}}</td>
                                                        <td class="text-center border-end">{{$detalle->rubricaDetalle->puntos}}</td>
                                                        <td class="text-center">{{$detalle->puntos_obtenidos}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
        @include('tesis.cargar_rubricas.proceso.scripts.show-scripts')
    @endsection
@endcan
