@can('ver_fechas_defensas_tesis_ubs')
    @extends('layouts.master')
    @section('title') Ver Fechas de Defensa @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Fechas de Defensa @endslot
            @slot('title') Ver Fechas de Defensa  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar fechas de defensa</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{\Carbon\Carbon::parse($fecha->fecha)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="hora">Hora</label>
                                    <input type="text" class="form-control text-center" id="hora" value="{{\Carbon\Carbon::parse($fecha->hora)->format('H:i')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center fw-bold @if ($fecha->estado == 'LI') text-success @else text-danger @endif" id="estado" @if ($fecha->estado == 'LI') value="LIBRE" @else value="OCUPADO" @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$fecha->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($fecha->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($fecha->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$fecha->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($fecha->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis_parametros_ubs.fechas_defensas_index')}}">Volver</a>
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
