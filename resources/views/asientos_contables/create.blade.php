@can('crear_asientos_contables')
    @extends('layouts.master')
    @section('title') Agregar Asiento Contable @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Asientos Contabless @endslot
            @slot('title') Agregar Asiento Contable  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('asientos_contables.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Nuevo asiento contable</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="id">ID</label>
                                    <input type="text" class="form-control text-center" id="id" value="{{$id_asiento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_asiento">N° Asiento</label>
                                    <input type="text" class="form-control text-center" id="numero_asiento" value="{{$numero_asiento}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="origen">Origen</label>
                                    <input type="text" class="form-control" id="origen" value="MANUAL" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="moneda">Moneda</label>
                                    <input type="text" class="form-control" id="moneda" value="GUARANIES" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('unidad_negocio') is-invalid @enderror" id="unidad_negocio" name="unidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($unidades_negocios as $unidad_negocio)
                                            <option value="{{$unidad_negocio->id}}" @if (old('unidad_negocio') == strval($unidad_negocio->id)) selected @endif>{{$unidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('subunidad_negocio') is-invalid @enderror" id="subunidad_negocio" name="subunidad_negocio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header mb-0">
                                    <h4 class="card-title">Detalles</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row">
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label label-numero" for="numero-0">N°</label>
                                                <input type="text" class="form-control text-center" id="numero-0" value="1" readonly>
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label label-cuenta_contable" for="cuenta_contable-0">Cuenta Contable</label>
                                                <select class="selectpicker form-control cuenta_contable cuenta_contable-0 @error('detalles.0.cuenta_contable') is-invalid @enderror" id="cuenta_contable-0" name="detalles[0][cuenta_contable]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($cuentas_contables as $cuenta_contable)
                                                        <option value="{{$cuenta_contable->id}}" @if (old('cuenta_contable') == strval($cuenta_contable->id)) selected @endif data-subtext="{{$cuenta_contable->cuenta}}">{{$cuenta_contable->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.cuenta_contable')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label" for="descripcion">Descripción</label>
                                                <textarea class="form-control text-center descripcion @error('detalles.0.descripcion') is-invalid @enderror" id="descripcion-0" name="detalles[0][descripcion]" cols="30" rows="3" data-id="0">{{old('detalles.0.descripcion')}}</textarea>
                                                @error('detalles.0.descripcion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label label-centro_costo" for="centro_costo-0">CC1</label>
                                                <select class="selectpicker form-control centro_costo centro_costo-0 @error('detalles.0.centro_costo') is-invalid @enderror" id="centro_costo-0" name="detalles[0][centro_costo]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($centros_costos as $centro_costo)
                                                        <option value="{{$centro_costo->id}}" @if (old('centro_costo') == strval($centro_costo->id)) selected @endif>{{$centro_costo->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.centro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                <label class="form-label label-subcentro_costo" for="subcentro_costo-0">CC2</label>
                                                <select class="selectpicker form-control subcentro_costo subcentro_costo-0 @error('detalles.0.subcentro_costo') is-invalid @enderror" id="subcentro_costo-0" name="detalles[0][subcentro_costo]" data-live-search="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    
                                                </select>
                                                @error('detalles.0.subcentro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label label-debe" for="debe-0">Debe</label>
                                                <input type="text" class="form-control text-center debe-0 debe @error('detalles.0.debe') is-invalid @enderror" id="debe-0" name="detalles[0][debe]" placeholder="0" data-id="0">
                                                @error('detalles.0.debe')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                <label class="form-label label-haber" for="haber-0">Haber</label>
                                                <input type="text" class="form-control text-center haber-0 haber @error('detalles.0.haber') is-invalid @enderror" id="haber-0" name="detalles[0][haber]" placeholder="0" data-id="0">
                                                @error('detalles.0.haber')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-2 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="asiento-fila">

                                    </div>
                                    <div class="row d-flex-flex-wrap justify-content-end">
                                        <div class="col-lg-1 mb-3 text-center">
                                            <label class="form-label" for="diferencia">Diferencia</label>
                                            <input type="text" class="form-control text-center @error('diferencia') is-invalid @enderror" id="diferencia" value="{{old('diferencia', 0)}}" readonly>
                                            <input type="hidden" id="diferencia_send" name="diferencia" value="{{old('diferencia')}}">
                                            @error('diferencia')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-success me-2" id="save-btn">Guardar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('asientos_contables.scripts.create-scripts')
        @include('asientos_contables.scripts.create-detalles-scripts')
    @endsection
@endcan
