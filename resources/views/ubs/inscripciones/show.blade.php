@can('ver_inscripciones_ubs')
    @extends('layouts.master')
    @section('title') Ver Inscripción @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inscripciones @endslot
            @slot('title') Ver Inscripción  @endslot
        @endcomponent

        @include('ubs.inscripciones.scripts.messages-scripts')
        @include('ubs.inscripciones.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar inscripción</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @if (!$inscripcion->convenio_id && $inscripcion->estado == 'AC' && $inscripcion->tipo_pago != 'EM')
                                    @can('agregar_inscripciones_ubs_convenios')
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#agregarConvenioModal">Aplicar Convenio</button>
                                    @endcan
                                @elseif ($inscripcion->convenio_id && $inscripcion->estado == 'AC' && $inscripcion->tipo_pago != 'EM')
                                    @can('eliminar_inscripciones_ubs_convenios')
                                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#eliminarConvenioModal">Desvincular Convenio</button>
                                    @endcan
                                @endif
                                @can('imprimir_contratos_inscripciones_ubs')
                                    @if ($inscripcion->curso->programa_id >= 5)
                                        <a class="btn btn-info" href="{{route('inscripciones_ubs.pdf_contrato', $inscripcion->id)}}" target="_blank">Imprimir Contrato</a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control" id="fecha" value="{{\Carbon\Carbon::parse($inscripcion->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}} - {{$inscripcion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Curso</label>
                                    <input type="text" class="form-control" id="curso" value="{{$inscripcion->curso->nombre_fantasia}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <h4 class="card-title mb-0">Estado de Cuenta</h4>
                                    <label class="form-label" for="tipo_pago">Tipo de Pago: <b>@if ($inscripcion->tipo_pago == 'CO') CONTADO @elseif ($inscripcion->tipo_pago == 'CR') CREDITO @else EMPRESA @endif</b></label>
                                </div>
                                @if ($inscripcion->tipo_pago == 'CO' || $inscripcion->tipo_pago == 'CR')
                                    <div class="card-body">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap">
                                                <thead class="table text-center">
                                                    <tr>
                                                        <th>Descripción</th>
                                                        <th>Vencimiento</th>
                                                        <th>Monto</th>
                                                        <th>Convenio</th>
                                                        <th>Multa</th>
                                                        <th>Saldo</th>
                                                        <th>Fecha Pago</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    @foreach ($inscripcion->pagosInscripciones as $detalle)
                                                        <tr>
                                                            <td>{{preg_replace('/ - .*/', '', $detalle->descripcion)}}</td>
                                                            <td>{{\Carbon\Carbon::parse($detalle->fecha_vencimiento)->format('d/m/Y')}}</td>
                                                            <td>{{number_format($detalle->monto, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->monto_convenio, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->monto_multa, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->saldo, 0, ',', '.')}}</td>
                                                            @if ($detalle->fecha_pago)
                                                                <td>{{\Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m/Y')}}</td>
                                                            @else
                                                                <td></td>
                                                            @endif
                                                            <td>
                                                                <span
                                                                    class="badge @if ($detalle->estado == 'PE')
                                                                        bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                                    @elseif ($detalle->estado == 'PA')
                                                                        bg-warning-subtle text-warning text-uppercase"> Pago Parcial
                                                                    @elseif ($detalle->estado == 'CA')
                                                                        bg-success-subtle text-success text-uppercase"> Pagado
                                                                    @elseif ($detalle->estado == 'AN')
                                                                        bg-danger-subtle text-danger text-uppercase"> Anulado
                                                                    @elseif ($detalle->estado == 'CO')
                                                                        bg-success-subtle text-success text-uppercase"> Convenio
                                                                    @elseif ($detalle->estado == 'CP')
                                                                        bg-warning-subtle text-warning text-uppercase"> Convenio Parcial
                                                                    @endif
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info text-center" role="alert">
                                        <strong>Esta inscripción se facturó a una empresa, para ver el estado de cuenta, visualice la factura <a href="{{ route('ventas.show', $inscipcion->venta_id) }}">{{ $inscipcion->venta->numero_factura }}</a>.</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$inscripcion->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($inscripcion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($inscripcion->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$inscripcion->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($inscripcion->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('inscripciones_ubs.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('ubs.inscripciones.scripts.show-scripts')
    @endsection
@endcan
