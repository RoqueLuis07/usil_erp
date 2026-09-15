@can('ver_unidades_negocios_contables')
    @extends('layouts.master')
    @section('title') Ver Centro de Costo @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Centros de Costoss @endslot
            @slot('title') Ver Centro de Costo @endslot
        @endcomponent

        @include('unidades_negocios_contables.scripts.messages-scripts')
        @include('unidades_negocios_contables.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h4 class="card-title mb-0">Visualizar unidad de negocio</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="{{$unidad_negocio->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center" id="estado" @if ($unidad_negocio->estado == 'AC') value="ACTIVO" @elseif ($unidad_negocio->estado == 'IN') value="INACTIVO" @endif readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Subunidades de Negocio</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($unidad_negocio->subunidadesNegociosContables as $key => $detalle)
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="nombre">Nombre</label> @endif
                                                    <input type="text" class="form-control text-center" id="nombre" value="{{$detalle->nombre}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="estado">Estado</label> @endif
                                                    <input type="text" class="form-control text-center" id="estado" @if ($detalle->estado == 'AC') value="ACTIVO" @elseif ($detalle->estado == 'IN') value="INACTIVO" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="accion">Acciones</label> @endif
                                                    <div>
                                                        <button type="button" class="btn @if ($detalle->estado == 'AC') btn-danger @elseif ($detalle->estado == 'IN') btn-success @endif change-estado-btn" id="accion" data-bs-toggle="modal" data-bs-target="#changeEstado-{{ $detalle->id }}" @if ($detalle->estado == 'AC') value="ACTIVO" @elseif ($detalle->estado == 'IN') value="INACTIVO" @endif readonly>@if ($detalle->estado == 'AC') Inactivar @elseif ($detalle->estado == 'IN') Activar @endif</button>
                                                    </div>
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
                            {{$unidad_negocio->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($unidad_negocio->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($unidad_negocio->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$unidad_negocio->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($unidad_negocio->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('unidades_negocios_contables.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('unidades_negocios_contables.scripts.show-scripts')
    @endsection
@endcan
