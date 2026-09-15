@can('editar_requerimientos_entregas_tesis')
    @extends('layouts.master')
    @section('title') Editar Requerimientos de TFG @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ver Requerimientos de TFG @endslot
            @slot('title') Editar Requerimientos de TFG  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('requerimientos_entregas_tesis.update', $requerimiento->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar requerimientos de trabajos finales de grado</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_inicio_anteproyecto">Inicio Entrega Anteproyecto <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_inicio_anteproyecto') is-invalid @enderror" id="fecha_inicio_anteproyecto" name="fecha_inicio_anteproyecto" value="{{old('fecha_inicio_anteproyecto', $requerimiento->fecha_inicio_anteproyecto)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_inicio_anteproyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_fin_anteproyecto">Fin Entrega Anteproyecto <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_fin_anteproyecto') is-invalid @enderror" id="fecha_fin_anteproyecto" name="fecha_fin_anteproyecto" value="{{old('fecha_fin_anteproyecto', $requerimiento->fecha_fin_anteproyecto)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_fin_anteproyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_inicio_proyecto">Inicio Entrega Proyecto <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_inicio_proyecto') is-invalid @enderror" id="fecha_inicio_proyecto" name="fecha_inicio_proyecto" value="{{old('fecha_inicio_proyecto', $requerimiento->fecha_inicio_proyecto)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_inicio_proyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_fin_proyecto">Fin Entrega Proyecto <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_fin_proyecto') is-invalid @enderror" id="fecha_fin_proyecto" name="fecha_fin_proyecto" value="{{old('fecha_fin_proyecto', $requerimiento->fecha_fin_proyecto)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_fin_proyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_inicio_borrador">Inicio Entrega Borrador <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_inicio_borrador') is-invalid @enderror" id="fecha_inicio_borrador" name="fecha_inicio_borrador" value="{{old('fecha_inicio_borrador', $requerimiento->fecha_inicio_borrador)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_inicio_borrador')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha_fin_borrador">Fin Entrega Borrador <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha_fin_borrador') is-invalid @enderror" id="fecha_fin_borrador" name="fecha_fin_borrador" value="{{old('fecha_fin_borrador', $requerimiento->fecha_fin_borrador)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_fin_borrador')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn" data-id="{{$requerimiento->id}}">Actualizar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        @include('tesis.requerimientos.scripts.edit-scripts')
    @endsection
@endcan
