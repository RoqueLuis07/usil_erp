@can('ver_evaluaciones')
    @extends('layouts.master')
    @section('title') Ver Evaluación @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Evaluaciones @endslot
            @slot('title') Ver Evaluación  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar evaluación</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre_evaluacion">Nombre de la Evaluación <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="nombre_evaluacion" value="{{$evaluacion->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo_evaluacion">Tipo de Evaluación <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control" id="tipo_evaluacion" value="{{$evaluacion->tipoEvaluacion->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="puntos">Puntos Posibles <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center" id="puntos" value="{{$evaluacion->puntos}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="valor_porcentual">Valor Porcentual <span class="text-danger">(*)</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center" id="valor_porcentual" value="{{$evaluacion->valor_porcentual}}" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="puntaje_minimo_requerido">Puntaje Min. Req. <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center" id="puntaje_minimo_requerido" value="{{$evaluacion->puntaje_minimo_requerido}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$evaluacion->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($evaluacion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($evaluacion->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$evaluacion->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($evaluacion->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('evaluaciones.index')}}">Volver</a>
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
