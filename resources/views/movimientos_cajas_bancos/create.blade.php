@can('crear_cajas_cuentas_movimientos')
    @extends('layouts.master')
    @section('title') Agregar Movimiento entre Caja y Cuenta Bancaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Movimientos entre Cajas y Cuentas Bancarias @endslot
            @slot('title') Agregar Movimiento entre Caja y Cuenta Bancaria  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('movimientos_cajas_bancos.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo movimiento entre caja y cuenta bancaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tipo_movimiento">Tipo de Mov. <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_movimiento') is-invalid @enderror" id="tipo_movimiento" name="tipo_movimiento" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_movimientos as $tipo_movimiento)
                                            <option value="{{$tipo_movimiento->id}}" @if(old('tipo_movimiento') == strval($tipo_movimiento->id)) selected @endif>{{$tipo_movimiento->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_movimiento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="caja">Caja <span class="text-danger obg-cuenta-bancaria-origen">(*)</span></label>
                                    <select class="selectpicker form-control @error('caja') is-invalid @enderror" id="caja" name="caja" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cajas as $caja)
                                            <option value="{{$caja->id}}" @if (old('caja') == strval($caja->id)) selected @endif>{{$caja->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('caja')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3 text-center" style="margin-top: 35px;">
                                    <input type="hidden" id="sentido" name="sentido">
                                    <h3 id="icon-sentido">
                                        @if (old('sentido') == 'I')
                                            <i class="ri-arrow-left-line"></i>
                                        @elseif (old('sentido') == 'R')
                                            <i class="ri-arrow-right-line"></i>
                                        @endif
                                    </h3>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('cuenta_bancaria') is-invalid @enderror" id="cuenta_bancaria" name="cuenta_bancaria" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_bancarias as $cuenta_bancaria)
                                            <option value="{{$cuenta_bancaria->id}}" @if (old('cuenta_bancaria') == strval($cuenta_bancaria->id)) selected @endif data-subtext="{{$cuenta_bancaria->numero_cuenta}}">{{$cuenta_bancaria->banco->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_bancaria')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control" id="fecha" value="{{Carbon\Carbon::now()->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-5 mb-3">
                                    <label class="form-label" for="monto">Monto <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('monto') is-invalid @enderror" id="monto" name="monto" placeholder="0" value="{{old('monto')}}">
                                    @error('monto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="moneda">Moneda</label>
                                    <input type="text" class="form-control" id="moneda" name="moneda" value="{{old('moneda')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="motivo">Motivo <span class="text-danger">(*)</span></label>
                                    <textarea class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" cols="30" rows="5" placeholder="Describe por qué realizas este movimiento">{{old('motivo')}}</textarea>
                                    @error('motivo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('movimientos_cajas_bancos.scripts.create-scripts')
    @endsection
@endcan
