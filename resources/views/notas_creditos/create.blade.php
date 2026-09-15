@can('crear_notas_creditos')
    @extends('layouts.master')
    @section('title') Agregar Nota de Crédito @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Notas de Crédito @endslot
            @slot('title') Agregar Nota de Crédito  @endslot
        @endcomponent

        @include('notas_creditos.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('notas_creditos.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva nota de crédito</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{$fecha_hoy}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="numero_nota_credito">N° Nota de Crédito</label>
                                    <input type="text" class="form-control text-center" id="numero_nota_credito" name="numero_nota_credito" value="{{$numero_nota_credito}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="aplica_a">Aplicar a <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('aplica_a') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check aplica_a1" id="aplica_a1" name="aplica_a" value="TOTAL" @if (old('aplica_a') == 'TOTAL') checked @endif>
                                        <label class="btn btn-outline-info" for="aplica_a1">Total</label>
                                        <input type="radio" class="btn-check aplica_a2" id="aplica_a2" name="aplica_a" value="SALDO" @if (old('aplica_a') == 'SALDO') checked @endif>
                                        <label class="btn btn-outline-info" for="aplica_a2">Saldo</label>
                                    </div>
                                    @error('aplica_a')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="reversar_pago">Reversar Pago ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('reversar_pago') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check reversar_pago1" id="reversar_pago1" name="reversar_pago" value="false" @if (old('reversar_pago') == 'false') checked @endif>
                                        <label class="btn btn-outline-danger" for="reversar_pago1">No</label>
                                        <input type="radio" class="btn-check reversar_pago2" id="reversar_pago2" name="reversar_pago" value="true" @if (old('reversar_pago') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="reversar_pago2">Si</label>
                                    </div>
                                    @error('reversar_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="cliente">Cliente <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cliente') is-invalid @enderror" id="cliente" name="cliente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{$cliente->id}}" @if (old('cliente') == strval($cliente->id)) selected @endif data-subtext="{{$cliente->numero_documento}}">{{$cliente->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="venta">Factura de Venta <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('venta') is-invalid @enderror" id="venta" name="venta" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="monto_venta">Monto de Factura</label>
                                    <input type="text" class="form-control text-center" id="monto_venta" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="descripcion">Descripción</label>
                                    <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{old('descripcion')}}" placeholder="Inserte una descrición personaliada si lo requiere" maxlength="200">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('notas_creditos.scripts.create-scripts')
    @endsection
@endcan
