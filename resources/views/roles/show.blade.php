@can('crear_roles')
    @extends('layouts.master-academic')
    @section('title') Ver Rol @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Roles @endslot
            @slot('title') Ver Rol  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('roles.update', $rol->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar rol</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code>para poder agregar un registro con éxito</p>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label class="form-label" for="name">Nombre (*)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Escriba un nombre de rol" @if ($errors->any()) value="{{old('name')}}" @else value="{{$rol->name}}" @endif readonly>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="cant_usuarios">Cantidad de Usuarios</label>
                                    <input type="text" class="form-control" id="cant_usuarios" name="cant_usuarios" value="{{$cant_usuarios}}" readonly>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="cant_permisos">Cantidad de Permisos</label>
                                    <input type="text" class="form-control" id="cant_permisos" name="cant_permisos" value="{{$cant_permisos}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-end">
                                    <a type="button" href="{{route('roles.index')}}" class="btn btn-danger me-2">Volver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="card-title mb-3">Permisos del rol</h4>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Atención al Cliente</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Académico</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Alumnos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_alumnos as $p_alumno)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_alumno->id}}]" name="permiso[{{$p_alumno->id}}]" value="permiso[{{$p_alumno->id}}]">
                                                        <label class="form-check-label" for="{{$p_alumno->name}}">{{$p_alumno->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Docentes</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_docentes as $p_docente)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_docente->id}}]" name="permiso[{{$p_docente->id}}]" value="permiso[{{$p_docente->id}}]">
                                                        <label class="form-check-label" for="{{$p_docente->name}}">{{$p_docente->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            </div>
                            <div class="row">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Convalidaciones</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_convalidaciones as $key => $p_convalidacion)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_convalidacion->id}}]" name="permiso[{{$p_convalidacion->id}}]" value="permiso[{{$p_convalidacion->id}}]">
                                                                <label class="form-check-label" for="{{$p_convalidacion->name}}">{{$p_convalidacion->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_convalidaciones))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            </div>
                            <div class="row">
                                <div class="col-xxl-6 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Extensiones Universitarias</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_extensiones_universitarias as $key => $p_extension_universitaria)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_extension_universitaria->id}}]" name="permiso[{{$p_extension_universitaria->id}}]" value="permiso[{{$p_extension_universitaria->id}}]">
                                                                <label class="form-check-label" for="{{$p_extension_universitaria->name}}">{{$p_extension_universitaria->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_extensiones_universitarias))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Parámetros Académicos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_parametros_academicos as $p_parametro_academico)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_parametro_academico->id}}]" name="permiso[{{$p_parametro_academico->id}}]" value="permiso[{{$p_parametro_academico->id}}]">
                                                        <label class="form-check-label" for="{{$p_parametro_academico->name}}">{{$p_parametro_academico->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Programas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_programas as $p_programa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_programa->id}}]" name="permiso[{{$p_programa->id}}]" value="permiso[{{$p_programa->id}}]">
                                                        <label class="form-check-label" for="{{$p_programa->name}}">{{$p_programa->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Facultades</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_facultades as $p_facultad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_facultad->id}}]" name="permiso[{{$p_facultad->id}}]" value="permiso[{{$p_facultad->id}}]">
                                                        <label class="form-check-label" for="{{$p_facultad->name}}">{{$p_facultad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Carreras</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_carreras as $p_carrera)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_carrera->id}}]" name="permiso[{{$p_carrera->id}}]" value="permiso[{{$p_carrera->id}}]">
                                                        <label class="form-check-label" for="{{$p_carrera->name}}">{{$p_carrera->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Materias</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias as $p_materia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_materia->id}}]" name="permiso[{{$p_materia->id}}]" value="permiso[{{$p_materia->id}}]">
                                                        <label class="form-check-label" for="{{$p_materia->name}}">{{$p_materia->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Semestres</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_semestres as $p_semestre)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_semestre->id}}]" name="permiso[{{$p_semestre->id}}]" value="permiso[{{$p_semestre->id}}]">
                                                        <label class="form-check-label" for="{{$p_semestre->name}}">{{$p_semestre->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Modalidades</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_modalidades as $p_modalidad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_modalidad->id}}]" name="permiso[{{$p_modalidad->id}}]" value="permiso[{{$p_modalidad->id}}]">
                                                        <label class="form-check-label" for="{{$p_modalidad->name}}">{{$p_modalidad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Formaciones Académicas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_formaciones_academicas as $p_formacion_academica)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_formacion_academica->id}}]" name="permiso[{{$p_formacion_academica->id}}]" value="permiso[{{$p_formacion_academica->id}}]">
                                                        <label class="form-check-label" for="{{$p_formacion_academica->name}}">{{$p_formacion_academica->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Instituciones Educativas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_instituciones_educativas as $p_institucion_educativa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_institucion_educativa->id}}]" name="permiso[{{$p_institucion_educativa->id}}]" value="permiso[{{$p_institucion_educativa->id}}]">
                                                        <label class="form-check-label" for="{{$p_institucion_educativa->name}}">{{$p_institucion_educativa->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Áreas de Conocimiento</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_areas_conocimientos as $p_area_conocimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_area_conocimiento->id}}]" name="permiso[{{$p_area_conocimiento->id}}]" value="permiso[{{$p_area_conocimiento->id}}]">
                                                        <label class="form-check-label" for="{{$p_area_conocimiento->name}}">{{$p_area_conocimiento->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Reportes</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_reportes_academicos as $p_reporte_academico)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_reporte_academico->id}}]" name="permiso[{{$p_reporte_academico->id}}]" value="permiso[{{$p_reporte_academico->id}}]">
                                                        <label class="form-check-label" for="{{$p_reporte_academico->name}}">{{$p_reporte_academico->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Ventas</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Compras</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Contabilidad</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                </div>
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Tesorería</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                            <div class="row">
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Recursos Humanos</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Usuarios</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Usuarios</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_usuarios as $p_usuario)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_usuario->id}}]" name="permiso[{{$p_usuario->id}}]" value="permiso[{{$p_usuario->id}}]">
                                                        <label class="form-check-label" for="{{$p_usuario->name}}">{{$p_usuario->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Roles</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_roles as $p_rol)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_rol->id}}]" name="permiso[{{$p_rol->id}}]" value="permiso[{{$p_rol->id}}]">
                                                        <label class="form-check-label" for="{{$p_rol->name}}">{{$p_rol->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Permisos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_permisos as $p_permiso)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_permiso->id}}]" name="permiso[{{$p_permiso->id}}]" value="permiso[{{$p_permiso->id}}]">
                                                        <label class="form-check-label" for="{{$p_permiso->name}}">{{$p_permiso->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Noticias y Avisos</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Parámetros</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Nacionalidades</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_nacionalidades as $p_nacionalidad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_nacionalidad->id}}]" name="permiso[{{$p_nacionalidad->id}}]" value="permiso[{{$p_nacionalidad->id}}]">
                                                        <label class="form-check-label" for="{{$p_nacionalidad->name}}">{{$p_nacionalidad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Departamentos del Paraguay</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_departamentos_paraguay as $p_departamento_paraguay)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_departamento_paraguay->id}}]" name="permiso[{{$p_departamento_paraguay->id}}]" value="permiso[{{$p_departamento_paraguay->id}}]">
                                                        <label class="form-check-label" for="{{$p_departamento_paraguay->name}}">{{$p_departamento_paraguay->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Ciudades</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_ciudades as $p_ciudad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_ciudad->id}}]" name="permiso[{{$p_ciudad->id}}]" value="permiso[{{$p_ciudad->id}}]">
                                                        <label class="form-check-label" for="{{$p_ciudad->name}}">{{$p_ciudad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Barrios</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_barrios as $p_barrio)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_barrio->id}}]" name="permiso[{{$p_barrio->id}}]" value="permiso[{{$p_barrio->id}}]">
                                                        <label class="form-check-label" for="{{$p_barrio->name}}">{{$p_barrio->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Formas de Conocer la Universidad</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_formas_conocimientos as $p_forma_conocimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_forma_conocimiento->id}}]" name="permiso[{{$p_forma_conocimiento->id}}]" value="permiso[{{$p_forma_conocimiento->id}}]">
                                                        <label class="form-check-label" for="{{$p_forma_conocimiento->name}}">{{$p_forma_conocimiento->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Pantalla de Alumnos</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Pantalla de Alumnos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_alumnos_pantalla as $key => $p_alumno_pantalla)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_alumno_pantalla->id}}]" name="permiso[{{$p_alumno_pantalla->id}}]" value="permiso[{{$p_alumno_pantalla->id}}]">
                                                                <label class="form-check-label" for="{{$p_alumno_pantalla->name}}">{{$p_alumno_pantalla->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_alumnos_pantalla))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Pantalla de Docentes</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Pantalla de Docentes</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_docentes_pantalla as $key => $p_docente_pantalla)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_docente_pantalla->id}}]" name="permiso[{{$p_docente_pantalla->id}}]" value="permiso[{{$p_docente_pantalla->id}}]">
                                                                <label class="form-check-label" for="{{$p_docente_pantalla->name}}">{{$p_docente_pantalla->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_docentes_pantalla))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        @include('roles.scripts.show-scripts')
    @endsection
@endcan
