@can('ver_escalas')
    @extends('layouts.master')
    @section('title') Ver Escala @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Escalas @endslot
            @slot('title') Ver Escala  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar escala</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="{{$escala->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="programa">Programa</label>
                                    <input type="text" class="form-control" id="programa" value="{{$escala->programa->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Escala</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($escala->escalaDetalles as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="punto_minimo">Puntaje Mínimo</label> @endif
                                                    <input type="text" class="form-control text-center" id="punto_minimo" value="{{$detalle->punto_minimo}}">
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="punto_maximo">Puntaje Máximo</label> @endif
                                                    <input type="text" class="form-control text-center" id="punto_maximo" value="{{$detalle->punto_maximo}}">
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="nota">Nota</label> @endif
                                                    <input type="text" class="form-control text-center" id="nota" value="{{$detalle->nota}}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$escala->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($escala->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($escala->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$escala->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($escala->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('escalas.index')}}">Volver</a>
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
