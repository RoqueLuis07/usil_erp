@can('ver_convenios')
    @extends('layouts.master')
    @section('title') Ver Convenio @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Convenios @endslot
            @slot('title') Ver Convenio  @endslot
        @endcomponent

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar convenio</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre">Nombre</span></label>
                                    <input type="text" class="form-control" id="nombre" value="{{$convenio->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label">Tipo</span></label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check tipo1" id="tipo1" @if ($convenio->tipo == 'CO') checked @endif disabled>
                                        <label class="btn btn-outline-primary" for="tipo1">Convenio</label>
                                        <input type="radio" class="btn-check tipo2" id="tipo2" @if ($convenio->tipo == 'DE') checked @endif disabled>
                                        <label class="btn btn-outline-primary" for="tipo2">Descuento</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if ($convenio->tipo == 'DE') d-none @endif">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Detalles del Convenio</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="text-center">Matrícula</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_matricula">Tipo</label>
                                        <input type="text" class="form-control text-center" id="tipo_matricula" @if ($convenio->detalle->tipo_matricula == 'VA') value="VALOR FIJO" @elseif($convenio->detalle->tipo_matricula == 'PO') value="PORCENTAJE" @endif readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="valor_matricula">@if ($convenio->detalle->tipo_matricula == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_matricula == 'PO') Porcentaje @endif</label>
                                        <div class="input-group">
                                            @if ($convenio->detalle->tipo_matricula == 'VA')
                                                <span class="input-group-text">Gs.</span>
                                            @endif
                                            <input type="text" class="form-control text-center" id="valor_matricula" @if ($convenio->detalle->tipo_matricula == 'VA') value="{{ $convenio->detalle->descuento_matricula }}" @elseif($convenio->detalle->tipo_matricula == 'PO') value="{{ $convenio->detalle->porcentaje_matricula }}" @endif readonly>
                                            @if ($convenio->detalle->tipo_matricula == 'PO')
                                                <span class="input-group-text">%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <h6 class="text-center">Contado</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_contado">Tipo</label>
                                        <input type="text" class="form-control text-center" id="tipo_contado" @if ($convenio->detalle->tipo_contado == 'VA') value="VALOR FIJO" @elseif($convenio->detalle->tipo_contado == 'PO') value="PORCENTAJE" @endif readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="valor_contado">@if ($convenio->detalle->tipo_contado == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_contado == 'PO') Porcentaje @endif</label>
                                        <div class="input-group">
                                            @if ($convenio->detalle->tipo_contado == 'VA')
                                                <span class="input-group-text">Gs.</span>
                                            @endif
                                            <input type="text" class="form-control text-center" id="valor_contado" @if ($convenio->detalle->tipo_contado == 'VA') value="{{ $convenio->detalle->descuento_contado }}" @elseif($convenio->detalle->tipo_contado == 'PO') value="{{ $convenio->detalle->porcentaje_contado }}" @endif readonly>
                                            @if ($convenio->detalle->tipo_contado == 'PO')
                                                <span class="input-group-text">%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <h6 class="text-center">Cuotas</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_cuotas">Tipo</label>
                                        <input type="text" class="form-control text-center" id="tipo_cuotas" @if ($convenio->detalle->tipo_cuotas == 'VA') value="VALOR FIJO" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="PORCENTAJE" @endif readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="aplica_a_cuotas">Aplica a</label>
                                        <input type="text" class="form-control text-center" id="aplica_a_cuotas" @if ($convenio->detalle->aplica_a_cuotas == '1C') value="PRIMERA CUOTA" @elseif($convenio->detalle->aplica_a_cuotas == 'CO') value="COMBINADO" @elseif($convenio->detalle->aplica_a_cuotas == 'TO') value="TODAS LAS CUOTAS" @endif readonly>
                                        @error('aplica_a_cuotas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    @if ($convenio->detalle->aplica_a_cuotas == 'TO')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuotas">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuotas" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuotas }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuotas }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if ($convenio->detalle->aplica_a_cuotas == '1C' || $convenio->detalle->aplica_a_cuotas == 'CO')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuota_1">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 1</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuota_1" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuota_1 }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuota_1 }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if ($convenio->detalle->aplica_a_cuotas == 'CO')
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuota_2">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 2</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuota_2" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuota_2 }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuota_2 }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuota_3">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 3</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuota_3" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuota_3 }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuota_3 }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuota_4">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 4</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuota_4" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuota_4 }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuota_4 }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="descuento_cuota_5">@if ($convenio->detalle->tipo_cuotas == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_cuotas == 'PO') Porcentaje @endif Cuota 5</label>
                                            <div class="input-group">
                                                @if ($convenio->detalle->tipo_cuotas == 'VA')
                                                    <span class="input-group-text">Gs.</span>
                                                @endif
                                                <input type="text" class="form-control text-center" id="descuento_cuota_5" @if ($convenio->detalle->tipo_cuotas == 'VA') value="{{ $convenio->detalle->descuento_cuota_5 }}" @elseif($convenio->detalle->tipo_cuotas == 'PO') value="{{ $convenio->detalle->porcentaje_cuota_5 }}" @endif readonly>
                                                @if ($convenio->detalle->tipo_cuotas == 'PO')
                                                    <span class="input-group-text">%</span>                                                    
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if ($convenio->tipo == 'CO') d-none @endif">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Detalles del Convenio</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="text-center">Descuentos</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_descuento">Tipo</label>
                                        <input type="text" class="form-control text-center" id="tipo_descuento" @if ($convenio->detalle->tipo_descuento == 'VA') value="VALOR FIJO" @elseif($convenio->detalle->tipo_descuento == 'PO') value="PORCENTAJE" @endif readonly>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="precio_descuento">@if ($convenio->detalle->tipo_descuento == 'VA') Valor Fijo @elseif($convenio->detalle->tipo_descuento == 'PO') Porcentaje @endif</label>
                                        <div class="input-group">
                                            @if ($convenio->detalle->tipo_descuento == 'VA')
                                                <span class="input-group-text">Gs.</span>
                                            @endif
                                            <input type="text" class="form-control text-center" id="precio_descuento" @if ($convenio->detalle->tipo_descuento == 'VA') value="{{ $convenio->detalle->precio_descuento }}" @elseif($convenio->detalle->tipo_descuento == 'PO') value="{{ $convenio->detalle->porcentaje_descuento }}" @endif readonly>
                                            @if ($convenio->detalle->tipo_descuento == 'PO')
                                                <span class="input-group-text">%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('convenios.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
