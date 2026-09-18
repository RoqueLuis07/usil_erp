@can('editar_requerimientos_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Editar Requerimientos de Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ver Requerimientos de Extensiones Universitarias @endslot
            @slot('title') Editar Requerimientos de Extensión Universitaria  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('requerimientos_extensiones_universitarias.update', $requerimiento->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar requerimientos de extensión universitaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Carrera</label>
                                    <input type="text" class="form-control" value="{{ $requerimiento->carrera->nombre_fantasia ?? 'General (todas las carreras sin requerimiento propio)' }}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="actividades_requeridas">Actividades Requeridas <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('actividades_requeridas') is-invalid @enderror" id="actividades_requeridas" name="actividades_requeridas" value="{{old('actividades_requeridas', $requerimiento->actividades_requeridas)}}" placeholder="4">
                                    @error('actividades_requeridas')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="horas_requeridas">Horas Requeridas <span class="text-danger">(*)</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center @error('horas_requeridas') is-invalid @enderror" id="horas_requeridas" name="horas_requeridas" value="{{old('horas_requeridas', number_format($requerimiento->horas_requeridas, 2, ',', '.'))}}" placeholder="100">
                                        <span class="input-group-text">horas</span>
                                        @error('horas_requeridas')
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
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('extensiones_universitarias.requerimientos.scripts.edit-scripts')
    @endsection
@endcan
