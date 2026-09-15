@can('crear_roles')
    @extends('layouts.master')
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cajero</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajeros as $p_cajero)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cajero->id}}]" name="permiso[{{$p_cajero->id}}]" value="permiso[{{$p_cajero->id}}]">
                                                        <label class="form-check-label" for="{{$p_cajero->name}}">{{$p_cajero->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Clientes</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_clientes as $p_cliente)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cliente->id}}]" name="permiso[{{$p_cliente->id}}]" value="permiso[{{$p_cliente->id}}]">
                                                        <label class="form-check-label" for="{{$p_cliente->name}}">{{$p_cliente->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Matriculaciones</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_matriculaciones as $p_matriculacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_matriculacion->id}}]" name="permiso[{{$p_matriculacion->id}}]" value="permiso[{{$p_matriculacion->id}}]">
                                                        <label class="form-check-label" for="{{$p_matriculacion->name}}">{{$p_matriculacion->name}}</label>
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Exámenes de Suficiencia</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_examenes_suficiencia as $p_examen_suficiencia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_examen_suficiencia->id}}]" name="permiso[{{$p_examen_suficiencia->id}}]" value="permiso[{{$p_examen_suficiencia->id}}]">
                                                        <label class="form-check-label" for="{{$p_examen_suficiencia->name}}">{{$p_examen_suficiencia->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Materias por Semestres</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_semestres as $p_materia_semestre)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_materia_semestre->id}}]" name="permiso[{{$p_materia_semestre->id}}]" value="permiso[{{$p_materia_semestre->id}}]">
                                                        <label class="form-check-label" for="{{$p_materia_semestre->name}}">{{$p_materia_semestre->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Actas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_actas as $p_acta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_acta->id}}]" name="permiso[{{$p_acta->id}}]" value="permiso[{{$p_acta->id}}]">
                                                        <label class="form-check-label" for="{{$p_acta->name}}">{{$p_acta->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Clases</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_clases as $p_materia_clase)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_materia_clase->id}}]" name="permiso[{{$p_materia_clase->id}}]" value="permiso[{{$p_materia_clase->id}}]">
                                                        <label class="form-check-label" for="{{$p_materia_clase->name}}">{{$p_materia_clase->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Anulaciones de Correlatividades</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_anulaciones_correlatividades as $p_anulacion_correlatividad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_anulacion_correlatividad->id}}]" name="permiso[{{$p_anulacion_correlatividad->id}}]" value="permiso[{{$p_anulacion_correlatividad->id}}]">
                                                        <label class="form-check-label" for="{{$p_anulacion_correlatividad->name}}">{{$p_anulacion_correlatividad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Trabajos Finales de Grado</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tesis as $key => $p_tfg)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_tfg->id}}]" name="permiso[{{$p_tfg->id}}]" value="permiso[{{$p_tfg->id}}]">
                                                                <label class="form-check-label" for="{{$p_tfg->name}}">{{$p_tfg->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_tesis))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-6 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Tutorías</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tutorias as $key => $p_tutoria)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_tutoria->id}}]" name="permiso[{{$p_tutoria->id}}]" value="permiso[{{$p_tutoria->id}}]">
                                                                <label class="form-check-label" for="{{$p_tutoria->name}}">{{$p_tutoria->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_tutorias))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                            <h6 class="card-title mb-3 text-white">Solicitudes</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_solicitudes as $p_solicitud)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_solicitud->id}}]" name="permiso[{{$p_solicitud->id}}]" value="permiso[{{$p_solicitud->id}}]">
                                                        <label class="form-check-label" for="{{$p_solicitud->name}}">{{$p_solicitud->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Encuestas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_encuestas as $p_encuesta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_encuesta->id}}]" name="permiso[{{$p_encuesta->id}}]" value="permiso[{{$p_encuesta->id}}]">
                                                        <label class="form-check-label" for="{{$p_encuesta->name}}">{{$p_encuesta->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
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
                                            <h6 class="card-title mb-3 text-white">Materias Suficiencia</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_suficiencia as $p_materia_suficiencia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_materia_suficiencia->id}}]" name="permiso[{{$p_materia_suficiencia->id}}]" value="permiso[{{$p_materia_suficiencia->id}}]">
                                                        <label class="form-check-label" for="{{$p_materia_suficiencia->name}}">{{$p_materia_suficiencia->name}}</label>
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Mallas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_mallas as $p_malla)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_malla->id}}]" name="permiso[{{$p_malla->id}}]" value="permiso[{{$p_malla->id}}]">
                                                        <label class="form-check-label" for="{{$p_malla->name}}">{{$p_malla->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Mallas Espejo</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_mallas_espejo as $p_malla_espejo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_malla_espejo->id}}]" name="permiso[{{$p_malla_espejo->id}}]" value="permiso[{{$p_malla_espejo->id}}]">
                                                        <label class="form-check-label" for="{{$p_malla_espejo->name}}">{{$p_malla_espejo->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Escalas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_escalas as $p_escala)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_escala->id}}]" name="permiso[{{$p_escala->id}}]" value="permiso[{{$p_escala->id}}]">
                                                        <label class="form-check-label" for="{{$p_escala->name}}">{{$p_escala->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                            <h6 class="card-title mb-3 text-white">Evaluaciones</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_evaluaciones as $p_evaluacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_evaluacion->id}}]" name="permiso[{{$p_evaluacion->id}}]" value="permiso[{{$p_evaluacion->id}}]">
                                                        <label class="form-check-label" for="{{$p_evaluacion->name}}">{{$p_evaluacion->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Fechas de Desmatriculación</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_fechas_desmatriculaciones as $p_fecha_desmatriculacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_fecha_desmatriculacion->id}}]" name="permiso[{{$p_fecha_desmatriculacion->id}}]" value="permiso[{{$p_fecha_desmatriculacion->id}}]">
                                                        <label class="form-check-label" for="{{$p_fecha_desmatriculacion->name}}">{{$p_fecha_desmatriculacion->name}}</label>
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
                            <h4 class="card-title mb-0">Escuela de Negocios</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Alumnos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_alumnos_ubs as $p_alumno_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_alumno_ubs->id}}]" name="permiso[{{$p_alumno_ubs->id}}]" value="permiso[{{$p_alumno_ubs->id}}]">
                                                        <label class="form-check-label" for="{{$p_alumno_ubs->name}}">{{$p_alumno_ubs->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Inscripciones</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_inscripciones_ubs as $p_inscripcion_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_inscripcion_ubs->id}}]" name="permiso[{{$p_inscripcion_ubs->id}}]" value="permiso[{{$p_inscripcion_ubs->id}}]">
                                                        <label class="form-check-label" for="{{$p_inscripcion_ubs->name}}">{{$p_inscripcion_ubs->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cursos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_cursos_ubs as $key => $p_curso_ubs)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_curso_ubs->id}}]" name="permiso[{{$p_curso_ubs->id}}]" value="permiso[{{$p_curso_ubs->id}}]">
                                                                <label class="form-check-label" for="{{$p_curso_ubs->name}}">{{$p_curso_ubs->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_cursos_ubs))
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
                                            <h6 class="card-title mb-3 text-white">Módulos de Cursos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_modulos_ubs as $p_modulo_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_modulo_ubs->id}}]" name="permiso[{{$p_modulo_ubs->id}}]" value="permiso[{{$p_modulo_ubs->id}}]">
                                                        <label class="form-check-label" for="{{$p_modulo_ubs->name}}">{{$p_modulo_ubs->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Tipos de Cursos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_cursos_ubs as $p_tipo_curso_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_tipo_curso_ubs->id}}]" name="permiso[{{$p_tipo_curso_ubs->id}}]" value="permiso[{{$p_tipo_curso_ubs->id}}]">
                                                        <label class="form-check-label" for="{{$p_tipo_curso_ubs->name}}">{{$p_tipo_curso_ubs->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Maestrías</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_maestrias_ubs as $key => $p_maestria_ubs)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_maestria_ubs->id}}]" name="permiso[{{$p_maestria_ubs->id}}]" value="permiso[{{$p_maestria_ubs->id}}]">
                                                                <label class="form-check-label" for="{{$p_maestria_ubs->name}}">{{$p_maestria_ubs->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_maestrias_ubs))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Tesis</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tesis_ubs as $key => $p_t_ubs)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_t_ubs->id}}]" name="permiso[{{$p_t_ubs->id}}]" value="permiso[{{$p_t_ubs->id}}]">
                                                                <label class="form-check-label" for="{{$p_t_ubs->name}}">{{$p_t_ubs->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 4 == 0 && $key + 1 < count($p_tesis_ubs))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-6 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Extensiones Universitarias</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_extensiones_ubs as $key => $p_extension_ubs)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_extension_ubs->id}}]" name="permiso[{{$p_extension_ubs->id}}]" value="permiso[{{$p_extension_ubs->id}}]">
                                                                <label class="form-check-label" for="{{$p_extension_ubs->name}}">{{$p_extension_ubs->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_extensiones_ubs))
                                                        </div><div class="row">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Docentes</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_docentes_ubs as $p_docente_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_docente_ubs->id}}]" name="permiso[{{$p_docente_ubs->id}}]" value="permiso[{{$p_docente_ubs->id}}]">
                                                        <label class="form-check-label" for="{{$p_docente_ubs->name}}">{{$p_docente_ubs->name}}</label>
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Ventas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_ventas as $p_venta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_venta->id}}]" name="permiso[{{$p_venta->id}}]" value="permiso[{{$p_venta->id}}]">
                                                        <label class="form-check-label" for="{{$p_venta->name}}">{{$p_venta->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Recibos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_recibos as $p_recibo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_recibo->id}}]" name="permiso[{{$p_recibo->id}}]" value="permiso[{{$p_recibo->id}}]">
                                                        <label class="form-check-label" for="{{$p_recibo->name}}">{{$p_recibo->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cobros</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cobros as $p_cobro)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cobro->id}}]" name="permiso[{{$p_cobro->id}}]" value="permiso[{{$p_cobro->id}}]">
                                                        <label class="form-check-label" for="{{$p_cobro->name}}">{{$p_cobro->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Notas de Crédito</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_notas_creditos as $p_nota_credito)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_nota_credito->id}}]" name="permiso[{{$p_nota_credito->id}}]" value="permiso[{{$p_nota_credito->id}}]">
                                                        <label class="form-check-label" for="{{$p_nota_credito->name}}">{{$p_nota_credito->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Arqueos de Cajas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_arqueos as $p_caja_arqueo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_caja_arqueo->id}}]" name="permiso[{{$p_caja_arqueo->id}}]" value="permiso[{{$p_caja_arqueo->id}}]">
                                                        <label class="form-check-label" for="{{$p_caja_arqueo->name}}">{{$p_caja_arqueo->name}}</label>
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
                            <h4 class="card-title mb-0">Compras</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Ordenes de Compras</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_compras_ordenes as $p_compra_orden)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_compra_orden->id}}]" name="permiso[{{$p_compra_orden->id}}]" value="permiso[{{$p_compra_orden->id}}]">
                                                        <label class="form-check-label" for="{{$p_compra_orden->name}}">{{$p_compra_orden->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Compras</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_compras as $p_compra)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_compra->id}}]" name="permiso[{{$p_compra->id}}]" value="permiso[{{$p_compra->id}}]">
                                                        <label class="form-check-label" for="{{$p_compra->name}}">{{$p_compra->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Ordenes de Pagos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos_ordenes as $p_pago_orden)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_pago_orden->id}}]" name="permiso[{{$p_pago_orden->id}}]" value="permiso[{{$p_pago_orden->id}}]">
                                                        <label class="form-check-label" for="{{$p_pago_orden->name}}">{{$p_pago_orden->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Pagos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos as $p_pago)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_pago->id}}]" name="permiso[{{$p_pago->id}}]" value="permiso[{{$p_pago->id}}]">
                                                        <label class="form-check-label" for="{{$p_pago->name}}">{{$p_pago->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xxl-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Proveedores</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_proveedores as $key => $p_proveedor)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_proveedor->id}}]" name="permiso[{{$p_proveedor->id}}]" value="permiso[{{$p_proveedor->id}}]">
                                                                <label class="form-check-label" for="{{$p_proveedor->name}}">{{$p_proveedor->name}}</label>
                                                            </div>
                                                        </li>
                                                    </div>
                                                    @if (($key + 1) % 2 == 0 && $key + 1 < count($p_proveedores))
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
                            <h4 class="card-title mb-0">Contabilidad</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Asientos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_asientos_contables as $p_asiento_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_asiento_contable->id}}]" name="permiso[{{$p_asiento_contable->id}}]" value="permiso[{{$p_asiento_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_asiento_contable->name}}">{{$p_asiento_contable->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Saldos de Cuentas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_contables_saldos as $p_cuenta_contable_saldo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cuenta_contable_saldo->id}}]" name="permiso[{{$p_cuenta_contable_saldo->id}}]" value="permiso[{{$p_cuenta_contable_saldo->id}}]">
                                                        <label class="form-check-label" for="{{$p_cuenta_contable_saldo->name}}">{{$p_cuenta_contable_saldo->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Artículos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_articulos as $p_articulo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_articulo->id}}]" name="permiso[{{$p_articulo->id}}]" value="permiso[{{$p_articulo->id}}]">
                                                        <label class="form-check-label" for="{{$p_articulo->name}}">{{$p_articulo->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cuentas Contables</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_contables as $p_cuenta_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cuenta_contable->id}}]" name="permiso[{{$p_cuenta_contable->id}}]" value="permiso[{{$p_cuenta_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_cuenta_contable->name}}">{{$p_cuenta_contable->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Tipos de Documentos Contables</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_documentos_contables as $p_tipo_documento_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_tipo_documento_contable->id}}]" name="permiso[{{$p_tipo_documento_contable->id}}]" value="permiso[{{$p_tipo_documento_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_tipo_documento_contable->name}}">{{$p_tipo_documento_contable->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Centros de Costos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_centros_costos_contables as $p_centro_costo_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_centro_costo_contable->id}}]" name="permiso[{{$p_centro_costo_contable->id}}]" value="permiso[{{$p_centro_costo_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_centro_costo_contable->name}}">{{$p_centro_costo_contable->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Unidades de Negocio</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_unidades_negocios_contables as $p_unidad_negocio_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_unidad_negocio_contable->id}}]" name="permiso[{{$p_unidad_negocio_contable->id}}]" value="permiso[{{$p_unidad_negocio_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_unidad_negocio_contable->name}}">{{$p_unidad_negocio_contable->name}}</label>
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
                            <h4 class="card-title mb-0">Tesorería</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cajas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas as $p_caja)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_caja->id}}]" name="permiso[{{$p_caja->id}}]" value="permiso[{{$p_caja->id}}]">
                                                        <label class="form-check-label" for="{{$p_caja->name}}">{{$p_caja->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Movimientos de Cajas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_movimientos as $p_caja_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_caja_movimiento->id}}]" name="permiso[{{$p_caja_movimiento->id}}]" value="permiso[{{$p_caja_movimiento->id}}]">
                                                        <label class="form-check-label" for="{{$p_caja_movimiento->name}}">{{$p_caja_movimiento->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Movimientos de Cuentas Bancarias</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_movimientos_cuentas as $p_movimiento_cuenta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_movimiento_cuenta->id}}]" name="permiso[{{$p_movimiento_cuenta->id}}]" value="permiso[{{$p_movimiento_cuenta->id}}]">
                                                        <label class="form-check-label" for="{{$p_movimiento_cuenta->name}}">{{$p_movimiento_cuenta->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Movimientos entre Cajas y Cuentas Bancarias</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_cuentas_movimientos as $p_caja_cuenta_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_caja_cuenta_movimiento->id}}]" name="permiso[{{$p_caja_cuenta_movimiento->id}}]" value="permiso[{{$p_caja_cuenta_movimiento->id}}]">
                                                        <label class="form-check-label" for="{{$p_caja_cuenta_movimiento->name}}">{{$p_caja_cuenta_movimiento->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Cuentas Bancarias</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_bancarias as $p_cuenta_bancaria)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cuenta_bancaria->id}}]" name="permiso[{{$p_cuenta_bancaria->id}}]" value="permiso[{{$p_cuenta_bancaria->id}}]">
                                                        <label class="form-check-label" for="{{$p_cuenta_bancaria->name}}">{{$p_cuenta_bancaria->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Bancos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_bancos as $p_banco)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_banco->id}}]" name="permiso[{{$p_banco->id}}]" value="permiso[{{$p_banco->id}}]">
                                                        <label class="form-check-label" for="{{$p_banco->name}}">{{$p_banco->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Cotizaciones</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cotizaciones as $p_cotizacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_cotizacion->id}}]" name="permiso[{{$p_cotizacion->id}}]" value="permiso[{{$p_cotizacion->id}}]">
                                                        <label class="form-check-label" for="{{$p_cotizacion->name}}">{{$p_cotizacion->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Convenios</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_convenios as $p_convenio)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_convenio->id}}]" name="permiso[{{$p_convenio->id}}]" value="permiso[{{$p_convenio->id}}]">
                                                        <label class="form-check-label" for="{{$p_convenio->name}}">{{$p_convenio->name}}</label>
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
                                            <h6 class="card-title mb-3 text-white">Formas de Pagos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos_formas as $p_pago_forma)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_pago_forma->id}}]" name="permiso[{{$p_pago_forma->id}}]" value="permiso[{{$p_pago_forma->id}}]">
                                                        <label class="form-check-label" for="{{$p_pago_forma->name}}">{{$p_pago_forma->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Monedas</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_monedas as $p_moneda)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_moneda->id}}]" name="permiso[{{$p_moneda->id}}]" value="permiso[{{$p_moneda->id}}]">
                                                        <label class="form-check-label" for="{{$p_moneda->name}}">{{$p_moneda->name}}</label>
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
                            <h4 class="card-title mb-0">Recursos Humanos</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Empleados</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_empleados as $p_empleado)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_empleado->id}}]" name="permiso[{{$p_empleado->id}}]" value="permiso[{{$p_empleado->id}}]">
                                                        <label class="form-check-label" for="{{$p_empleado->name}}">{{$p_empleado->name}}</label>
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Noticias y Avisos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_noticias_avisos as $p_noticia_aviso)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_noticia_aviso->id}}]" name="permiso[{{$p_noticia_aviso->id}}]" value="permiso[{{$p_noticia_aviso->id}}]">
                                                        <label class="form-check-label" for="{{$p_noticia_aviso->name}}">{{$p_noticia_aviso->name}}</label>
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
                            <h4 class="card-title mb-0">Parámetros</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">La Empresa</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_empresas as $p_empresa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_empresa->id}}]" name="permiso[{{$p_empresa->id}}]" value="permiso[{{$p_empresa->id}}]">
                                                        <label class="form-check-label" for="{{$p_empresa->name}}">{{$p_empresa->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Puntos de Impresión</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_puntos_impresiones as $p_punto_impresion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_punto_impresion->id}}]" name="permiso[{{$p_punto_impresion->id}}]" value="permiso[{{$p_punto_impresion->id}}]">
                                                        <label class="form-check-label" for="{{$p_punto_impresion->name}}">{{$p_punto_impresion->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Timbrados</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_timbrados as $p_timbrado)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_timbrado->id}}]" name="permiso[{{$p_timbrado->id}}]" value="permiso[{{$p_timbrado->id}}]">
                                                        <label class="form-check-label" for="{{$p_timbrado->name}}">{{$p_timbrado->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                            <h6 class="card-title mb-3 text-white">Países</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_paises as $p_pais)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_pais->id}}]" name="permiso[{{$p_pais->id}}]" value="permiso[{{$p_pais->id}}]">
                                                        <label class="form-check-label" for="{{$p_pais->name}}">{{$p_pais->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                            <h6 class="card-title mb-3 text-white">Tipos de Movimientos</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_movimientos as $p_tipo_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo" disabled type="checkbox" id="permiso[{{$p_tipo_movimiento->id}}]" name="permiso[{{$p_tipo_movimiento->id}}]" value="permiso[{{$p_tipo_movimiento->id}}]">
                                                        <label class="form-check-label" for="{{$p_tipo_movimiento->name}}">{{$p_tipo_movimiento->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <h6 class="card-title mb-3 text-white">Formas de Conocer USIL</h6>
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
