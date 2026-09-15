@can('crear_ventas_cajero')
    @extends('layouts.master')
    @section('title') Caja @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Caja  @endslot
        @endcomponent

        @include('cajeros.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('cajero.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva venta</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row d-flex flex-wrap justify-content-between">
                                <div class="col-lg-8 d-flex flex-wrap">
                                    <div class="col-lg-2 mb-3 me-3 text-center">
                                        <label class="form-label" for="fecha">Fecha</label>
                                        <input type="text" class="form-control text-center" id="fecha" value="{{$fecha_hoy}}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="numero_factura">N° de Factura</label>
                                        <input type="text" class="form-control text-center" id="numero_factura" name="numero_factura" value="{{$numero_factura}}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="text" class="form-control text-center" id="caja" value="{{$caja->caja->nombre}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6 mb-3">
                                                    <label class="form-label" for="alumno">Alumno <span class="text-danger">(*)</span></label>
                                                    <select class="form-control selectpicker @error('alumno') is-invalid @enderror" id="alumno" name="alumno" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($alumnos as $alumno)
                                                            <option value="{{$alumno->id}}" @if (old('alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('alumno')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6 mb-3">
                                                    <label class="form-label" for="cliente">Cliente <span class="text-danger">(*)</span></label>
                                                    <select class="form-control selectpicker @error('cliente') is-invalid @enderror" id="cliente" name="cliente" data-live-search="true" disabled>
                                                        <option value="" selected disabled>Seleccionar...</option>

                                                    </select>
                                                    @error('cliente')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                    <p class="mt-2 text-muted">
                                                        <span class="fw-bold">Razón Social: </span>
                                                        <span id="razon_social"></span>
                                                        <br>
                                                        <span class="fw-bold">R.U.C.: </span>
                                                        <span id="ruc"></span>
                                                        </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-5 mb-3">
                                                    <label class="form-label" for="pagos">Pagos Pendientes</label>
                                                    <select class="form-control selectpicker" id="pagos" data-live-search="true" disabled>
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-1 mb-3" style="margin-top: 30px;">
                                                    <button type="button" class="btn btn-icon btn-success" id="btn-add" disabled><i class="ri-add-fill"></i></button>
                                                </div>
                                                <div class="col-lg-5 mb-3">
                                                    <label class="form-label" for="descuento_aplicado">Aplicar Descuento</label>
                                                    <select class="form-control selectpicker" id="descuento_aplicado" data-live-search="true" disabled>
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($descuentos as $descuento)
                                                            <option value="{{ $descuento->id }}" @if (old('descuento_aplicado') == strval($descuento->id)) selected @endif>{{ $descuento->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="descuento_aplicado" id="input-descuento_aplicado">
                                                </div>
                                                <div class="col-lg-1 mb-3" style="margin-top: 30px;" id="div-btn-descuento">
                                                    <button type="button" class="btn btn-icon btn-success" id="btn-add-descuento" disabled><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 mb-3">
                                                    <div class="alert alert-danger d-none" role="alert">
                                                        <strong>Debe seleccionar un pago para poder agregar</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive table-card mt-3 mb-1">
                                                        <table class="table align-middle table-nowrap text-center" id="cobros-list">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 30%">Descripción</th>
                                                                    <th style="width: 20%">Monto Bruto</th>
                                                                    <th style="width: 20%">Descuento</th>
                                                                    <th style="width: 20%">A Pagar</th>
                                                                    <th style="width: 10%">Acción</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="a_cobrar">

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td colspan="2" style="text-align: right!important"> <b>Total a Pagar:</b></td>
                                                                    <td><span class="fw-bold" id="total_pagar">Gs. 0</span></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <input type="hidden" id="monto_total" name="monto_total">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="cobrar-btn" data-bs-toggle="modal" data-bs-target="#cobrarModal" disabled>Cobrar</button>
                        </div>
                    </div>
                </div>
                @include('cajeros.modals.create-modals')
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js')}}"></script>
        @include('cajeros.scripts.create-scripts')
    @endsection
@endcan
