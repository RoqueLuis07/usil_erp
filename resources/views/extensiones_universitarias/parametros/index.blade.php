@can('gestionar_parametros_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Facultades, carreras y materias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('href') {{ route('extensiones_universitarias.index') }} @endslot
            @slot('title') Facultades, carreras y materias @endslot
        @endcomponent

        @include('extensiones_universitarias.scripts.messages-scripts')

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)<div>{{$error}}</div>@endforeach
            </div>
        @endif

        @php $pestana = in_array(request('pestana'), ['carreras', 'materias']) ? request('pestana') : 'facultades'; @endphp

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item"><a class="nav-link @if ($pestana == 'facultades') active @endif" data-bs-toggle="tab" href="#tab-facultades" role="tab">Facultades <span class="badge bg-secondary-subtle text-secondary ms-1">{{$facultades->count()}}</span></a></li>
                    <li class="nav-item"><a class="nav-link @if ($pestana == 'carreras') active @endif" data-bs-toggle="tab" href="#tab-carreras" role="tab">Carreras <span class="badge bg-secondary-subtle text-secondary ms-1">{{$carreras->count()}}</span></a></li>
                    <li class="nav-item"><a class="nav-link @if ($pestana == 'materias') active @endif" data-bs-toggle="tab" href="#tab-materias" role="tab">Materias <span class="badge bg-secondary-subtle text-secondary ms-1">{{$materias->count()}}</span></a></li>
                </ul>
            </div>
            <div class="card-body">
                <p class="text-muted">Estos datos alimentan las listas desplegables de las actividades, los alumnos, los docentes y la carga masiva. Cada materia se relaciona con las carreras en cuya malla figura; al cargar una actividad, la lista de materias se acota a las carreras elegidas. Lo que ya se usó en otros datos no se elimina: se inactiva.</p>
                <div class="tab-content">
                    {{-- ------------------------------------------------------------ Facultades --}}
                    <div class="tab-pane @if ($pestana == 'facultades') active @endif" id="tab-facultades" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-2">
                            <input type="search" class="form-control form-control-sm w-auto js-filtrar" data-tabla="#tabla-facultades" placeholder="Buscar facultad...">
                            <div class="flex-grow-1"></div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#facultadModal" data-modo="crear"><i class="ri-add-line align-bottom me-1"></i>Agregar facultad</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0" id="tabla-facultades">
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
                                                    <button type="submit" class="btn btn-sm @if ($facultad->estado == 'AC') btn-warning @else btn-success @endif" title="@if ($facultad->estado == 'AC') Inactivar @else Activar @endif"><i class="@if ($facultad->estado == 'AC') ri-forbid-line @else ri-check-line @endif"></i></button>
                                                </form>
                                                <form action="{{route('parametros_extension.facultades.destroy', $facultad->id)}}" method="post" class="d-inline js-eliminar" data-mensaje="Vas a eliminar la facultad {{$facultad->nombre}}.">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
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

                    {{-- ------------------------------------------------------------ Carreras --}}
                    <div class="tab-pane @if ($pestana == 'carreras') active @endif" id="tab-carreras" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-2">
                            <input type="search" class="form-control form-control-sm w-auto js-filtrar" data-tabla="#tabla-carreras" placeholder="Buscar carrera o facultad...">
                            <div class="flex-grow-1"></div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#carreraModal" data-modo="crear" @if ($facultades->isEmpty()) disabled title="Primero creá una facultad" @endif><i class="ri-add-line align-bottom me-1"></i>Agregar carrera</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0" id="tabla-carreras">
                                <thead><tr><th>Carrera</th><th>Abrev.</th><th>Facultad</th><th class="text-center">Semestres</th><th class="text-center">Materias</th><th>Estado</th><th>Acciones</th></tr></thead>
                                <tbody>
                                    @forelse ($carreras as $carrera)
                                        <tr>
                                            <td>{{$carrera->nombre_fantasia}}</td>
                                            <td>{{$carrera->abreviatura}}</td>
                                            <td>{{optional($carrera->Facultad)->nombre}}</td>
                                            <td class="text-center">{{$carrera->cantidad_semestres}}</td>
                                            <td class="text-center">{{$carrera->materias_count}}</td>
                                            <td><span class="badge @if ($carrera->estado == 'AC') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif">@if ($carrera->estado == 'AC') ACTIVA @else INACTIVA @endif</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#carreraModal" data-modo="editar" data-id="{{$carrera->id}}" data-nombre="{{$carrera->nombre_fantasia}}" data-abreviatura="{{$carrera->abreviatura}}" data-facultad="{{$carrera->facultad_id}}" data-semestres="{{$carrera->cantidad_semestres}}" title="Editar"><i class="ri-edit-fill"></i></button>
                                                <form action="{{route('parametros_extension.carreras.toggle', $carrera->id)}}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm @if ($carrera->estado == 'AC') btn-warning @else btn-success @endif" title="@if ($carrera->estado == 'AC') Inactivar @else Activar @endif"><i class="@if ($carrera->estado == 'AC') ri-forbid-line @else ri-check-line @endif"></i></button>
                                                </form>
                                                <form action="{{route('parametros_extension.carreras.destroy', $carrera->id)}}" method="post" class="d-inline js-eliminar" data-mensaje="Vas a eliminar la carrera {{$carrera->nombre_fantasia}}.">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted">Todavía no hay carreras cargadas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ------------------------------------------------------------ Materias --}}
                    <div class="tab-pane @if ($pestana == 'materias') active @endif" id="tab-materias" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-2">
                            <input type="search" class="form-control form-control-sm w-auto js-filtrar" data-tabla="#tabla-materias" placeholder="Buscar materia o carrera...">
                            <div class="flex-grow-1"></div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#materiaModal" data-modo="crear"><i class="ri-add-line align-bottom me-1"></i>Agregar materia</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="tabla-materias">
                                <thead><tr><th>Materia</th><th>Carreras</th><th class="text-center">Horas</th><th>Estado</th><th class="text-nowrap">Acciones</th></tr></thead>
                                <tbody>
                                    @forelse ($materias as $materia)
                                        <tr>
                                            <td>{{$materia->nombre_fantasia}} @if ($materia->codigo)<small class="text-muted">({{$materia->codigo}})</small>@endif</td>
                                            <td>
                                                @forelse ($materia->carreras as $carrera)
                                                    <span class="badge bg-primary-subtle text-primary">{{$carrera->nombre_fantasia}}</span>
                                                @empty
                                                    <span class="text-muted">Sin carrera (aparece en todas las listas)</span>
                                                @endforelse
                                            </td>
                                            <td class="text-center">{{$materia->carga_horaria}}</td>
                                            <td><span class="badge @if ($materia->estado == 'AC') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif">@if ($materia->estado == 'AC') ACTIVA @else INACTIVA @endif</span></td>
                                            <td class="text-nowrap">
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#materiaModal" data-modo="editar" data-id="{{$materia->id}}" data-nombre="{{$materia->nombre_fantasia}}" data-codigo="{{$materia->codigo}}" data-horas="{{$materia->carga_horaria}}" data-carreras="{{ $materia->carreras->pluck('id')->implode(',') }}" title="Editar"><i class="ri-edit-fill"></i></button>
                                                <form action="{{route('parametros_extension.materias.toggle', $materia->id)}}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm @if ($materia->estado == 'AC') btn-warning @else btn-success @endif" title="@if ($materia->estado == 'AC') Inactivar @else Activar @endif"><i class="@if ($materia->estado == 'AC') ri-forbid-line @else ri-check-line @endif"></i></button>
                                                </form>
                                                <form action="{{route('parametros_extension.materias.destroy', $materia->id)}}" method="post" class="d-inline js-eliminar" data-mensaje="Vas a eliminar la materia {{$materia->nombre_fantasia}}.">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">Todavía no hay materias cargadas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0" style="font-size: 12px">Docentes y alumnos se administran en sus propias pantallas (menú Docentes y Alumnos); sus carreras se eligen de esta misma lista.</p>
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

        {{-- Modal materia (alta / edición) --}}
        <div class="modal fade" id="materiaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="post" id="materia-form" action="{{route('parametros_extension.materias.store')}}" class="modal-content">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title" id="materia-titulo">Agregar materia</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="materia-nombre">Nombre <span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="materia-nombre" name="nombre" maxlength="255" required>
                                @error('nombre')<span class="invalid-feedback d-block"><strong>{{$message}}</strong></span>@enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="materia-codigo">Código</label>
                                <input type="text" class="form-control" id="materia-codigo" name="codigo" maxlength="50">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="materia-horas">Horas</label>
                                <input type="number" class="form-control" id="materia-horas" name="carga_horaria" min="0" max="5000">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="materia-carreras">Carreras en las que figura</label>
                            <select class="selectpicker form-control" id="materia-carreras" name="carreras[]" multiple data-live-search="true" data-actions-box="true" data-size="8" data-selected-text-format="count > 3" title="Sin carrera (aparece en todas las listas)">
                                @foreach ($carreras->groupBy(fn ($c) => optional($c->Facultad)->nombre) as $nombreFacultad => $grupo)
                                    <optgroup label="{{$nombreFacultad}}">
                                        @foreach ($grupo as $carrera)
                                            <option value="{{$carrera->id}}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <p class="text-muted mt-1 mb-0" style="font-size: 12px">Al cargar una actividad con carreras elegidas, solo se ofrecen las materias de esas carreras.</p>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script>
            $(function () {
                var rutaFacultad = "{{ route('parametros_extension.facultades.store') }}";
                var rutaFacultadUpd = "{{ url('parametros_extension/facultades') }}";
                var rutaCarrera = "{{ route('parametros_extension.carreras.store') }}";
                var rutaCarreraUpd = "{{ url('parametros_extension/carreras') }}";
                var rutaMateria = "{{ route('parametros_extension.materias.store') }}";
                var rutaMateriaUpd = "{{ url('parametros_extension/materias') }}";

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
                $('#materiaModal').on('show.bs.modal', function (e) {
                    var b = $(e.relatedTarget);
                    var editar = b.data('modo') === 'editar';
                    $('#materia-titulo').text(editar ? 'Editar materia' : 'Agregar materia');
                    $('#materia-form').attr('action', editar ? rutaMateriaUpd + '/' + b.data('id') : rutaMateria);
                    $('#materia-nombre').val(editar ? b.data('nombre') : '');
                    $('#materia-codigo').val(editar ? b.data('codigo') : '');
                    $('#materia-horas').val(editar ? b.data('horas') : '');
                    var ids = editar ? String(b.data('carreras') || '').split(',').filter(Boolean) : [];
                    $('#materia-carreras').selectpicker('val', ids).selectpicker('refresh');
                });

                // Buscador de cada tabla
                $('.js-filtrar').on('input', function () {
                    var texto = $(this).val().toLowerCase();
                    $($(this).data('tabla') + ' tbody tr').each(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(texto) !== -1);
                    });
                });

                // Confirmación antes de eliminar
                $('form.js-eliminar').on('submit', function (e) {
                    var formulario = this;
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Eliminar?',
                        text: $(formulario).data('mensaje') + ' Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#f06548'
                    }).then(function (r) { if (r.isConfirmed) { formulario.submit(); } });
                });
            });
        </script>
    @endsection
@endcan
