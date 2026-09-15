@can('editar_mallas_espejo')
    @extends('layouts.master')
    @section('title') Editar Malla Espejo @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Mallas Espejo @endslot
            @slot('title') Editar Malla Espejo  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('mallas_espejos.update', $malla_espejo->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar malla espejo</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row d-flex justify-content-center">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_paraguay">Malla Paraguay</label>
                                    <input type="text" class="form-control" id="malla_paraguay" value="{{$malla_espejo->mallaParaguay->carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="malla_siu">Malla SIU</label>
                                    <input type="text" class="form-control" id="malla_siu" value="{{$malla_espejo->mallaSiu->carrera->nombre_fantasia}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Materias de la Malla Espejo</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($malla_espejo->mallaEspejoDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-6 col-lg-3 mb-2 text-center" id="div-materia_paraguay-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia_paraguay">Materia Paraguay <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control materia_paraguay-{{$key}} materia_paraguay @error('detalles.'. $key . '.materia_paraguay') is-invalid @enderror" id="materia_paraguay-{{$key}}" name="detalles[{{$key}}][materia_paraguay]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias_paraguay as $materia_paraguay)
                                                            <option value="{{$materia_paraguay->materia->id}}" @if (old('detalles.{{$key}}.materia_paraguay') == strval($materia_paraguay->materia->id) || $detalle->materia_paraguay_id == strval($materia_paraguay->materia->id)) selected @endif>{{$materia_paraguay->materia->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.materia_paraguay')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_siu-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-materia_siu">Materia SIU <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control materia_siu-{{$key}} materia_siu @error('detalles.'. $key . '.materia_siu') is-invalid @enderror" id="materia_siu-{{$key}}" name="detalles[{{$key}}][materia_siu]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($materias_siu as $materia_siu)
                                                            <option value="{{$materia_siu->materia->id}}" @if (old('detalles.{{$key}}.materia_siu') == strval($materia_siu->materia->id) || $detalle->materia_siu_id == strval($materia_siu->materia->id)) selected @endif>{{$materia_siu->materia->nombre_fantasia}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.materia_siu')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-1 col-lg-1 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                    <div class="align-middle" id="acciones-{{$key}}">
                                                        @if ($key == 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                        @elseif ($key > 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                        @elseif ($key > 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                        @elseif ($key == 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="materia-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
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
        @include('mallas_espejos.scripts.edit-scripts')
        @include('mallas_espejos.scripts.edit-detalles-scripts')
    @endsection
@endcan
