@can('ver_extensiones_ubs')
    @extends('layouts.master')
    @section('title') Ver Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Ver Extensión Universitaria  @endslot
        @endcomponent

        @include('ubs.maestrias.extensiones_universitarias.scripts.messages-scripts')
        @include('ubs.maestrias.extensiones_universitarias.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar extensión universitaria</h4>
                            </div>
                            @if ($extension->estado == 'IN')
                                @can('editar_horas_alumnos_extensiones_ubs')
                                    <div class="col-lg-6 text-end">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#horasModal">Cargar Horas</button>
                                    </div>
                                @endcan
                            @elseif ($extension->estado == 'CO')
                                @can('editar_horas_alumnos_extensiones_ubs')
                                    <div class="col-lg-6 text-end">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeHorasModal">Editar Horas</button>
                                    </div>
                                @endcan
                            @endif
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="nombre_proyecto">Nombre del Proyecto</label>
                                    <input type="text" class="form-control" id="nombre_proyecto" value="{{$extension->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Responsable</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$extension->alumno->primer_nombre}} {{$extension->alumno->primer_apellido}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="tipo_extension">Tipo de Actividad</label>
                                    <input type="text" class="form-control" id="tipo_extension" value="{{$extension->tipoExtension->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_horas_proyecto">Horas del Proyecto</label>
                                    @php
                                        if ($extension->cantidad_horas != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    <input type="text" class="form-control text-center" id="cantidad_horas_proyecto" value="{{number_format($extension->cantidad_horas, 2, ',', '.')}} {{$texto}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold
                                        @if ($extension->estado == 'PE')
                                            text-warning
                                        @elseif($extension->estado == 'AP')
                                            text-info
                                        @elseif($extension->estado == 'IN')
                                            text-info
                                        @elseif($extension->estado == 'RE')
                                            text-danger
                                        @elseif($extension->estado == 'CO')
                                            text-secondary
                                        @elseif($extension->estado == 'FI')
                                            text-success
                                        @endif" id="estado"
                                        @if ($extension->estado == 'PE')
                                            value="PENDIENTE"
                                        @elseif($extension->estado == 'AP')
                                            value="APROBADO"
                                        @elseif($extension->estado == 'IN')
                                            value="INFORMADO"
                                        @elseif($extension->estado == 'RE')
                                            value="RECHAZADO"
                                        @elseif($extension->estado == 'CO')
                                            value="COMPLETO"
                                        @elseif($extension->estado == 'FI')
                                            value="FINALIZADO"
                                        @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="proyecto">Proyecto</label>
                                    <div class="text-center">
                                        @can('ver_adjunto_proyectos_extensiones_ubs')
                                            <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_proyecto)}}" target="_blank">Ver</a>
                                        @endcan
                                        @can('cambiar_adjunto_proyectos_extensiones_ubs')
                                            @if ($extension->estado == 'PE')
                                                <a type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-proyecto">Cambiar</a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                @if ($extension->ubicacion_informe)
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="informe-show">Informe</label>
                                        <div class="text-center">
                                            @can('ver_adjunto_informes_extensiones_ubs')
                                                <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_informe)}}" target="_blank">Ver</a>
                                            @endcan
                                            @can('cambiar_adjunto_informes_extensiones_ubs')
                                                @if ($extension->estado == 'IN')
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-informe">Cambiar</button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                @else
                                    @if ($extension->estado == 'AP')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="informe">Informe</label>
                                            @can('cargar_informes_extensiones_ubs')
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cargarInformeModal-{{$extension->id}}">Cargar Informe</button>
                                                </div>
                                            @endcan
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$extension->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($extension->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($extension->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$extension->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($extension->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('extensiones_universitarias_ubs.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.maestrias.extensiones_universitarias.scripts.show-scripts')
    @endsection
@endcan
