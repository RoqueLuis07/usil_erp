@can('crear_inscripciones_ubs')
    @extends('layouts.master')
    @section('title') Agregar Inscripción @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inscripciones @endslot
            @slot('title') Agregar Inscripción  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('inscripciones_ubs.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva inscripción</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="alumno">Alumno <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('alumno') is-invalid @enderror" id="alumno" name="alumno" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($alumnos as $alumno)
                                            <option value="{{$alumno->id}}" @if (old('alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="curso">Curso <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('curso') is-invalid @enderror" id="curso" name="curso" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($cursos as $curso)
                                            <option value="{{$curso->id}}" @if (old('curso') == strval($curso->id)) selected @endif data-subtext="{{$curso->llamado}}° LLAMADO - {{Carbon\Carbon::parse($curso->fecha_apertura)->format('Y')}}">{{$curso->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('curso')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 text-center">
                                    <label class="form-label">Tipo de Pago <span class="text-danger">(*)</span></label>
                                    <div class="btn-group @error('tipo_pago') is-invalid @enderror" role="group" id="div-tipo_pago">
                                        <input type="radio" class="btn-check tipo_pago" id="tipo_pago1" name="tipo_pago" value="CO" @if (old('tipo_pago') == 'CO') checked @endif>
                                        <label class="btn btn-outline-info" for="tipo_pago1">Contado</label>
                                        <input type="radio" class="btn-check tipo_pago" id="tipo_pago2" name="tipo_pago" value="CR" @if (old('tipo_pago') == 'CR') checked @endif>
                                        <label class="btn btn-outline-info" for="tipo_pago2">Crédito</label>
                                    </div>
                                    @error('tipo_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        @include('ubs.inscripciones.scripts.create-scripts')
    @endsection
@endcan
