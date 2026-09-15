 @can('editar_cuentas_contables')
    @extends('layouts.master')
    @section('title') Editar Cuenta Contable @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Cuentas Contables @endslot
            @slot('title') Editar Cuenta Contable  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('cuentas_contables.update', $cuenta_contable->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar cuenta contable</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cuenta">N° de Cuenta <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('cuenta') is-invalid @enderror" id="cuenta" name="cuenta" value="{{old('cuenta', $cuenta_contable->cuenta)}}">
                                    @error('cuenta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre', $cuenta_contable->nombre)}}" placeholder="Escriba el nombre">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="padre">Cuenta Padre</label>
                                    <select class="selectpicker form-control @error('padre') is-invalid @enderror" id="padre" name="padre" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_contables as $cc)
                                            <option value="{{$cc->id}}" @if (old('padre') == strval($cc->id) || $cuenta_contable->padre_id == strval($cc->id)) selected @endif data-subtext="{{$cc->cuenta}}">{{$cc->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('padre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="imputable">Imputable ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('imputable') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check imputable1" id="imputable1" name="imputable" value="false" @if (old('imputable') == 'false' || $cuenta_contable->imputable == false) checked @endif>
                                        <label class="btn btn-outline-danger" for="imputable1">No</label>
                                        <input type="radio" class="btn-check imputable2" id="imputable2" name="imputable" value="true" @if (old('imputable') == 'true' || $cuenta_contable->imputable == true) checked @endif>
                                        <label class="btn btn-outline-success" for="imputable2">Si</label>
                                    </div>
                                    @error('imputable')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Guardar</button>
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
        @include('cuentas_contables.scripts.edit-scripts')
    @endsection
@endcan
