@can('crear_usuarios')
    @extends('layouts.master-academic')
    @section('title') Agregar Usuario @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Usuarios @endslot
            @slot('title') Agregar Usuario  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('usuarios.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo usuario</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label class="form-label" for="name">Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Escriba un nombre de usuario" value="{{old('name')}}">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="email">Correo Electrónico <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Escriba un correo electrónico" value="{{old('email')}}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="rol">Rol <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('rol') is-invalid @enderror" id="rol" name="rol" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($roles as $rol)
                                            <option value="{{$rol->id}}" @if(old('rol') == strval($rol->id)) selected @endif>{{$rol->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('rol')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label class="form-label" for="password">Contraseña <span class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Escriba una contraseña">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="password_confirmation">Confirmar Contraseña <span class="text-danger">(*)</span></label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirme una contraseña">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end">
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
        @include('usuarios.scripts.create-scripts')
    @endsection
@endcan
