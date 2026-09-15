@can('crear_cajas_movimientos')
    @extends('layouts.master')
    @section('title') Agregar Movimiento de Caja @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Movimientos de Cajas @endslot
            @slot('title') Agregar Movimiento de Caja  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('movimientos_cajas.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo movimiento de caja</h4>
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
                                <div class="col-lg-5 mb-3">
                                    <label class="form-label" for="caja_origen">Caja Origen <span class="text-danger obg-caja-origen">(*)</span></label>
                                    <select class="selectpicker form-control @error('caja_origen') is-invalid @enderror" id="caja_origen" name="caja_origen" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('caja_origen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-5 mb-3">
                                    <label class="form-label" for="caja_destino">Caja Destino <span class="text-danger obg-caja-destino">(*)</span></label>
                                    <select class="selectpicker form-control @error('caja_destino') is-invalid @enderror" id="caja_destino" name="caja_destino" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('caja_destino')
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
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="monto">Monto <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('monto') is-invalid @enderror" id="monto" name="monto" placeholder="0" value="{{old('monto')}}">
                                    @error('monto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
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
        @include('movimientos_cajas.scripts.create-scripts')
    @endsection
@endcan
