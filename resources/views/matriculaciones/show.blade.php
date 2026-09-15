@can('ver_matriculaciones')
    @extends('layouts.master')
    @section('title') Ver Matriculación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Matriculaciones @endslot
            @slot('title') Ver Matriculación  @endslot
        @endcomponent

        @include('matriculaciones.scripts.messages-scripts')
        @include('matriculaciones.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar matriculación</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                @if (!$matriculacion->convenio_id && $matriculacion->estado == 'AC' && $matriculacion->tipo_pago != 'EM')
                                    @can('agregar_matriculaciones_convenios')
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#agregarConvenioModal">Aplicar Convenio</button>
                                    @endcan
                                @elseif ($matriculacion->convenio_id && $matriculacion->estado == 'AC' && $matriculacion->tipo_pago != 'EM')
                                    @can('eliminar_matriculaciones_convenios')
                                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#eliminarConvenioModal">Desvincular Convenio</button>
                                    @endcan
                                @endif
                                @can('imprimir_contratos_matriculaciones')
                                    <a class="btn btn-info" href="{{route('matriculaciones.pdf_contrato', $matriculacion->id)}}" target="_blank">Imprimir Contrato</a>
                                @endcan
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control" id="fecha" value="{{\Carbon\Carbon::parse($matriculacion->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$matriculacion->alumno->primer_nombre}} {{$matriculacion->alumno->primer_apellido}} - {{$matriculacion->alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="semestre">Semestre</label>
                                    <input type="text" class="form-control" id="semestre" value="{{$matriculacion->semestre->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$matriculacion->programa->nombre}}" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label" for="carrera">Carrera</label>
                                                <input type="text" class="form-control" id="carrera" value="{{$matriculacion->carrera->nombre_fantasia}}" readonly>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label" for="carrera_siu">Carrera SIU</label>
                                                <input type="text" class="form-control" id="carrera_siu" @if ($matriculacion->carrera_siu_id) value="{{$matriculacion->carreraSiu->nombre_fantasia}}" @endif readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($matriculacion->convenio_id)
                                        <div class="col-lg-6">
                                            <div class="row d-flex justify-content-end">
                                                <div class="col-lg-6 text-center">
                                                    <label class="form-label" for="convenio">Convenio</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-center" id="convenio" value="{{ $matriculacion->convenio->nombre }}" readonly>
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showConvenioModal" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <h4 class="card-title mb-0">Estado de Cuenta</h4>
                                    <label class="form-label" for="tipo_pago">Tipo de Pago: <b>@if ($matriculacion->tipo_pago == 'CO') CONTADO @elseif ($matriculacion->tipo_pago == 'CR') CREDITO @else EMPRESA @endif</b></label>
                                </div>
                                @if ($matriculacion->tipo_pago == 'CO' || $matriculacion->tipo_pago == 'CR')
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
                                                        <th>Factura</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    @foreach ($matriculacion->pagosMatriculacion as $detalle)
                                                        <tr>
                                                            <td>{{preg_replace('/ - .*/', '', $detalle->descripcion)}}</td>
                                                            <td>{{\Carbon\Carbon::parse($detalle->fecha_vencimiento)->format('d/m/Y')}}</td>
                                                            <td>{{number_format($detalle->monto, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->monto_convenio, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->monto_multa, 0, ',', '.')}}</td>
                                                            <td>{{number_format($detalle->saldo, 0, ',', '.')}}</td>
                                                            <td>
                                                                @if ($detalle->fecha_pago)
                                                                    {{\Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m/Y')}}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($detalle->ventaDetalle)
                                                                    @can('ver_ventas')
                                                                        <a href="{{ route('ventas.show', $detalle->ventaDetalle->venta_id) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta">{{ $detalle->ventaDetalle->venta->numero_factura}}</a>
                                                                    @else
                                                                        {{ $detalle->ventaDetalle->venta->numero_factura}}
                                                                    @endcan
                                                                @endif
                                                            </td>
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
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5"></th>
                                                        <th class="text-center @if ($matriculacion->pagosMatriculacion->sum('saldo') > 0) text-danger @endif">{{number_format($matriculacion->pagosMatriculacion->sum('saldo'), 0, ',', '.')}}</th>
                                                        <th colspan="3"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="card-body">
                                        <div class="alert alert-info text-center" role="alert">
                                            <strong>Esta matriculación se facturó a una empresa, para ver el estado de cuenta, visualice la factura <a href="{{ route('ventas.show', $matriculacion->venta_id) }}">{{ $matriculacion->venta->numero_factura }}</a>.</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$matriculacion->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($matriculacion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($matriculacion->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$matriculacion->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($matriculacion->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('matriculaciones.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('matriculaciones.scripts.show-scripts')
    @endsection
@endcan
