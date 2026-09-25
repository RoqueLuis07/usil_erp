@can('ver_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Ver Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Ver Extensión Universitaria  @endslot
        @endcomponent

        @include('extensiones_universitarias.scripts.messages-scripts')
        @include('extensiones_universitarias.modals.show-modals')

        <div class="row">
            <div>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar extensión universitaria</h4>
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
                                        @can('ver_adjunto_proyectos_extensiones_universitarias')
                                            <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_proyecto)}}" target="_blank">Ver</a>
                                        @endcan
                                        @if ($extension->estado == 'PE')
                                            @can('cambiar_adjunto_proyectos_extensiones_universitarias')
                                                <a type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-proyecto">Cambiar</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                                @if ($extension->ubicacion_informe)
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="informe-show">Informe</label>
                                        <div class="text-center">
                                            @can('ver_adjunto_informes_extensiones_universitarias')
                                                <a type="button" class="btn btn-warning" href="{{asset($extension->ubicacion_informe)}}" target="_blank">Ver</a>
                                            @endcan
                                            @if ($extension->estado == 'IN')
                                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cambiarAdjuntosModal-{{$extension->id}}" id="btn-change-informe">Cambiar</button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @if ($extension->estado == 'AP')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="informe">Informe</label>
                                            <div class="text-center">
                                                @can('cargar_informes_extensiones_universitarias')
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#cargarInformeModal-{{$extension->id}}">Cargar Informe</button>
                                                @endcan
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
							<div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio</label>
                                    <input type="text" class="form-control" id="fecha_inicio" value="{{\Carbon\Carbon::parse($extension->fecha_inicio)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                    <input type="text" class="form-control" id="fecha_fin" value="{{\Carbon\Carbon::parse($extension->fecha_fin)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cupo_maximo">Cupo</label>
                                    <input type="text" class="form-control text-center" id="cupo_maximo" value="{{$extension->cupo_maximo ? $extension->cuposDisponibles() . ' / ' . $extension->cupo_maximo . ' disponibles' : 'Sin límite'}}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="carreras_habilitadas">Carreras Habilitadas</label>
                                    <input type="text" class="form-control" id="carreras_habilitadas" value="{{$extension->carreras->isEmpty() ? 'Todas las carreras' : $extension->carreras->pluck('nombre_fantasia')->implode(', ')}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Alumnos Participantes: {{ $extension->extensionUniversitariaDetalles->count() }}</h4>
                                    </div>
                                    @can('editar_horas_alumnos_extensiones_universitarias')
                                        @if ($extension->estado == 'IN' || $extension->estado == 'CO')
                                            <div class="col-lg-6 text-end" id="div-subir" style="margin-bottom: -5em">
                                                <div class="d-flex justify-content-end">
                                                    <a type="button" class="btn btn-warning me-2" href="{{route('extensiones_universitarias.edit_hours', $extension->id)}}">@if ($extension->estado == 'IN') Cargar @elseif ($extension->estado == 'CO') Editar @endif Horas</a>
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                                <div class="card-body">
                                    @foreach ($extension->extensionUniversitariaDetalles as $key =>$detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-5 col-lg-2 mb-2 text-center" id="div-numero_documento-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-numero_documento">N° Documento</label> @endif
                                                    <input type="text" class="form-control text-center" id="numero_documento-{{$key}}" value="{{$detalle->alumno->numero_documento}}">
                                                </div>
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-alumno">Alumno</label> @endif
                                                    <input type="text" class="form-control text-center" id="alumno-{{$key}}" value="{{$detalle->alumno->primer_nombre}} {{$detalle->alumno->primer_apellido}}">
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
                                                @if ($extension->estado == 'IN' && $extension->tiene_certificado)
                                                    <div class="col-lg-2 mb-2 text-center">
                                                        @if ($key == 0)
                                                            <div>
                                                                <label class="form-label" for="show_certificado-{{$key}}">Certificado</label>
                                                            </div>
                                                        @endif
                                                        @if ($detalle->url_certificado)
                                                            <a type="button" class="btn btn-sm btn-info" href="{{asset($detalle->url_certificado)}}" target="_blank" id="show_certificado-{{$key}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-fill"></i></a>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-danger" id="show_certificado-{{$key}}" disabled><i class="ri-close-fill"></i></button>
                                                        @endif
                                                    </div>
                                                @endif
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('extensiones_universitarias.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('extensiones_universitarias.scripts.show-scripts')
    @endsection
@endcan
