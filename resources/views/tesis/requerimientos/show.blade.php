@can('ver_requerimientos_entregas_tesis')
    @extends('layouts.master')
    @section('title') Ver Requerimientos de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Trabajos Finales de Grado @endslot
            @slot('title') Ver Requerimientos de TFG  @endslot
        @endcomponent

        @include('tesis.requerimientos.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar requerimientos de trabajos finales de grado</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_inicio_anteproyecto">Inicio Anteproyecto</label>
                                    <input type="text" class="form-control text-center" id="fecha_inicio_anteproyecto" value="{{\Carbon\Carbon::parse($requerimiento->fecha_inicio_anteproyecto)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_fin_anteproyecto">Fin Anteproyecto</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin_anteproyecto" value="{{\Carbon\Carbon::parse($requerimiento->fecha_fin_anteproyecto)->format('d/m/Y')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_inicio_proyecto">Inicio Proyecto</label>
                                    <input type="text" class="form-control text-center" id="fecha_inicio_proyecto" value="{{\Carbon\Carbon::parse($requerimiento->fecha_inicio_proyecto)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_fin_proyecto">Fin Proyecto</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin_proyecto" value="{{\Carbon\Carbon::parse($requerimiento->fecha_fin_proyecto)->format('d/m/Y')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_inicio_borrador">Inicio Borrador</label>
                                    <input type="text" class="form-control text-center" id="fecha_inicio_borrador" value="{{\Carbon\Carbon::parse($requerimiento->fecha_inicio_borrador)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="fecha_fin_borrador">Fin Borrador</label>
                                    <input type="text" class="form-control text-center" id="fecha_fin_borrador" value="{{\Carbon\Carbon::parse($requerimiento->fecha_fin_borrador)->format('d/m/Y')}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('tesis.index')}}">Volver</a>
                            @can('editar_requerimientos_entregas_tesis')
                                <a type="button" class="btn btn-warning" href="{{route('requerimientos_entregas_tesis.edit', $requerimiento->id)}}">Editar</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('tesis.requerimientos.scripts.show-scripts')
    @endsection
@endcan
