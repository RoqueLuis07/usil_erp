@can('crear_roles')
    @extends('layouts.master-academic')
    @section('title') Agregar Rol @endsection
    @section('content')
        @section('css')
            <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
        @endsection
        @component('components.breadcrumb')
            @slot('li_1') Roles @endslot
            @slot('title') Agregar rol @endslot
        @endcomponent

        <div class="row">
            <form method="post" action="{{ route('roles.store') }}" id="store-form">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo rol</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito.</p>
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label" for="name">Nombre (*)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Escriba un nombre para el rol" value="{{old('name')}}">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="cant_permisos">Permisos Seleccionados</label>
                                    <input type="text" class="form-control" id="cant_permisos" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('permission')
                        <div class="alert alert-danger" role="alert">
                            <strong>{{$message}}</strong>
                        </div>
                    @enderror
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-3">Permisos del rol</h4>
                        </div>
                        <div class="col-lg-6 mb-3 text-end">
                            <a class="btn btn-outline-info" id="seleccionar_todo" style="margin-right: 1em">Seleccionar todo</a>
                            <a class="btn btn-outline-danger" id="deseleccionar_todo">Deseleccionar todo</a>
                        </div>
                    </div>
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_alumnos" name="chk_alumnos">
                                                <h6 class="card-title text-white">Alumnos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_alumnos as $p_alumno)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-alumnos" type="checkbox" id="permission[{{$p_alumno->id}}]" name="permission[{{$p_alumno->id}}]" value="permission[{{$p_alumno->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_docentes" name="chk_docentes">
                                                <h6 class="card-title text-white">Docentes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_docentes as $p_docente)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-docentes" type="checkbox" id="permission[{{$p_docente->id}}]" name="permission[{{$p_docente->id}}]" value="permission[{{$p_docente->id}}]">
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
                                <div class="col-xxl-6 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_convalidaciones" name="chk_convalidaciones">
                                                <h6 class="card-title text-white">Convalidaciones</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_convalidaciones as $key => $p_convalidacion)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-convalidaciones" type="checkbox" id="permission[{{$p_convalidacion->id}}]" name="permission[{{$p_convalidacion->id}}]" value="permission[{{$p_convalidacion->id}}]">
                                                                <label class="form-check-label" for="{{$p_convalidacion->name}}">{{$p_convalidacion->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_convalidaciones))
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_extensiones_universitarias" name="chk_extensiones_universitarias">
                                                <h6 class="card-title text-white">Extensiones Universitarias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_extensiones_universitarias as $key => $p_extension_universitaria)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-extensiones_universitarias" type="checkbox" id="permission[{{$p_extension_universitaria->id}}]" name="permission[{{$p_extension_universitaria->id}}]" value="permission[{{$p_extension_universitaria->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_parametros_academicos" name="chk_parametros_academicos">
                                                <h6 class="card-title text-white">Parámetros Académicos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_parametros_academicos as $p_parametro_academico)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-parametros_academicos" type="checkbox" id="permission[{{$p_parametro_academico->id}}]" name="permission[{{$p_parametro_academico->id}}]" value="permission[{{$p_parametro_academico->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_programas" name="chk_programas">
                                                <h6 class="card-title text-white">Programas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_programas as $p_programa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-programas" type="checkbox" id="permission[{{$p_programa->id}}]" name="permission[{{$p_programa->id}}]" value="permission[{{$p_programa->id}}]">
                                                        <label class="form-check-label" for="{{$p_programa->name}}">{{$p_programa->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_facultades" name="chk_facultades">
                                                <h6 class="card-title text-white">Facultades</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_facultades as $p_facultad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-facultades" type="checkbox" id="permission[{{$p_facultad->id}}]" name="permission[{{$p_facultad->id}}]" value="permission[{{$p_facultad->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_carreras" name="chk_carreras">
                                                <h6 class="card-title text-white">Carreras</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_carreras as $p_carrera)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-carreras" type="checkbox" id="permission[{{$p_carrera->id}}]" name="permission[{{$p_carrera->id}}]" value="permission[{{$p_carrera->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_materias" name="chk_materias">
                                                <h6 class="card-title text-white">Materias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias as $p_materia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-materias" type="checkbox" id="permission[{{$p_materia->id}}]" name="permission[{{$p_materia->id}}]" value="permission[{{$p_materia->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_semestres" name="chk_semestres">
                                                <h6 class="card-title text-white">Semestres</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_semestres as $p_semestre)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-semestres" type="checkbox" id="permission[{{$p_semestre->id}}]" name="permission[{{$p_semestre->id}}]" value="permission[{{$p_semestre->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_modalidades" name="chk_modalidades">
                                                <h6 class="card-title text-white">Modalidades</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_modalidades as $p_modalidad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-modalidades" type="checkbox" id="permission[{{$p_modalidad->id}}]" name="permission[{{$p_modalidad->id}}]" value="permission[{{$p_modalidad->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_formaciones_academicas" name="chk_formaciones_academicas">
                                                <h6 class="card-title text-white">Formaciones Académicas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_formaciones_academicas as $p_formacion_academica)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-formaciones_academicas" type="checkbox" id="permission[{{$p_formacion_academica->id}}]" name="permission[{{$p_formacion_academica->id}}]" value="permission[{{$p_formacion_academica->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_instituciones_educativas" name="chk_instituciones_educativas">
                                                <h6 class="card-title text-white">Instituciones Educativas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_instituciones_educativas as $p_institucion_educativa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-instituciones_educativas" type="checkbox" id="permission[{{$p_institucion_educativa->id}}]" name="permission[{{$p_institucion_educativa->id}}]" value="permission[{{$p_institucion_educativa->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_areas_conocimientos" name="chk_areas_conocimientos">
                                                <h6 class="card-title text-white">Áreas de Conocimiento</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_areas_conocimientos as $p_area_conocimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-areas_conocimientos" type="checkbox" id="permission[{{$p_area_conocimiento->id}}]" name="permission[{{$p_area_conocimiento->id}}]" value="permission[{{$p_area_conocimiento->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_reportes_academicos" name="chk_reportes_academicos">
                                                <h6 class="card-title text-white">Reportes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_reportes_academicos as $p_reporte_academico)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-reportes_academicos" type="checkbox" id="permission[{{$p_reporte_academico->id}}]" name="permission[{{$p_reporte_academico->id}}]" value="permission[{{$p_reporte_academico->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_usuarios" name="chk_usuarios">
                                                <h6 class="card-title text-white">Usuarios</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_usuarios as $p_usuario)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-usuarios" type="checkbox" id="permission[{{$p_usuario->id}}]" name="permission[{{$p_usuario->id}}]" value="permission[{{$p_usuario->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_roles" name="chk_roles">
                                                <h6 class="card-title text-white">Roles</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_roles as $p_rol)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-roles" type="checkbox" id="permission[{{$p_rol->id}}]" name="permission[{{$p_rol->id}}]" value="permission[{{$p_rol->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_permisos" name="chk_permisos">
                                                <h6 class="card-title text-white">Permisos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_permisos as $p_permiso)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-permisos" type="checkbox" id="permission[{{$p_permiso->id}}]" name="permission[{{$p_permiso->id}}]" value="permission[{{$p_permiso->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_nacionalidades" name="chk_nacionalidades">
                                                <h6 class="card-title text-white">Nacionalidades</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_nacionalidades as $p_nacionalidad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-nacionalidades" type="checkbox" id="permission[{{$p_nacionalidad->id}}]" name="permission[{{$p_nacionalidad->id}}]" value="permission[{{$p_nacionalidad->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_departamentos_paraguay" name="chk_departamentos_paraguay">
                                                <h6 class="card-title text-white">Departamentos del Paraguay</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_departamentos_paraguay as $p_departamento_paraguay)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-departamentos_paraguay" type="checkbox" id="permission[{{$p_departamento_paraguay->id}}]" name="permission[{{$p_departamento_paraguay->id}}]" value="permission[{{$p_departamento_paraguay->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_ciudades" name="chk_ciudades">
                                                <h6 class="card-title text-white">Ciudades</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_ciudades as $p_ciudad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-ciudades" type="checkbox" id="permission[{{$p_ciudad->id}}]" name="permission[{{$p_ciudad->id}}]" value="permission[{{$p_ciudad->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_barrios" name="chk_barrios">
                                                <h6 class="card-title text-white">Barrios</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_barrios as $p_barrio)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-barrios" type="checkbox" id="permission[{{$p_barrio->id}}]" name="permission[{{$p_barrio->id}}]" value="permission[{{$p_barrio->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_formas_conocimientos" name="chk_formas_conocimientos">
                                                <h6 class="card-title text-white">Formas de Conocer la Universidad</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_formas_conocimientos as $p_forma_conocimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-formas_conocimientos" type="checkbox" id="permission[{{$p_forma_conocimiento->id}}]" name="permission[{{$p_forma_conocimiento->id}}]" value="permission[{{$p_forma_conocimiento->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_alumnos_pantalla" name="chk_alumnos_pantalla">
                                                <h6 class="card-title text-white">Pantalla de Alumnos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_alumnos_pantalla as $key => $p_alumno_pantalla)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-alumnos_pantalla" type="checkbox" id="permission[{{$p_alumno_pantalla->id}}]" name="permission[{{$p_alumno_pantalla->id}}]" value="permission[{{$p_alumno_pantalla->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_docentes_pantalla" name="chk_docentes_pantalla">
                                                <h6 class="card-title text-white">Pantalla de Docentes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_docentes_pantalla as $key => $p_docente_pantalla)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-docentes_pantalla" type="checkbox" id="permission[{{$p_docente_pantalla->id}}]" name="permission[{{$p_docente_pantalla->id}}]" value="permission[{{$p_docente_pantalla->id}}]">
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
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="text-end">
                                <button type="button" class="btn btn-light me-2" id="clean-btn">Limpiar</button>
                                <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                                <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </form>
        </div>
        <!--end row-->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        @include('roles.scripts.create-scripts')
    @endsection
@endcan
