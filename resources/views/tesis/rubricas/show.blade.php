@can('ver rubricas tesis')
    @extends('layouts.master')
    @section('title') Ver Rúbrica de TFG @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Rúbricas de TFG @endslot
            @slot('title') Ver Rúbrica de TFG  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar rúbrica de trabajos finales de grado</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="{{$rubrica->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" value="{{$rubrica->tipo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control" id="estado" @if ($rubrica->estado == 'AC') value="ACTIVO" @elseif ($rubrica->estado == 'IN') value="INACTIVO" @endif readonly>
                                </div>
                                <div class="col-lg-4 mb-3 text-center d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="cantidad_rubricas">Cant. Rúbricas</label>
                                        <input class="form-control text-center" type="text" id="cantidad_rubricas" value="{{number_format($rubrica->detalles->count(), 0, ',', '.')}}" readonly>
                                    </div>
                                    <div class="col-lg-3 me-3">
                                        <label class="form-label" for="puntaje_total">Puntaje Total</label>
                                        <input class="form-control text-center" type="text" id="puntaje_total" value="{{number_format($rubrica->detalles->sum('puntos'), 0, ',', '.')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Rúbrica</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($rubrica->detalles as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="nivel">Pertenece a</label> @endif
                                                    <input type="text" class="form-control" id="nivel" value="{{$detalle->nivel->nombre}}" readonly></textarea>
                                                </div>
                                                <div class="col-lg-7 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="descripcion">Semestre</label> @endif
                                                    <textarea class="form-control" id="descripcion" cols="30" rows="10" readonly>{{$detalle->descripcion}}</textarea>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="puntos">Puntos</label> @endif
                                                    <input type="text" class="form-control text-center" id="puntos" value="{{number_format($detalle->puntos, 0, ',', '.')}}" readonly>
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
                            {{$rubrica->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($rubrica->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($rubrica->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$rubrica->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($rubrica->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('rubricas_tesis.index')}}">Volver</a>
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
