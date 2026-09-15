@can('editar_convenios')
    @extends('layouts.master')
    @section('title') Editar Convenio @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Convenios @endslot
            @slot('title') Editar Convenio  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('convenios.update', $convenio->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar convenio</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre', $convenio->nombre)}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label">Tipo <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('tipo') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check tipo1" id="tipo1" name="tipo" value="CO" @if (old('tipo') == 'CO' || ($convenio->tipo == 'CO' && old('tipo') == null)) checked @endif>
                                        <label class="btn btn-outline-primary" for="tipo1">Convenio</label>
                                        <input type="radio" class="btn-check tipo2" id="tipo2" name="tipo" value="DE" @if (old('tipo') == 'DE' || ($convenio->tipo == 'DE' && old('tipo') == null)) checked @endif>
                                        <label class="btn btn-outline-primary" for="tipo2">Descuento</label>
                                    </div>
                                    @error('tipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if (old('tipo') != 'CO' && ($convenio->tipo != 'CO' || old('tipo') == 'DE')) d-none @endif" id='div-convenio'>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Detalles del Convenio</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="text-center">Matrícula</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_matricula">Tipo</label>
                                        <select class="form-control selectpicker @error('tipo_matricula') is-invalid @enderror" id="tipo_matricula" name="tipo_matricula" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="VA" @if (old('tipo_matricula') == 'VA' || ($convenio->detalle->tipo_matricula == 'VA' && old('tipo_matricula') == null)) selected @endif>VALOR FIJO</option>
                                            <option value="PO" @if (old('tipo_matricula') == 'PO' || ($convenio->detalle->tipo_matricula == 'PO' && old('tipo_matricula') == null)) selected @endif>PORCENTAJE</option>
                                        </select>
                                        @error('tipo_matricula')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_matricula') != 'VA' && ($convenio->detalle->tipo_matricula != 'VA' || old('tipo_matricula') == 'PO')) d-none @endif" id="div-descuento-matricula">
                                        <label class="form-label" for="descuento_matricula">Valor Fijo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_matricula') is-invalid @enderror" id="descuento_matricula" name="descuento_matricula" value="{{old('descuento_matricula', $convenio->detalle->descuento_matricula)}}">
                                            @error('descuento_matricula')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_matricula') != 'PO' && ($convenio->detalle->tipo_matricula != 'PO' || old('tipo_matricula') == 'VA')) d-none @endif" id="div-porcentaje-matricula">
                                        <label class="form-label" for="porcentaje_matricula">Porcentaje</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_matricula') is-invalid @enderror" id="porcentaje_matricula" name="porcentaje_matricula" value="{{old('porcentaje_matricula', $convenio->detalle->porcentaje_matricula)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_matricula')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <h6 class="text-center">Contado</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_contado">Tipo</label>
                                        <select class="form-control selectpicker @error('tipo_contado') is-invalid @enderror" id="tipo_contado" name="tipo_contado" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="VA" @if (old('tipo_contado') == 'VA' || ($convenio->detalle->tipo_contado == 'VA' && old('tipo_contado') == null)) selected @endif>VALOR FIJO</option>
                                            <option value="PO" @if (old('tipo_contado') == 'PO' || ($convenio->detalle->tipo_contado == 'PO' && old('tipo_contado') == null)) selected @endif>PORCENTAJE</option>
                                        </select>
                                        @error('tipo_contado')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_contado') != 'VA' && ($convenio->detalle->tipo_contado != 'VA' || old('tipo_contado') == 'PO')) d-none @endif" id="div-descuento-contado">
                                        <label class="form-label" for="descuento_contado">Valor Fijo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_contado') is-invalid @enderror" id="descuento_contado" name="descuento_contado" value="{{old('descuento_contado', $convenio->detalle->descuento_contado)}}">
                                            @error('descuento_contado')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_contado') != 'PO' && ($convenio->detalle->tipo_contado != 'PO' || old('tipo_contado') == 'VA')) d-none @endif" id="div-porcentaje-contado">
                                        <label class="form-label" for="porcentaje_contado">Porcentaje</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_contado') is-invalid @enderror" id="porcentaje_contado" name="porcentaje_contado" value="{{old('porcentaje_contado', $convenio->detalle->porcentaje_contado)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_contado')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <h6 class="text-center">Cuotas</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_cuotas">Tipo Cuotas</label>
                                        <select class="form-control selectpicker @error('tipo_cuotas') is-invalid @enderror" id="tipo_cuotas" name="tipo_cuotas" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="VA" @if (old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) selected @endif>VALOR FIJO</option>
                                            <option value="PO" @if (old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) selected @endif>PORCENTAJE</option>
                                        </select>
                                        @error('tipo_cuotas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="aplica_a_cuotas">Aplicar a</label>
                                        <select class="form-control selectpicker @error('aplica_a_cuotas') is-invalid @enderror" id="aplica_a_cuotas" name="aplica_a_cuotas" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="1C" @if (old('aplica_a_cuotas') == '1C' || ($convenio->detalle->aplica_a_cuotas == '1C' && old('aplica_a_cuotas') == null)) selected @endif>PRIMERA CUOTA</option>
                                            <option value="CO" @if (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)) selected @endif>COMBINADO</option>
                                            <option value="TO" @if (old('aplica_a_cuotas') == 'TO' || ($convenio->detalle->aplica_a_cuotas == 'TO' && old('aplica_a_cuotas') == null)) selected @endif>TODAS LAS CUOTAS</option>
                                        </select>
                                        @error('aplica_a_cuotas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || $convenio->detalle->tipo_cuotas == 'VA') && (old('aplica_a_cuotas') == 'TO' || $convenio->detalle->aplica_a_cuotas == 'TO'))) d-none @endif" id="div-descuento-cuotas">
                                        <label class="form-label" for="descuento_cuotas">Valor Fijo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuotas') is-invalid @enderror" id="descuento_cuotas" name="descuento_cuotas" value="{{old('descuento_cuotas', $convenio->detalle->descuento_cuotas)}}">
                                            @error('descuento_cuotas')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || $convenio->detalle->tipo_cuotas == 'PO') && (old('aplica_a_cuotas') == 'TO' || $convenio->detalle->aplica_a_cuotas == 'TO'))) d-none @endif" id="div-porcentaje-cuotas">
                                        <label class="form-label" for="porcentaje_cuotas">Porcentaje</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuotas') is-invalid @enderror" id="porcentaje_cuotas" name="porcentaje_cuotas" value="{{old('porcentaje_cuotas', $convenio->detalle->porcentaje_cuotas)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuotas')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == '1C' || old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == '1C' && old('aplica_a_cuotas') == null) || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-descuento-cuota-1">
                                        <label class="form-label" for="descuento_cuota_1">Valor Fijo Cuota 1</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuota_1') is-invalid @enderror @error('descuentos') is-invalid @enderror" id="descuento_cuota_1" name="descuento_cuota_1" value="{{old('descuento_cuota_1', $convenio->detalle->descuento_cuota_1)}}">
                                            @error('descuento_cuota_1')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == '1C' || old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == '1C' && old('aplica_a_cuotas') == null) || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-porcentaje-cuota-1">
                                        <label class="form-label" for="porcentaje_cuota_1">Porcentaje Cuota 1</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuota_1') is-invalid @enderror @error('porcentajes') is-invalid @enderror" id="porcentaje_cuota_1" name="porcentaje_cuota_1" value="{{old('porcentaje_cuota_1', $convenio->detalle->porcentaje_cuota_1)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuota_1')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-descuento-cuota-2">
                                        <label class="form-label" for="descuento_cuota_2">Valor Fijo Cuota 2</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuota_2') is-invalid @enderror @error('descuentos') is-invalid @enderror" id="descuento_cuota_2" name="descuento_cuota_2" value="{{old('descuento_cuota_2', $convenio->detalle->descuento_cuota_2)}}">
                                            @error('descuento_cuota_2')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-porcentaje-cuota-2">
                                        <label class="form-label" for="porcentaje_cuota_2">Porcentaje Cuota 2</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuota_2') is-invalid @enderror @error('porcentajes') is-invalid @enderror" id="porcentaje_cuota_2" name="porcentaje_cuota_2" value="{{old('porcentaje_cuota_2', $convenio->detalle->porcentaje_cuota_2)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuota_2')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-descuento-cuota-3">
                                        <label class="form-label" for="descuento_cuota_3">Valor Fijo Cuota 3</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuota_3') is-invalid @enderror @error('descuentos') is-invalid @enderror" id="descuento_cuota_3" name="descuento_cuota_3" value="{{old('descuento_cuota_3', $convenio->detalle->descuento_cuota_3)}}">
                                            @error('descuento_cuota_3')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-porcentaje-cuota-3">
                                        <label class="form-label" for="porcentaje_cuota_3">Porcentaje Cuota 3</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuota_3') is-invalid @enderror @error('porcentajes') is-invalid @enderror" id="porcentaje_cuota_3" name="porcentaje_cuota_3" value="{{old('porcentaje_cuota_3', $convenio->detalle->porcentaje_cuota_3)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuota_3')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-descuento-cuota-4">
                                        <label class="form-label" for="descuento_cuota_4">Valor Fijo Cuota 4</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuota_4') is-invalid @enderror @error('descuentos') is-invalid @enderror" id="descuento_cuota_4" name="descuento_cuota_4" value="{{old('descuento_cuota_4', $convenio->detalle->descuento_cuota_4)}}">
                                            @error('descuento_cuota_4')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-porcentaje-cuota-4">
                                        <label class="form-label" for="porcentaje_cuota_4">Porcentaje Cuota 4</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuota_4') is-invalid @enderror @error('porcentajes') is-invalid @enderror" id="porcentaje_cuota_4" name="porcentaje_cuota_4" value="{{old('porcentaje_cuota_4', $convenio->detalle->porcentaje_cuota_4)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuota_4')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'VA' || ($convenio->detalle->tipo_cuotas == 'VA' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-descuento-cuota-5">
                                        <label class="form-label" for="descuento_cuota_5">Valor Fijo Cuota 5</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('descuento_cuota_5') is-invalid @enderror @error('descuentos') is-invalid @enderror" id="descuento_cuota_5" name="descuento_cuota_5" value="{{old('descuento_cuota_5', $convenio->detalle->descuento_cuota_5)}}">
                                            @error('descuento_cuota_5')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (!((old('tipo_cuotas') == 'PO' || ($convenio->detalle->tipo_cuotas == 'PO' && old('tipo_cuotas') == null)) && (old('aplica_a_cuotas') == 'CO' || ($convenio->detalle->aplica_a_cuotas == 'CO' && old('aplica_a_cuotas') == null)))) d-none @endif" id="div-porcentaje-cuota-5">
                                        <label class="form-label" for="porcentaje_cuota_5">Porcentaje Cuota 5</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_cuota_5') is-invalid @enderror @error('porcentajes') is-invalid @enderror" id="porcentaje_cuota_5" name="porcentaje_cuota_5" value="{{old('porcentaje_cuota_5', $convenio->detalle->porcentaje_cuota_5)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_cuota_5')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @error('cuotas')
                                        <span class="text-danger fw-bold" role="alert">
                                            <small>Debe completar algún campo de cuotas.</small>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if (old('tipo') != 'DE' && ($convenio->tipo != 'DE' || old('tipo') == 'CO')) d-none @endif" id='div-descuento'>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Detalles del Convenio</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="text-center">Descuentos</h6>
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="tipo_descuento">Tipo</label>
                                        <select class="form-control selectpicker @error('tipo_descuento') is-invalid @enderror" id="tipo_descuento" name="tipo_descuento" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="VA" @if (old('tipo_descuento') == 'VA' || ($convenio->detalle->tipo_descuento == 'VA' && old('tipo_descuento') == null)) selected @endif>VALOR FIJO</option>
                                            <option value="PO" @if (old('tipo_descuento') == 'PO' || ($convenio->detalle->tipo_descuento == 'PO' && old('tipo_descuento') == null)) selected @endif>PORCENTAJE</option>
                                        </select>
                                        @error('tipo_descuento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_descuento') != 'VA' && ($convenio->detalle->tipo_descuento != 'VA' || old('tipo_descuento') == 'PO')) d-none @endif" id="div-descuento-matricula">
                                        <label class="form-label" for="precio_descuento">Valor Fijo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Gs.</span>
                                            <input type="text" class="form-control text-center @error('precio_descuento') is-invalid @enderror" id="precio_descuento" name="precio_descuento" value="{{old('precio_descuento', $convenio->detalle->precio_descuento)}}">
                                            @error('precio_descuento')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-3 text-center @if (old('tipo_descuento') != 'PO' && ($convenio->detalle->tipo_descuento != 'PO' || old('tipo_descuento') == 'VA')) d-none @endif" id="div-porcentaje-matricula">
                                        <label class="form-label" for="porcentaje_descuento">Porcentaje</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center porcentaje @error('porcentaje_descuento') is-invalid @enderror" id="porcentaje_descuento" name="porcentaje_descuento" value="{{old('porcentaje_descuento', $convenio->detalle->porcentaje_descuento)}}">
                                            <span class="input-group-text">%</span>
                                            @error('porcentaje_descuento')
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
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
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
        @include('convenios.scripts.edit-scripts')
    @endsection
@endcan
