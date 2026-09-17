@can('ver_extensiones_docentes_pantalla')
    @extends('layouts.master-academic')
    @section('title') Ver Extensión @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.scripts.messages-scripts')
        @include('pantallas_docentes.extensiones.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar extensión universitaria</h4>
                            </div>
							<div class="col-lg-6 text-end">
                            <div class="d-flex justify-content-end">
								<a type="button" class="btn btn-secondary" href="{{asset('storage/extensiones_universitaria/plantillas/registro-asistencia.xlsx')}}" download="registro-asistencia.xlsx">
                                        <i class="ri-download-line align-bottom mb-0 me-2"></i> Registro de Asistencia
								</a>
                            </div>
                        </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="nombre_proyecto">Nombre del Proyecto</label>
                                    <input type="text" class="form-control" id="nombre_proyecto" value="{{$extension->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Responsable</label>
                                    <input type="text" class="form-control" id="docente" value="{{$extension->docente->primer_nombre}} {{$extension->docente->primer_apellido}}" readonly>
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
                                        <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_proyecto)}}" target="_blank">Ver</a>
                                        @if ($extension->estado == 'PE')
                                            @can('cambiar_proyectos_extensiones_docentes_pantalla')
                                                <a type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-proyecto">Cambiar</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                                @if ($extension->ubicacion_informe)
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="informe-show">Informe</label>
                                        <div class="text-center">
                                            <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_informe)}}" target="_blank">Ver</a>
                                            @if ($extension->estado == 'IN')
                                                @can('cambiar_informes_extensiones_docentes_pantalla')
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-informe">Cambiar</button>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @if ($extension->estado == 'AP')
                                        @can('cargar_informes_extensiones_docentes_pantalla')
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label" for="informe">Informe</label>
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cargarInformeModal-{{$extension->id}}">Cargar Informe</button>
                                                </div>
                                            </div>
                                        @endcan
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Alumnos Participantes</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach ($extension->extensionUniversitariaDetalles as $key =>$detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-5 col-lg-2 mb-2 text-center" id="div-numero_documento-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-numero_documento">N° Documento</label> @endif
                                                    <input type="text" class="form-control text-center" id="numero_documento-{{$key}}" value="{{$detalle->alumno->numero_documento}}" readonly>
                                                </div>
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno-{{$key}}" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}" readonly>
                                                </div>
                                                <div class="col-3 col-lg-2 mb-2 text-center" id="div-cantidad_horas_alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-cantidad_horas_alumno">Cant. Horas</label> @endif
                                                    @php
                                                        if ($detalle->cantidad_horas != 1) {
                                                            $texto_alumno = 'horas';
                                                        } else {
                                                            $texto_alumno = 'hora';
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control text-center" id="cantidad_horas_alumno-{{$key}}" @if ($detalle->cantidad_horas) value="{{number_format($detalle->cantidad_horas, 2, ',', '.')}} {{$texto_alumno}}" @else value="0 {{$texto_alumno}}" @endif readonly>
                                                </div>
                                                <div class="col-5 col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label">Postulación</label> @endif
                                                    <div>
                                                        <span class="badge @if ($detalle->estado == 'AC') bg-success-subtle text-success @elseif ($detalle->estado == 'RE') bg-danger-subtle text-danger @else bg-warning-subtle text-warning @endif">
                                                            @if ($detalle->estado == 'AC') Aceptada @elseif ($detalle->estado == 'RE') Rechazada @else Pendiente @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                @can('gestionar_postulaciones_extensiones_universitarias')
                                                    @if ($detalle->estado == 'PE')
                                                        <div class="col-lg-2 mb-2 text-center">
                                                            @if ($key == 0) <label class="form-label">Acciones</label> @endif
                                                            <div>
                                                                <form action="{{route('extensiones_universitarias.aprobar_postulacion', $detalle->id)}}" method="post" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Aceptar postulación"><i class="ri-check-fill"></i></button>
                                                                </form>
                                                                <form action="{{route('extensiones_universitarias.rechazar_postulacion', $detalle->id)}}" method="post" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Rechazar postulación"><i class="ri-close-fill"></i></button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('pantallas_docentes.extensiones_universitarias', Auth::id())}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.extensiones.scripts.show-scripts')
    @endsection
@endcan
