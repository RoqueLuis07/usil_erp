@can('ver_articulos')
    @extends('layouts.master')
    @section('title') Ver Artículo @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Artículos @endslot
            @slot('title') Ver Artículo  @endslot
        @endcomponent

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar artículo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="codigo">Código</span></label>
                                    <input type="text" class="form-control" id="codigo" value="{{$articulo->codigo}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre">Nombre</span></label>
                                    <input type="text" class="form-control" id="nombre" value="{{$articulo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="impuesto">Impuesto</span></label>
                                    <input type="text" class="form-control text-center" id="impuesto" @if ($articulo->impuesto == 0) value="EXENTO" @elseif ($articulo->impuesto == 5) value="5 %" @elseif ($articulo->impuesto == 10) value="10 %" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="compra_venta">Compra / Venta</span></label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check compra_venta1" id="compra_venta1" @if ($articulo->compra_venta == 'COMPRA') checked @endif disabled>
                                        <label class="btn btn-outline-info" for="compra_venta1">Compra</label>
                                        <input type="radio" class="btn-check compra_venta2" id="compra_venta2" @if ($articulo->compra_venta == 'VENTA') checked @endif disabled>
                                        <label class="btn btn-outline-info" for="compra_venta2">Venta</label>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="stock">Tiene Stock ?</span></label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check stock1" id="stock1" @if ($articulo->tiene_stock == false) checked @endif disabled>
                                        <label class="btn btn-outline-danger" for="stock1">No</label>
                                        <input type="radio" class="btn-check stock2" id="stock2" @if ($articulo->tiene_stock == true) checked @endif disabled>
                                        <label class="btn btn-outline-success" for="stock2">Si</label>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3 @if ($articulo->tiene_stock == false) d-none @endif">
                                    <label class="form-label" for="stock">Stock</span></label>
                                    <input type="text" class="form-control" id="stock" value="{{number_format($articulo->stock, 0, ',', '.')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio</span></label>
                                    <input type="text" class="form-control" id="unidad_negocio" value="{{$articulo->unidadNegocio->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio</span></label>
                                    <input type="text" class="form-control" id="subunidad_negocio" value="{{$articulo->subunidadNegocio->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="centro_costo">Centro de Costo</span></label>
                                    <input type="text" class="form-control" id="centro_costo" value="{{$articulo->centroCosto->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="subcentro_costo">Subcentro de Costo</span></label>
                                    <input type="text" class="form-control" id="subcentro_costo" value="{{$articulo->subcentroCosto->nombre}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3 @if ($articulo->compra_venta == 'VENTA') d-none @endif">
                                    <label class="form-label" for="cuenta_compra">Cuenta Compra</span></label>
                                    <input type="text" class="form-control" id="cuenta_compra" @if ($articulo->cuenta_compra_id) value="{{$articulo->cuentaCompra->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center @if ($articulo->compra_venta == 'COMPRA') d-none @endif" id="div-carrera_curso">
                                    <div>
                                        <label class="form-label" for="carrera_curso">Carrera / Curso</label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check carrera_curso1" id="carrera_curso1" @if ($articulo->carrera_curso == 'CARRERA') checked @endif disabled>
                                        <label class="btn btn-outline-info" for="carrera_curso1">Carrera</label>
                                        <input type="radio" class="btn-check carrera_curso2" id="carrera_curso2" @if ($articulo->carrera_curso == 'CURSO') checked @endif disabled>
                                        <label class="btn btn-outline-info" for="carrera_curso2">Curso</label>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 @if(!$articulo->carrera) d-none @endif">
                                    <label class="form-label" for="carrera">Carrera</span></label>
                                    <input type="text" class="form-control" id="carrera" @if ($articulo->carrera) value="{{$articulo->carrera->nombre_fantasia}}" @endif readonly>
                                </div>
                                <div class="col-lg-3 mb-3 @if(!$articulo->carrera) d-none @endif">
                                    <label class="form-label" for="programa">Programa</span></label>
                                    <input type="text" class="form-control" id="programa" @if ($articulo->carrera) value="{{$articulo->carrera->programa->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-3 mb-3 @if(!$articulo->curso) d-none @endif">
                                    <label class="form-label" for="curso">Curso</span></label>
                                    <input type="text" class="form-control" id="curso" @if ($articulo->curso) value="{{$articulo->curso->nombre_fantasia}}" @endif readonly>
                                </div>
                                <div class="col-lg-3 mb-3 @if(!$articulo->curso) d-none @endif">
                                    <label class="form-label" for="tipo_curso">Tipo de Curso</span></label>
                                    <input type="text" class="form-control" id="tipo_curso" @if ($articulo->curso) value="{{$articulo->curso->tipoCurso->nombre}}" @endif readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if ($articulo->compra_venta != 'VENTA') d-none @endif">
                        <div class="col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Precios</h4>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_matricula">Precio Matrícula</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_matricula" value="{{number_format($articulo->detalle->precio_matricula, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_contado">Precio Contado</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_contado" value="{{number_format($articulo->detalle->precio_contado, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_cuota">Precio Cuotas</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_cuota" value="{{number_format($articulo->detalle->precio_cuota, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="cantidad_cuotas">Cant. Cuotas</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-center" id="cantidad_cuotas" value="{{$articulo->detalle->cantidad_cuotas}}" readonly>
                                                <span class="input-group-text">cuotas</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_defensa">Precio Defensa</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_defensa" value="{{number_format($articulo->detalle->precio_defensa, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_titulo">Precio Título</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_titulo" value="{{number_format($articulo->detalle->precio_titulo, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_certificado">Precio Certificado</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_certificado" value="{{number_format($articulo->detalle->precio_certificado, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_multa">Multa x Día</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_multa" value="{{number_format($articulo->detalle->precio_multa, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_examen_suficiencia">Precio Examen Suf.</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_examen_suficiencia" value="{{number_format($articulo->detalle->precio_examen_suficiencia, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_constancia_carrera">Precio Constancia</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control text-center" id="precio_constancia_carrera" value="{{number_format($articulo->detalle->precio_constancia_carrera, 0, ',', '.')}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h4 class="card-title mb-3">Vencimientos</h4>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="fecha_vencimiento_primera_cuota">Venc. 1ra Cuota</label>
                                            <input type="text" class="form-control text-center" id="fecha_vencimiento_primera_cuota" value="{{Carbon\Carbon::parse($articulo->detalle->fecha_vencimiento_primera_cuota)->format('d/m/Y')}}" readonly>
                                        </div>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="dia_vencimiento_cuotas">Día Venc. Cuotas</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Día</span>
                                                <input type="text" class="form-control text-center" id="dia_vencimiento_cuotas" value="{{$articulo->detalle->dia_vencimiento_cuotas}}" readonly>
                                                <span class="input-group-text">del mes</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="dias_gracia">Días de Gracia</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-center" id="dias_gracia" value="{{$articulo->detalle->dias_gracia}}" readonly>
                                                <span class="input-group-text">días</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Cuentas Contables</h4>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_matricula">Cuenta Matrícula</label>
                                            <input type="text" class="form-control" id="cuenta_matricula" @if ($articulo->detalle->cuenta_matricula_id) value="{{$articulo->detalle->cuentaMatricula->nombre}} - {{$articulo->detalle->cuentaMatricula->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_contado">Cuenta Contado</label>
                                            <input type="text" class="form-control" id="cuenta_contado" @if ($articulo->detalle->cuenta_contado_id) value="{{$articulo->detalle->cuentaContado->nombre}} - {{$articulo->detalle->cuentaContado->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_cuota">Cuenta Cuotas</label>
                                            <input type="text" class="form-control" id="cuenta_cuota" @if ($articulo->detalle->cuenta_cuota_id) value="{{$articulo->detalle->cuentaCuota->nombre}} - {{$articulo->detalle->cuentaCuota->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_descuento">Cuenta Descuentos</label>
                                            <input type="text" class="form-control" id="cuenta_descuento" @if ($articulo->detalle->cuenta_descuento_id) value="{{$articulo->detalle->cuentaDescuento->nombre}} - {{$articulo->detalle->cuentaDescuento->cuenta}}" @endif readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_defensa">Cuenta Defensa</label>
                                            <input type="text" class="form-control" id="cuenta_defensa" @if ($articulo->detalle->cuenta_defensa_id) value="{{$articulo->detalle->cuentaDefensa->nombre}} - {{$articulo->detalle->cuentaDefensa->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_titulo">Cuenta Título</label>
                                            <input type="text" class="form-control" id="cuenta_titulo" @if ($articulo->detalle->cuenta_titulo_id) value="{{$articulo->detalle->cuentaTitulo->nombre}} - {{$articulo->detalle->cuentaTitulo->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_certificado">Cuenta Certificado</label>
                                            <input type="text" class="form-control" id="cuenta_certificado" @if ($articulo->detalle->cuenta_certificado_id) value="{{$articulo->detalle->cuentaCertificado->nombre}} - {{$articulo->detalle->cuentaCertificado->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_multa">Cuenta Multa</label>
                                            <input type="text" class="form-control" id="cuenta_multa" @if ($articulo->detalle->cuenta_multa_id) value="{{$articulo->detalle->cuentaMulta->nombre}} - {{$articulo->detalle->cuentaMulta->cuenta}}" @endif readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_examen_suficiencia">Cuenta Examen Suf.</label>
                                            <input type="text" class="form-control" id="cuenta_examen_suficiencia" @if ($articulo->detalle->cuenta_examen_suficiencia_id) value="{{$articulo->detalle->cuentaExamenSuficiencia->nombre}} - {{$articulo->detalle->cuentaExamenSuficiencia->cuenta}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_constancia_carrera">Cuenta Constancia</label>
                                            <input type="text" class="form-control" id="cuenta_constancia_carrera" @if ($articulo->detalle->cuenta_constancia_carrera_id) value="{{$articulo->detalle->cuentaConstanciaCarrera->nombre}} - {{$articulo->detalle->cuentaConstanciaCarrera->cuenta}}" @endif readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('articulos.index')}}">Volver</a>
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
