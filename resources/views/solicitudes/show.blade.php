@can('ver_solicitudes')
    @extends('layouts.master')
    @section('title') Ver Solicitud @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Solicitudes @endslot
            @slot('title') Ver Solicitud  @endslot
        @endcomponent

        @include('solicitudes.modals.show-modals')
        @include('solicitudes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-8">
                            <h4 class="card-title mb-0">Visualizar solicitud</h4>
                        </div>
                        <div class="col-lg-4 text-end">
                            @if ($solicitud->estado == 'PE')
                                @can('aprobar_solicitudes')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Aprobar</button>
                                @endcan
                                @can('rechazar_solicitudes')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Rechazar</button>
                                @endcan
                            @elseif ($solicitud->estado == 'AP')
                                @can('anular_aprobacion_solicitudes')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal">Anular Aprobación</button>
                                @endcan
                            @elseif (($solicitud->estado == 'PA' || $solicitud->estado == 'PR') && $solicitud->tipo_solicitud_id != 2)
                                @can('entregar_solicitudes')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#deliverModal">Entregar</button>
                                @endcan
                            @elseif ($solicitud->estado == 'GE')
                                @can('retirar_solicitudes')
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#paraRetiroModal">Para Retiro</button>
                                @endcan
                            @elseif ($solicitud->estado == 'RE')
                                @can('anular_rechazo_solicitudes')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unrejectModal">Anular Rechazo</button>
                                @endcan
                            @elseif ($solicitud->estado == 'EN')
                                @can('anular_entrega_solicitudes')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#undeliverModal">Anular Entrega</button>
                                @endcan
                            @endif
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="fecha_solicitud">Fecha de Solicitud</label>
                                <input type="text" class="form-control text-center" id="fecha_solicitud" value="{{Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i:s')}}" readonly>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label" for="alumno">Solicitante</label>
                                <input type="text" class="form-control text-center" id="alumno" value="{{$solicitud->alumno->primer_nombre}} {{$solicitud->alumno->primer_apellido}}" readonly>
                            </div>
                            <div class="col-lg-1 mb-3">
                                <label class="form-label" for="estado">Estado</label>
                                <input type="text" id="estado" class="form-control text-center fw-bold
                                @if ($solicitud->estado == 'PE')
                                    text-danger" value="PENDIENTE"
                                @elseif ($solicitud->estado == 'AP')
                                    text-primary" value="APROBADO"
                                @elseif ($solicitud->estado == 'PA')
                                    text-warning" value="PAGADO"
                                @elseif ($solicitud->estado == 'GE')
                                    text-warning" value="GENERADO"
                                @elseif ($solicitud->estado == 'PR')
                                    text-info" value="PARA RETIRO"
                                @elseif ($solicitud->estado == 'EN')
                                    text-success" value="ENTREGADO"
                                    @elseif ($solicitud->estado == 'RE')
                                    text-danger" value="RECHAZADO"
                                @endif
                                readonly>
                            </div>
                            @if ($solicitud->tipo_solicitud_id == 2)
                                <div class="col-lg-1 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="adjunto">Certificado</label>
                                    </div>
                                    <a type="button" class="btn btn-warning" href="{{asset($solicitud->adjunto)}}" target="_blank">Ver</a>
                                </div>
                            @elseif ($solicitud->tipo_solicitud_id == 3)
                                <div class="col-lg-1 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="adjunto">Justificativo</label>
                                    </div>
                                    <a type="button" class="btn btn-warning" href="{{asset($solicitud->adjunto)}}" target="_blank">Ver</a>
                                </div>
                            @endif
                            <div class="@if ($solicitud->tipo_solicitud_id == 2 || $solicitud->tipo_solicitud_id == 3) col-lg-5 @else col-lg-6 @endif d-flex justify-content-end">
                                <div class="col-lg-3 mb-3 me-3 text-center">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control text-center" id="programa" value="{{$solicitud->programa->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="semestre">Semestre</label>
                                    <input type="text" class="form-control text-center" id="semestre" value="{{$solicitud->semestre->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="tipo">Solicitud</label>
                                <input type="text" class="form-control text-center" id="tipo" value="{{$solicitud->tipoSolicitud->nombre}}" readonly>
                            </div>
                            @if ($solicitud->tipo_solicitud_id == 2)
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia para Suficiencia</label>
                                    <input type="text" class="form-control text-center" id="materia" value="{{$solicitud->materia->nombre_real}}" readonly>
                                </div>
                            @elseif ($solicitud->tipo_solicitud_id == 3)
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inasistencia">Fecha de Inasistencia</label>
                                    <input type="text" class="form-control text-center" id="fecha_inasistencia" value="{{Carbon\Carbon::parse($solicitud->fecha_inasistencia)->format('d/m/Y')}}" readonly>
                                </div>
                            @elseif ($solicitud->tipo_solicitud_id == 5)
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="materia_tutoria">Materia de Tutoría</label>
                                    <input type="text" class="form-control text-center" id="materia_tutoria" value="{{$solicitud->materia->nombre_real}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="modalidad_tutoria">Modalidad de Tutoría</label>
                                    <input type="text" class="form-control text-center" id="modalidad_tutoria" value="{{$solicitud->modalidad->nombre}}" readonly>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="form-label" for="observaciones">Observaciones</label>
                                <textarea class="form-control" id="observaciones" cols="30" rows="10" readonly>@if ($solicitud->observaciones) {{$solicitud->observaciones}} @else SIN OBSERVACIONES @endif</textarea>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="pago">Estado del Pago</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap">
                                                <thead class="table text-center">
                                                    <tr>
                                                        <th>Vencimiento</th>
                                                        <th>Monto</th>
                                                        <th>Saldo</th>
                                                        <th>Fecha Pago</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    @if ($solicitud->pagoSolicitud)
                                                        <tr>
                                                            <td>{{Carbon\Carbon::parse($solicitud->pagoSolicitud->vencimiento)->format('d/m/Y')}}</td>
                                                            <td>Gs. {{number_format($solicitud->pagoSolicitud->monto, 0, ',', '.')}}</td>
                                                            <td>Gs. {{number_format($solicitud->pagoSolicitud->saldo, 0, ',', '.')}}</td>
                                                            @if ($solicitud->pagoSolicitud->fecha_pago)
                                                                <td>{{Carbon\Carbon::parse($solicitud->pagoSolicitud->fecha_pago)->format('d/m/Y')}}</td>
                                                            @else
                                                                <td></td>
                                                            @endif
                                                            <td>
                                                                <span
                                                                    class="badge @if ($solicitud->pagoSolicitud->estado == 'PE')
                                                                        bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                    @elseif ($solicitud->pagoSolicitud->estado == 'PA')
                                                                        bg-warning-subtle text-warning text-uppercase"> Parcial
                                                                    @elseif ($solicitud->pagoSolicitud->estado == 'CA')
                                                                        bg-success-subtle text-success text-uppercase"> Pagado
                                                                    @endif
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @if ($solicitud->tipo_solicitud_id == 5 && $solicitud->estado == 'AP')
                                                            <tr>
                                                                <td>{{Carbon\Carbon::parse($pago_tutoria->vencimiento)->format('d/m/Y')}}</td>
                                                                <td>Gs. {{number_format($pago_tutoria->monto, 0, ',', '.')}}</td>
                                                                <td>Gs. {{number_format($pago_tutoria->saldo, 0, ',', '.')}}</td>
                                                                @if ($pago_tutoria->fecha_pago)
                                                                    <td>{{Carbon\Carbon::parse($pago_tutoria->fecha_pago)->format('d/m/Y')}}</td>
                                                                @else
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    <span
                                                                        class="badge @if ($pago_tutoria->estado == 'PE')
                                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                        @elseif ($pago_tutoria->estado == 'PA')
                                                                            bg-warning-subtle text-warning text-uppercase"> Parcial
                                                                        @elseif ($pago_tutoria->estado == 'CA')
                                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                                        @endif
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td colspan="5">El pago es generado al aprobar la solicitud.</td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                </div>
                <div class="row">
                    @if ($solicitud->aprobado_por_id)
                        <div class="col-lg-4 mb-3">
                            <label class="form-label" for="aprobado_por">Aprobado por:</label>
                            <br>
                            {{$solicitud->aprobadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($solicitud->fecha_aprobacion)->format('d/m/Y H:i:s')}}
                        </div>
                    @endif
                    @if ($solicitud->rechazado_por_id)
                        <div class="col-lg-4 mb-3">
                            <label class="form-label" for="rechazado_por">Rechazado hecha por:</label>
                            <br>
                            {{$solicitud->rechazadoPor->name}}
                        </div>
                    @endif
                    @if ($solicitud->entregado_por_id)
                        <div class="col-lg-4 mb-3">
                            <label class="form-label" for="entregado_por">Rechazado hecha por:</label>
                            <br>
                            {{$solicitud->entregadoPor->name}}, , en fecha: {{\Carbon\Carbon::parse($solicitud->fecha_entrega)->format('d/m/Y H:i:s')}}
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-lg-12 text-end">
                        <a type="button" class="btn btn-danger me-2" href="{{route('solicitudes.index')}}">Volver</a>
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
        @include('solicitudes.scripts.show-scripts')
    @endsection
@endcan
