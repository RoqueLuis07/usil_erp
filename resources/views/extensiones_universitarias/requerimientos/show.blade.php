@can('ver_requerimientos_extensiones_universitarias')
    @extends('layouts.master')
    @section('title') Ver Requerimientos de Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Parámetros Académicos @endslot
            @slot('title') Ver Requerimientos de Extensión  @endslot
        @endcomponent

        @include('extensiones_universitarias.requerimientos.scripts.messages-scripts')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar requerimientos de extensión universitaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="actividades_requeridas">Actividades Requeridas <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center" id="actividades_requeridas" value="{{$requerimiento->actividades_requeridas}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="horas_requeridas">Horas Requeridas <span class="text-danger">(*)</span></label>
                                    @php
                                        if ($requerimiento->horas_requeridas != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    <input type="text" class="form-control text-center" id="horas_requeridas" value="{{number_format($requerimiento->horas_requeridas, 2, ',', '.')}} {{$texto}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('parametros_academicos.index')}}">Volver</a>
                            @can('editar_requerimientos_extensiones_universitarias')
                                <a type="button" class="btn btn-warning" href="{{route('requerimientos_extensiones_universitarias.edit', $requerimiento->id)}}">Editar</a>
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
        @include('extensiones_universitarias.requerimientos.scripts.show-scripts')
    @endsection
@endcan
