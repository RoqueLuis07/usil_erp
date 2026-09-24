@can('gestionar_parametros_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Parámetros de Extensión @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Parámetros @endslot
        @endcomponent

        @include('extensiones_universitarias.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Facultades</h4>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#facultadModal" data-modo="crear"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead><tr><th>Facultad</th><th class="text-center">Carreras</th><th>Estado</th><th>Acciones</th></tr></thead>
                                <tbody>
                                    @forelse ($facultades as $facultad)
                                        <tr>
                                            <td>{{$facultad->nombre}}</td>
                                            <td class="text-center">{{$facultad->carreras_count}}</td>
                                            <td><span class="badge @if ($facultad->estado == 'AC') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif">@if ($facultad->estado == 'AC') ACTIVA @else INACTIVA @endif</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#facultadModal" data-modo="editar" data-id="{{$facultad->id}}" data-nombre="{{$facultad->nombre}}" title="Editar"><i class="ri-edit-fill"></i></button>
                                                <form action="{{route('parametros_extension.facultades.toggle', $facultad->id)}}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm @if ($facultad->estado == 'AC') btn-danger @else btn-success @endif" title="@if ($facultad->estado == 'AC') Inactivar @else Activar @endif"><i class="@if ($facultad->estado == 'AC') ri-forbid-line @else ri-check-line @endif"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">Todavía no hay facultades. Agregá la primera para poder crear carreras.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Carreras</h4>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#carreraModal" data-modo="crear" @if ($facultades->isEmpty()) disabled title="Primero creá una facultad" @endif><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead><tr><th>Carrera</th><th>Abrev.</th><th>Facultad</th><th class="text-center">Semestres</th><th>Estado</th><th>Acciones</th></tr></thead>
                                <tbody>
                                    @forelse ($carreras as $carrera)
                                        <tr>
                                            <td>{{$carrera->nombre_fantasia}}</td>
                                            <td>{{$carrera->abreviatura}}</td>
                                            <td>{{optional($carrera->Facultad)->nombre}}</td>
                                            <td class="text-center">{{$carrera->cantidad_semestres}}</td>
                                            <td><span class="badge @if ($carrera->estado == 'AC') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif">@if ($carrera->estado == 'AC') ACTIVA @else INACTIVA @endif</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#carreraModal" data-modo="editar" data-id="{{$carrera->id}}" data-nombre="{{$carrera->nombre_fantasia}}" data-abreviatura="{{$carrera->abreviatura}}" data-facultad="{{$carrera->facultad_id}}" data-semestres="{{$carrera->cantidad_semestres}}" title="Editar"><i class="ri-edit-fill"></i></button>
                                                <form action="{{route('parametros_extension.carreras.toggle', $carrera->id)}}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm @if ($carrera->estado == 'AC') btn-danger @else btn-success @endif" title="@if ($carrera->estado == 'AC') Inactivar @else Activar @endif"><i class="@if ($carrera->estado == 'AC') ri-forbid-line @else ri-check-line @endif"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">Todavía no hay carreras cargadas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal facultad (alta / edición) --}}
        <div class="modal fade" id="facultadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" id="facultad-form" action="{{route('parametros_extension.facultades.store')}}" class="modal-content">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title" id="facultad-titulo">Agregar facultad</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <label class="form-label" for="facultad-nombre">Nombre <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="facultad-nombre" name="nombre" required>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div>

        {{-- Modal carrera (alta / edición) --}}
        <div class="modal fade" id="carreraModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" id="carrera-form" action="{{route('parametros_extension.carreras.store')}}" class="modal-content">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title" id="carrera-titulo">Agregar carrera</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="carrera-nombre">Nombre <span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="carrera-nombre" name="nombre" required>
                            @error('nombre')<span class="invalid-feedback d-block"><strong>{{$message}}</strong></span>@enderror
                        </div>
                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label" for="carrera-abreviatura">Abreviatura <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" id="carrera-abreviatura" name="abreviatura" maxlength="10" required>
                            </div>
                            <div class="col-8 mb-3">
                                <label class="form-label" for="carrera-facultad">Facultad <span class="text-danger">(*)</span></label>
                                <select class="form-select" id="carrera-facultad" name="facultad" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    @foreach ($facultades->where('estado', 'AC') as $facultad)
                                        <option value="{{$facultad->id}}">{{$facultad->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="carrera-semestres">Duración (semestres) <span class="text-danger">(*)</span></label>
                            <input type="number" class="form-control" id="carrera-semestres" name="cantidad_semestres" min="1" max="20" required>
                            <p class="text-muted mt-1 mb-0" style="font-size: 12px">Se usa para acotar el semestre que se calcula para cada alumno.</p>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script>
            $(function () {
                var rutaFacultad = "{{ route('parametros_extension.facultades.store') }}";
                var rutaFacultadUpd = "{{ url('parametros_extension/facultades') }}";
                var rutaCarrera = "{{ route('parametros_extension.carreras.store') }}";
                var rutaCarreraUpd = "{{ url('parametros_extension/carreras') }}";

                $('#facultadModal').on('show.bs.modal', function (e) {
                    var b = $(e.relatedTarget);
                    var editar = b.data('modo') === 'editar';
                    $('#facultad-titulo').text(editar ? 'Editar facultad' : 'Agregar facultad');
                    $('#facultad-form').attr('action', editar ? rutaFacultadUpd + '/' + b.data('id') : rutaFacultad);
                    $('#facultad-nombre').val(editar ? b.data('nombre') : '');
                });
                $('#carreraModal').on('show.bs.modal', function (e) {
                    var b = $(e.relatedTarget);
                    var editar = b.data('modo') === 'editar';
                    $('#carrera-titulo').text(editar ? 'Editar carrera' : 'Agregar carrera');
                    $('#carrera-form').attr('action', editar ? rutaCarreraUpd + '/' + b.data('id') : rutaCarrera);
                    $('#carrera-nombre').val(editar ? b.data('nombre') : '');
                    $('#carrera-abreviatura').val(editar ? b.data('abreviatura') : '');
                    $('#carrera-facultad').val(editar ? String(b.data('facultad')) : '');
                    $('#carrera-semestres').val(editar ? b.data('semestres') : '');
                });
            });
        </script>
    @endsection
@endcan
