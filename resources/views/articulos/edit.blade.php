 @can('editar_articulos')
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
            <form action="{{route('articulos.update', $articulo->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo artículo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="codigo">Código <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{old('codigo', $articulo->codigo)}}">
                                    @error('codigo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{old('nombre', $articulo->nombre)}}">
                                    @error('nombre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="impuesto">Impuesto <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('impuesto') is-invalid @enderror" id="impuesto" name="impuesto" data-live-search="true">
                                        <option value="" disabled>Seleccionar...</option>
                                        <option value="0" @if ($articulo->impuesto == 0) selected @endif>EXENTO</option>
                                        <option value="5" @if ($articulo->impuesto == 5) selected @endif>5 %</option>
                                        <option value="10" @if ($articulo->impuesto == 10) selected @endif>10 %</option>
                                    </select>
                                    @error('impuesto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="compra_venta">Compra / Venta <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('compra_venta') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check compra_venta1" id="compra_venta1" name="compra_venta" value="compra" @if (old('compra_venta') == 'compra' || $articulo->compra_venta == 'COMPRA') checked @endif>
                                        <label class="btn btn-outline-info" for="compra_venta1">Compra</label>
                                        <input type="radio" class="btn-check compra_venta2" id="compra_venta2" name="compra_venta" value="venta" @if (old('compra_venta') == 'venta' || $articulo->compra_venta == 'VENTA') checked @endif>
                                        <label class="btn btn-outline-info" for="compra_venta2">Venta</label>
                                    </div>
                                    @error('compra_venta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="stock">Tiene Stock ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('stock') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check stock1" id="stock1" name="stock" value="false" @if (old('stock') == 'false' || $articulo->tiene_stock == false) checked @endif>
                                        <label class="btn btn-outline-danger" for="stock1">No</label>
                                        <input type="radio" class="btn-check stock2" id="stock2" name="stock" value="true" @if (old('stock') == 'true' || $articulo->tiene_stock == true) checked @endif>
                                        <label class="btn btn-outline-success" for="stock2">Si</label>
                                    </div>
                                    @error('stock')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('unidad_negocio') is-invalid @enderror" id="unidad_negocio" name="unidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($unidades_negocios as $unidad_negocio)
                                            <option value="{{$unidad_negocio->id}}" @if (old('unidad_negocio') == strval($unidad_negocio->id) || $articulo->unidad_id == strval($unidad_negocio->id)) selected @endif>{{$unidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('subunidad_negocio') is-invalid @enderror" id="subunidad_negocio" name="subunidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($subunidades_negocios as $subunidad_negocio)
                                            <option value="{{$subunidad_negocio->id}}" @if (old('subunidad_negocio') == strval($subunidad_negocio->id) || $articulo->subunidad_id == strval($subunidad_negocio->id)) selected @endif>{{$subunidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="centro_costo">Centro de Costo <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('centro_costo') is-invalid @enderror" id="centro_costo" name="centro_costo" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($centros_costos as $centro_costo)
                                            <option value="{{$centro_costo->id}}" @if (old('centro_costo') == strval($centro_costo->id) || $articulo->centro_costo_id == strval($centro_costo->id)) selected @endif>{{$centro_costo->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subcentro_costo">Subcentro de Costo <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('subcentro_costo') is-invalid @enderror" id="subcentro_costo" name="subcentro_costo" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($subcentros_costos as $subcentro_costo)
                                            <option value="{{$subcentro_costo->id}}" @if (old('subcentro_costo') == strval($subcentro_costo->id) || $articulo->subcentro_costo_id == strval($subcentro_costo->id)) selected @endif>{{$subcentro_costo->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3 @if ($articulo->compra_venta == 'VENTA') d-none @endif" id="div-cuenta_compra">
                                    <label class="form-label" for="cuenta_compra">Cuenta Compra <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('cuenta_compra') is-invalid @enderror" id="cuenta_compra" name="cuenta_compra" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cuentas_contables as $cuenta_compra)
                                            <option value="{{$cuenta_compra->id}}" @if (old('cuenta_compra') == strval($cuenta_compra->id) || $articulo->cuenta_compra_id == strval($cuenta_compra->id)) selected @endif data-subtext="{{$cuenta_compra->cuenta}}">{{$cuenta_compra->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center @if ($articulo->compra_venta == 'COMPRA') d-none @endif" id="div-carrera_curso">
                                    <div>
                                        <label class="form-label" for="carrera_curso">Carrera / Curso</label>
                                    </div>
                                    <div class="btn-group @error('carrera_curso') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check carrera_curso1" id="carrera_curso1" name="carrera_curso" value="carrera" @if (old('carrera_curso') == 'carrera' || $articulo->carrera_curso == 'CARRERA') checked @endif>
                                        <label class="btn btn-outline-info" for="carrera_curso1">Carrera</label>
                                        <input type="radio" class="btn-check carrera_curso2" id="carrera_curso2" name="carrera_curso" value="curso" @if (old('carrera_curso') == 'curso' || $articulo->carrera_curso == 'CURSO') checked @endif>
                                        <label class="btn btn-outline-info" for="carrera_curso2">Curso</label>
                                    </div>
                                    @error('carrera_curso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3 @if(old('carrera_curso') != 'carrera' && !$articulo->carrera) d-none @endif" id="div-carrera">
                                    <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($carreras as $carrera)
                                            @if ($articulo->carrera)
                                                <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id) || $articulo->carrera->id) selected @endif data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                            @else
                                                <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id)) selected @endif data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3 @if(old('carrera_curso') != 'curso' && !$articulo->curso) d-none @endif" id="div-curso">
                                    <label class="form-label" for="curso">Curso <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('curso') is-invalid @enderror" id="curso" name="curso" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cursos as $curso)
                                            @if ($articulo->curso)
                                                <option value="{{$curso->id}}" @if (old('curso') == strval($curso->id) || $articulo->curso->id) selected @endif data-subtext="{{$curso->tipoCurso->nombre}}">{{$curso->nombre_fantasia}}</option>
                                            @else
                                                <option value="{{$curso->id}}" @if (old('curso') == strval($curso->id)) selected @endif data-subtext="{{$curso->tipoCurso->nombre}}">{{$curso->nombre_fantasia}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('curso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row @if (old('compra_venta') != 'venta' && $articulo->compra_venta != 'VENTA') d-none @endif" id="row-precios">
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
                                                <input type="text" class="form-control precios text-center @error('precio_matricula') is-invalid @enderror" id="precio_matricula" name="precio_matricula" value="{{old('precio_matricula', number_format($articulo->detalle->precio_matricula, 0, ',', '.'))}}">
                                                @error('precio_matricula')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-3 text-center">
                                            <label class="form-label" for="precio_contado">Precio Contado</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_contado') is-invalid @enderror" id="precio_contado" name="precio_contado" value="{{old('precio_contado', number_format($articulo->detalle->precio_contado, 0, ',', '.'))}}">
                                                @error('precio_contado')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_cuota">Precio Cuotas</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_cuota') is-invalid @enderror" id="precio_cuota" name="precio_cuota" value="{{old('precio_cuota', number_format($articulo->detalle->precio_cuota, 0, ',', '.'))}}">
                                                @error('precio_cuota')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="cantidad_cuotas">Cant. Cuotas</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control precios cantidad_cuotas text-center @error('cantidad_cuotas') is-invalid @enderror" id="cantidad_cuotas" name="cantidad_cuotas" value="{{old('cantidad_cuotas', $articulo->detalle->cantidad_cuotas)}}">
                                                <span class="input-group-text">cuotas</span>
                                                @error('cantidad_cuotas')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_defensa">Precio Defensa</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_defensa') is-invalid @enderror" id="precio_defensa" name="precio_defensa" value="{{old('precio_defensa', number_format($articulo->detalle->precio_defensa, 0, ',', '.'))}}">
                                                @error('precio_defensa')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_titulo">Precio Título</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_titulo') is-invalid @enderror" id="precio_titulo" name="precio_titulo" value="{{old('precio_titulo', number_format($articulo->detalle->precio_titulo, 0, ',', '.'))}}">
                                                @error('precio_titulo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_certificado">Precio Certificado</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_certificado') is-invalid @enderror" id="precio_certificado" name="precio_certificado" value="{{old('precio_certificado', number_format($articulo->detalle->precio_certificado, 0, ',', '.'))}}">
                                                @error('precio_certificado')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_multa">Multa x Día</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_multa') is-invalid @enderror" id="precio_multa" name="precio_multa" value="{{old('precio_multa', number_format($articulo->detalle->precio_multa, 0, ',', '.'))}}">
                                                @error('precio_multa')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_examen_suficiencia">Precio Examen Suf.</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_examen_suficiencia') is-invalid @enderror" id="precio_examen_suficiencia" name="precio_examen_suficiencia" value="{{old('precio_examen_suficiencia', number_format($articulo->detalle->precio_examen_suficiencia, 0, ',', '.'))}}">
                                                @error('precio_examen_suficiencia')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-3 text-center">
                                            <label class="form-label" for="precio_constancia_carrera">Precio Constancia</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Gs.</span>
                                                <input type="text" class="form-control precios text-center @error('precio_constancia_carrera') is-invalid @enderror" id="precio_constancia_carrera" name="precio_constancia_carrera" value="{{old('precio_constancia_carrera', number_format($articulo->detalle->precio_constancia_carrera, 0, ',', '.'))}}">
                                                @error('precio_constancia_carrera')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h4 class="card-title mb-3">Vencimientos</h4>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="fecha_vencimiento_primera_cuota">Venc. 1ra Cuota</label>
                                            <div class="form-icon right">
                                                <input type="text" class="form-control vencimientos flatpickr text-center form-control-icon @error('fecha_vencimiento_primera_cuota') is-invalid @enderror" id="fecha_vencimiento_primera_cuota" name="fecha_vencimiento_primera_cuota" value="{{old('fecha_vencimiento_primera_cuota', $articulo->detalle->fecha_vencimiento_primera_cuota)}}">
                                                <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                                @error('fecha_vencimiento_primera_cuota')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="dia_vencimiento_cuotas">Día Venc. Cuotas</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Día</span>
                                                <input type="text" class="form-control vencimientos text-center @error('dia_vencimiento_cuotas') is-invalid @enderror" id="dia_vencimiento_cuotas" name="dia_vencimiento_cuotas" value="{{old('dia_vencimiento_cuotas', $articulo->detalle->dia_vencimiento_cuotas)}}">
                                                <span class="input-group-text">del mes</span>
                                                @error('dia_vencimiento_cuotas')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-3 text-center">
                                            <label class="form-label" for="dias_gracia">Días de Gracia</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control vencimientos text-center @error('dias_gracia') is-invalid @enderror" id="dias_gracia" name="dias_gracia" value="{{old('dias_gracia', $articulo->detalle->dias_gracia)}}">
                                                <span class="input-group-text">días</span>
                                                @error('dias_gracia')
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
                                            <select class="form-control selectpicker cuentas @error('cuenta_matricula') is-invalid @enderror" id="cuenta_matricula" name="cuenta_matricula" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_matricula)
                                                    <option value="{{$cuenta_matricula->id}}" @if (old('cuenta_matricula') == strval($cuenta_matricula->id) || $articulo->detalle->cuenta_matricula_id == strval($cuenta_matricula->id)) selected @endif data-subtext="{{$cuenta_matricula->cuenta}}">{{$cuenta_matricula->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_matricula')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_contado">Cuenta Contado</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_contado') is-invalid @enderror" id="cuenta_contado" name="cuenta_contado" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_contado)
                                                    <option value="{{$cuenta_contado->id}}" @if (old('cuenta_contado') == strval($cuenta_contado->id) || $articulo->detalle->cuenta_contado_id == strval($cuenta_contado->id)) selected @endif data-subtext="{{$cuenta_contado->cuenta}}">{{$cuenta_contado->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_contado')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_cuota">Cuenta Cuotas</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_cuota') is-invalid @enderror" id="cuenta_cuota" name="cuenta_cuota" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_cuota)
                                                    <option value="{{$cuenta_cuota->id}}" @if (old('cuenta_cuota') == strval($cuenta_cuota->id) || $articulo->detalle->cuenta_cuota_id == strval($cuenta_cuota->id)) selected @endif data-subtext="{{$cuenta_cuota->cuenta}}">{{$cuenta_cuota->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_cuota')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_descuento">Cuenta Descuentos</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_descuento') is-invalid @enderror" id="cuenta_descuento" name="cuenta_descuento" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_descuento)
                                                    <option value="{{$cuenta_descuento->id}}" @if (old('cuenta_descuento') == strval($cuenta_descuento->id) || $articulo->detalle->cuenta_descuento_id == strval($cuenta_descuento->id)) selected @endif data-subtext="{{$cuenta_descuento->cuenta}}">{{$cuenta_descuento->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_descuento')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_defensa">Cuenta Defensa</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_defensa') is-invalid @enderror" id="cuenta_defensa" name="cuenta_defensa" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_defensa)
                                                    <option value="{{$cuenta_defensa->id}}" @if (old('cuenta_defensa') == strval($cuenta_defensa->id) || $articulo->detalle->cuenta_defensa_id == strval($cuenta_defensa->id)) selected @endif data-subtext="{{$cuenta_defensa->cuenta}}">{{$cuenta_defensa->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_defensa')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_titulo">Cuenta Título</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_titulo') is-invalid @enderror" id="cuenta_titulo" name="cuenta_titulo" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_titulo)
                                                    <option value="{{$cuenta_titulo->id}}" @if (old('cuenta_titulo') == strval($cuenta_titulo->id) || $articulo->detalle->cuenta_titulo_id == strval($cuenta_titulo->id)) selected @endif data-subtext="{{$cuenta_titulo->cuenta}}">{{$cuenta_titulo->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_titulo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_certificado">Cuenta Certificado</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_certificado') is-invalid @enderror" id="cuenta_certificado" name="cuenta_certificado" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_certificado)
                                                    <option value="{{$cuenta_certificado->id}}" @if (old('cuenta_certificado') == strval($cuenta_certificado->id) || $articulo->detalle->cuenta_certificado_id == strval($cuenta_certificado->id)) selected @endif data-subtext="{{$cuenta_certificado->cuenta}}">{{$cuenta_certificado->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_certificado')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_multa">Cuenta Multa</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_multa') is-invalid @enderror" id="cuenta_multa" name="cuenta_multa" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_multa)
                                                    <option value="{{$cuenta_multa->id}}" @if (old('cuenta_multa') == strval($cuenta_multa->id) || $articulo->detalle->cuenta_multa_id == strval($cuenta_multa->id)) selected @endif data-subtext="{{$cuenta_multa->cuenta}}">{{$cuenta_multa->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_multa')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_examen_suficiencia">Cuenta Examen Suf.</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_examen_suficiencia') is-invalid @enderror" id="cuenta_examen_suficiencia" name="cuenta_examen_suficiencia" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_examen_suficiencia)
                                                    <option value="{{$cuenta_examen_suficiencia->id}}" @if (old('cuenta_examen_suficiencia') == strval($cuenta_examen_suficiencia->id) || $articulo->detalle->cuenta_examen_suficiencia_id == strval($cuenta_examen_suficiencia->id)) selected @endif data-subtext="{{$cuenta_examen_suficiencia->cuenta}}">{{$cuenta_examen_suficiencia->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_examen_suficiencia')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="cuenta_constancia_carrera">Cuenta Constancia</label>
                                            <select class="form-control selectpicker cuentas @error('cuenta_constancia_carrera') is-invalid @enderror" id="cuenta_constancia_carrera" name="cuenta_constancia_carrera" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($cuentas_contables as $cuenta_constancia_carrera)
                                                    <option value="{{$cuenta_constancia_carrera->id}}" @if (old('cuenta_constancia_carrera') == strval($cuenta_constancia_carrera->id) || $articulo->detalle->cuenta_constancia_carrera_id == strval($cuenta_constancia_carrera->id)) selected @endif data-subtext="{{$cuenta_constancia_carrera->cuenta}}">{{$cuenta_constancia_carrera->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('cuenta_constancia_carrera')
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('articulos.scripts.edit-scripts')
    @endsection
@endcan
