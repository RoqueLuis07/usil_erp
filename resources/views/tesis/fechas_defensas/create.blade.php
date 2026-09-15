@can('crear_fechas_defensas_tesis')
    @extends('layouts.master')
    @section('title') Agregar Fecha de Defensa @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Fechas de Defensas @endslot
            @slot('title') Agregar Fecha de Defensa  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('fechas_defensas_tesis.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva fecha de defensa</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control text-center form-control-icon @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="hora">Hora <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="timepickr form-control text-center form-control-icon @error('hora') is-invalid @enderror" id="hora" name="hora" value="{{old('hora')}}" placeholder="Seleccionar...">
                                        <i class="ri-time-line" id="calendar-icon"></i>
                                        @error('hora')
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
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('tesis.fechas_defensas.scripts.create-scripts')
    @endsection
@endcan
