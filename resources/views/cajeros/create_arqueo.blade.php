@can('crear_arqueos_cajas_cajero')
    @extends('layouts.master')
    @section('title') Arqueo de Caja @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Arqueo de Caja  @endslot
        @endcomponent

        @include('cajeros.scripts.messages-scripts')

        <div class="row">
            <form action="{{route('arqueo_caja.generate_arqueo')}}" method="get" target="_blank" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo arqueo de caja</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="col-lg-2 mb-3 me-3 text-center">
                                    <label class="form-label" for="caja">Caja</label>
                                    <input type="hidden" name="caja" value="{{$caja->caja_id}}">
                                    <input type="text" class="form-control text-center" id="caja" value="{{$caja->caja->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 me-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <div class="form-icon right">
                                        <input type="text" class="form-control form-control-icon flatpickr text-center @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
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
                            <button type="button" class="btn btn-success" id="generate-btn">Generar</button>
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
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        @include('cajeros.scripts.create_arqueo-scripts')
    @endsection
@endcan
