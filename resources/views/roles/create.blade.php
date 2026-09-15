@can('crear_roles')
    @extends('layouts.master')
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cajeros" name="chk_cajeros">
                                                <h6 class="card-title text-white">Cajero</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajeros as $p_cajero)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cajeros" type="checkbox" id="permission[{{$p_cajero->id}}]" name="permission[{{$p_cajero->id}}]" value="permission[{{$p_cajero->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_clientes" name="chk_clientes">
                                                <h6 class="card-title text-white">Clientes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_clientes as $p_cliente)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-clientes" type="checkbox" id="permission[{{$p_cliente->id}}]" name="permission[{{$p_cliente->id}}]" value="permission[{{$p_cliente->id}}]">
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_matriculaciones" name="chk_matriculaciones">
                                                <h6 class="card-title text-white">Matriculaciones</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_matriculaciones as $p_matriculacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-matriculaciones" type="checkbox" id="permission[{{$p_matriculacion->id}}]" name="permission[{{$p_matriculacion->id}}]" value="permission[{{$p_matriculacion->id}}]">
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_examenes_suficiencia" name="chk_examenes_suficiencia">
                                                <h6 class="card-title text-white">Exámenes de Suficiencia</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_examenes_suficiencia as $p_examen_suficiencia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-examenes_suficiencia" type="checkbox" id="permission[{{$p_examen_suficiencia->id}}]" name="permission[{{$p_examen_suficiencia->id}}]" value="permission[{{$p_examen_suficiencia->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_materias_semestres" name="chk_materias_semestres">
                                                <h6 class="card-title text-white">Materias por Semestres</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_semestres as $p_materia_semestre)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-materias_semestres" type="checkbox" id="permission[{{$p_materia_semestre->id}}]" name="permission[{{$p_materia_semestre->id}}]" value="permission[{{$p_materia_semestre->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_actas" name="chk_actas">
                                                <h6 class="card-title text-white">Actas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_actas as $p_acta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-actas" type="checkbox" id="permission[{{$p_acta->id}}]" name="permission[{{$p_acta->id}}]" value="permission[{{$p_acta->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_materias_clases" name="chk_materias_clases">
                                                <h6 class="card-title text-white">Clases</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_clases as $p_materia_clase)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-materias_clases" type="checkbox" id="permission[{{$p_materia_clase->id}}]" name="permission[{{$p_materia_clase->id}}]" value="permission[{{$p_materia_clase->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_anulaciones_correlatividades" name="chk_anulaciones_correlatividades">
                                                <h6 class="card-title text-white">Anulaciones de Correlatividades</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_anulaciones_correlatividades as $p_anulacion_correlativdad)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-anulaciones_correlatividades" type="checkbox" id="permission[{{$p_anulacion_correlativdad->id}}]" name="permission[{{$p_anulacion_correlativdad->id}}]" value="permission[{{$p_anulacion_correlativdad->id}}]">
                                                        <label class="form-check-label" for="{{$p_anulacion_correlativdad->name}}">{{$p_anulacion_correlativdad->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tesis" name="chk_tesis">
                                                <h6 class="card-title text-white">Trabajos Finales de Grado</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tesis as $key => $p_tfg)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-tesis" type="checkbox" id="permission[{{$p_tfg->id}}]" name="permission[{{$p_tfg->id}}]" value="permission[{{$p_tfg->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tutorias" name="chk_tutorias">
                                                <h6 class="card-title text-white">Tutorías</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tutorias as $key => $p_tutoria)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-tutorias" type="checkbox" id="permission[{{$p_tutoria->id}}]" name="permission[{{$p_tutoria->id}}]" value="permission[{{$p_tutoria->id}}]">
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_solicitudes" name="chk_solicitudes">
                                                <h6 class="card-title text-white">Solicitudes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_solicitudes as $p_solicitud)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-solicitudes" type="checkbox" id="permission[{{$p_solicitud->id}}]" name="permission[{{$p_solicitud->id}}]" value="permission[{{$p_solicitud->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_encuestas" name="chk_encuestas">
                                                <h6 class="card-title text-white">Encuestas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_encuestas as $p_encuesta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-encuestas" type="checkbox" id="permission[{{$p_encuesta->id}}]" name="permission[{{$p_encuesta->id}}]" value="permission[{{$p_encuesta->id}}]">
                                                        <label class="form-check-label" for="{{$p_encuesta->name}}">{{$p_encuesta->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_materias_suficiencia" name="chk_materias_suficiencia">
                                                <h6 class="card-title text-white">Materias Suficiencia</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_materias_suficiencia as $p_materia_suficiencia)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-materias_suficiencia" type="checkbox" id="permission[{{$p_materia_suficiencia->id}}]" name="permission[{{$p_materia_suficiencia->id}}]" value="permission[{{$p_materia_suficiencia->id}}]">
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_mallas" name="chk_mallas">
                                                <h6 class="card-title text-white">Mallas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_mallas as $p_malla)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-mallas" type="checkbox" id="permission[{{$p_malla->id}}]" name="permission[{{$p_malla->id}}]" value="permission[{{$p_malla->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_mallas_espejo" name="chk_mallas_espejo">
                                                <h6 class="card-title text-white">Mallas Espejo</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_mallas_espejo as $p_malla_espejo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-mallas_espejo" type="checkbox" id="permission[{{$p_malla_espejo->id}}]" name="permission[{{$p_malla_espejo->id}}]" value="permission[{{$p_malla_espejo->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_escalas" name="chk_escalas">
                                                <h6 class="card-title text-white">Escalas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_escalas as $p_escala)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-escalas" type="checkbox" id="permission[{{$p_escala->id}}]" name="permission[{{$p_escala->id}}]" value="permission[{{$p_escala->id}}]">
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_evaluaciones" name="chk_evaluaciones">
                                                <h6 class="card-title text-white">Evaluaciones</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_evaluaciones as $p_evaluacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-evaluaciones" type="checkbox" id="permission[{{$p_evaluacion->id}}]" name="permission[{{$p_evaluacion->id}}]" value="permission[{{$p_evaluacion->id}}]">
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
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_fechas_desmatriculaciones" name="chk_fechas_desmatriculaciones">
                                                <h6 class="card-title text-white">Fechas de Desmatriculación</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_fechas_desmatriculaciones as $p_fecha_desmatriculacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-fechas_desmatriculaciones" type="checkbox" id="permission[{{$p_fecha_desmatriculacion->id}}]" name="permission[{{$p_fecha_desmatriculacion->id}}]" value="permission[{{$p_fecha_desmatriculacion->id}}]">
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
                            <h4 class="card-title mb-0">Escuela de Negocios</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_alumnos_ubs" name="chk_alumnos_ubs">
                                                <h6 class="card-title text-white">Alumnos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_alumnos_ubs as $p_alumno_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-alumnos_ubs" type="checkbox" id="permission[{{$p_alumno_ubs->id}}]" name="permission[{{$p_alumno_ubs->id}}]" value="permission[{{$p_alumno_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_inscripciones_ubs" name="chk_inscripciones_ubs">
                                                <h6 class="card-title text-white">Inscripciones</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_inscripciones_ubs as $p_inscripcion_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-inscripciones_ubs" type="checkbox" id="permission[{{$p_inscripcion_ubs->id}}]" name="permission[{{$p_inscripcion_ubs->id}}]" value="permission[{{$p_inscripcion_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cursos_ubs" name="chk_cursos_ubs">
                                                <h6 class="card-title text-white">Cursos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_cursos_ubs as $key => $p_curso_ubs)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-cursos_ubs" type="checkbox" id="permission[{{$p_curso_ubs->id}}]" name="permission[{{$p_curso_ubs->id}}]" value="permission[{{$p_curso_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_modulos_ubs" name="chk_modulos_ubs">
                                                <h6 class="card-title text-white">Módulos de Cursos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_modulos_ubs as $p_modulo_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-modulos_ubs" type="checkbox" id="permission[{{$p_modulo_ubs->id}}]" name="permission[{{$p_modulo_ubs->id}}]" value="permission[{{$p_modulo_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tipos_cursos_ubs" name="chk_tipos_cursos_ubs">
                                                <h6 class="card-title text-white">Tipos de Cursos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_cursos_ubs as $p_tipo_curso_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-tipos_cursos_ubs" type="checkbox" id="permission[{{$p_tipo_curso_ubs->id}}]" name="permission[{{$p_tipo_curso_ubs->id}}]" value="permission[{{$p_tipo_curso_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_maestrias_ubs" name="chk_maestrias_ubs">
                                                <h6 class="card-title text-white">Maestrías</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_maestrias_ubs as $key => $p_maestria_ubs)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-maestrias_ubs" type="checkbox" id="permission[{{$p_maestria_ubs->id}}]" name="permission[{{$p_maestria_ubs->id}}]" value="permission[{{$p_maestria_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tesis_ubs" name="chk_tesis_ubs">
                                                <h6 class="card-title text-white">Tesis</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_tesis_ubs as $key => $p_t_ubs)
                                                    <div class="col-md-3">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-tesis_ubs" type="checkbox" id="permission[{{$p_t_ubs->id}}]" name="permission[{{$p_t_ubs->id}}]" value="permission[{{$p_t_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_extensiones_ubs" name="chk_extensiones_ubs">
                                                <h6 class="card-title text-white">Extensiones Universitarias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_extensiones_ubs as $key => $p_extension_ubs)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-extensiones_ubs" type="checkbox" id="permission[{{$p_extension_ubs->id}}]" name="permission[{{$p_extension_ubs->id}}]" value="permission[{{$p_extension_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_docentes_ubs" name="chk_docentes_ubs">
                                                <h6 class="card-title text-white">Docentes</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_docentes_ubs as $p_docente_ubs)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-docentes_ubs" type="checkbox" id="permission[{{$p_docente_ubs->id}}]" name="permission[{{$p_docente_ubs->id}}]" value="permission[{{$p_docente_ubs->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_ventas" name="chk_ventas">
                                                <h6 class="card-title text-white">Ventas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_ventas as $p_venta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-ventas" type="checkbox" id="permission[{{$p_venta->id}}]" name="permission[{{$p_venta->id}}]" value="permission[{{$p_venta->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_recibos" name="chk_recibos">
                                                <h6 class="card-title text-white">Recibos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_recibos as $p_recibo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-recibos" type="checkbox" id="permission[{{$p_recibo->id}}]" name="permission[{{$p_recibo->id}}]" value="permission[{{$p_recibo->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cobros" name="chk_cobros">
                                                <h6 class="card-title text-white">Cobros</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cobros as $p_cobro)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cobros" type="checkbox" id="permission[{{$p_cobro->id}}]" name="permission[{{$p_cobro->id}}]" value="permission[{{$p_cobro->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_notas_creditos" name="chk_notas_creditos">
                                                <h6 class="card-title text-white">Notas de Crédito</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_notas_creditos as $p_nota_credito)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-notas_creditos" type="checkbox" id="permission[{{$p_nota_credito->id}}]" name="permission[{{$p_nota_credito->id}}]" value="permission[{{$p_nota_credito->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cajas_arqueos" name="chk_cajas_arqueos">
                                                <h6 class="card-title text-white">Arqueos de Cajas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_arqueos as $p_caja_arqueo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-caja_arqueos" type="checkbox" id="permission[{{$p_caja_arqueo->id}}]" name="permission[{{$p_caja_arqueo->id}}]" value="permission[{{$p_caja_arqueo->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_compras_ordenes" name="chk_compras_ordenes">
                                                <h6 class="card-title text-white">Ordenes de Compras</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_compras_ordenes as $p_compra_orden)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-compras_ordenes" type="checkbox" id="permission[{{$p_compra_orden->id}}]" name="permission[{{$p_compra_orden->id}}]" value="permission[{{$p_compra_orden->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_compras" name="chk_compras">
                                                <h6 class="card-title text-white">Compras</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_compras as $p_compra)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-compras" type="checkbox" id="permission[{{$p_compra->id}}]" name="permission[{{$p_compra->id}}]" value="permission[{{$p_compra->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_pagos_ordenes" name="chk_pagos_ordenes">
                                                <h6 class="card-title text-white">Ordenes de Pagos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos_ordenes as $p_pago_orden)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-pagos_ordenes" type="checkbox" id="permission[{{$p_pago_orden->id}}]" name="permission[{{$p_pago_orden->id}}]" value="permission[{{$p_pago_orden->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_pagos" name="chk_pagos">
                                                <h6 class="card-title text-white">Pagos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos as $p_pago)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-pagos" type="checkbox" id="permission[{{$p_pago->id}}]" name="permission[{{$p_pago->id}}]" value="permission[{{$p_pago->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_proveedores" name="chk_proveedores">
                                                <h6 class="card-title text-white">Proveedores</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($p_proveedores as $key => $p_proveedor)
                                                    <div class="col-md-6">
                                                        <li class="list-group-item">
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input hijo hijo-proveedores" type="checkbox" id="permission[{{$p_proveedor->id}}]" name="permission[{{$p_proveedor->id}}]" value="permission[{{$p_proveedor->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_asientos_contables" name="chk_asientos_contables">
                                                <h6 class="card-title text-white">Asientos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_asientos_contables as $p_asiento_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-asientos_contables" type="checkbox" id="permission[{{$p_asiento_contable->id}}]" name="permission[{{$p_asiento_contable->id}}]" value="permission[{{$p_asiento_contable->id}}]">
                                                        <label class="form-check-label" for="{{$p_asiento_contable->name}}">{{$p_asiento_contable->name}}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cuentas_contables_saldos" name="chk_cuentas_contables_saldos">
                                                <h6 class="card-title text-white">Saldos de Cuetnas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_contables_saldos as $p_cuenta_contable_saldo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cuentas_contables_saldos" type="checkbox" id="permission[{{$p_cuenta_contable_saldo->id}}]" name="permission[{{$p_cuenta_contable_saldo->id}}]" value="permission[{{$p_cuenta_contable_saldo->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_articulos" name="chk_articulos">
                                                <h6 class="card-title text-white">Artículos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_articulos as $p_articulo)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-articulos" type="checkbox" id="permission[{{$p_articulo->id}}]" name="permission[{{$p_articulo->id}}]" value="permission[{{$p_articulo->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cuentas_contables" name="chk_cuentas_contables">
                                                <h6 class="card-title text-white">Cuentas Contables</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_contables as $p_cuenta_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cuentas_contables" type="checkbox" id="permission[{{$p_cuenta_contable->id}}]" name="permission[{{$p_cuenta_contable->id}}]" value="permission[{{$p_cuenta_contable->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tipos_documentos_contables" name="chk_tipos_documentos_contables">
                                                <h6 class="card-title text-white">Tipos de Documentos Contables</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_documentos_contables as $p_tipo_documento_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-tipos_documentos_contables" type="checkbox" id="permission[{{$p_tipo_documento_contable->id}}]" name="permission[{{$p_tipo_documento_contable->id}}]" value="permission[{{$p_tipo_documento_contable->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_centros_costos_contables" name="chk_centros_costos_contables">
                                                <h6 class="card-title text-white">Centros de Costos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_centros_costos_contables as $p_centro_costo_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-centros_costos_contables" type="checkbox" id="permission[{{$p_centro_costo_contable->id}}]" name="permission[{{$p_centro_costo_contable->id}}]" value="permission[{{$p_centro_costo_contable->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_unidades_negocios_contables" name="chk_unidades_negocios_contables">
                                                <h6 class="card-title text-white">Unidades de Negocio</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_unidades_negocios_contables as $p_unidad_negocio_contable)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-unidades_negocios_contables" type="checkbox" id="permission[{{$p_unidad_negocio_contable->id}}]" name="permission[{{$p_unidad_negocio_contable->id}}]" value="permission[{{$p_unidad_negocio_contable->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cajas" name="chk_cajas">
                                                <h6 class="card-title text-white">Cajas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas as $p_caja)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cajas" type="checkbox" id="permission[{{$p_caja->id}}]" name="permission[{{$p_caja->id}}]" value="permission[{{$p_caja->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cajas_movimientos" name="chk_cajas_movimientos">
                                                <h6 class="card-title text-white">Movimientos de Cajas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_movimientos as $p_caja_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cajas_movimientos" type="checkbox" id="permission[{{$p_caja_movimiento->id}}]" name="permission[{{$p_caja_movimiento->id}}]" value="permission[{{$p_caja_movimiento->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_movimientos_cuentas" name="chk_movimientos_cuentas">
                                                <h6 class="card-title text-white">Movimientos de Cuentas Bancarias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_movimientos_cuentas as $p_movimiento_cuenta)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-movimientos_cuentas" type="checkbox" id="permission[{{$p_movimiento_cuenta->id}}]" name="permission[{{$p_movimiento_cuenta->id}}]" value="permission[{{$p_movimiento_cuenta->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cajas_cuentas_movimientos" name="chk_cajas_cuentas_movimientos">
                                                <h6 class="card-title text-white">Movimientos entre Cajas y Cuentas Bancarias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cajas_cuentas_movimientos as $p_caja_cuenta_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cajas_cuentas_movimientos" type="checkbox" id="permission[{{$p_caja_cuenta_movimiento->id}}]" name="permission[{{$p_caja_cuenta_movimiento->id}}]" value="permission[{{$p_caja_cuenta_movimiento->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cuentas_bancarias" name="chk_cuentas_bancarias">
                                                <h6 class="card-title text-white">Cuentas Bancarias</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cuentas_bancarias as $p_cuenta_bancaria)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cuentas_bancarias" type="checkbox" id="permission[{{$p_cuenta_bancaria->id}}]" name="permission[{{$p_cuenta_bancaria->id}}]" value="permission[{{$p_cuenta_bancaria->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_bancos" name="chk_bancos">
                                                <h6 class="card-title text-white">Bancos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_bancos as $p_banco)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-bancos" type="checkbox" id="permission[{{$p_banco->id}}]" name="permission[{{$p_banco->id}}]" value="permission[{{$p_banco->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_cotizaciones" name="chk_cotizaciones">
                                                <h6 class="card-title text-white">Cotizaciones</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_cotizaciones as $p_cotizacion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-cotizaciones" type="checkbox" id="permission[{{$p_cotizacion->id}}]" name="permission[{{$p_cotizacion->id}}]" value="permission[{{$p_cotizacion->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_convenios" name="chk_convenios">
                                                <h6 class="card-title text-white">Convenios</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_convenios as $p_convenio)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-convenios" type="checkbox" id="permission[{{$p_convenio->id}}]" name="permission[{{$p_convenio->id}}]" value="permission[{{$p_convenio->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_pagos_formas" name="chk_pagos_formas">
                                                <h6 class="card-title text-white">Formas de Pagos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_pagos_formas as $p_pago_forma)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-pagos_formas" type="checkbox" id="permission[{{$p_pago_forma->id}}]" name="permission[{{$p_pago_forma->id}}]" value="permission[{{$p_pago_forma->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_monedas" name="chk_monedas">
                                                <h6 class="card-title text-white">Monedas</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_monedas as $p_moneda)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-monedas" type="checkbox" id="permission[{{$p_moneda->id}}]" name="permission[{{$p_moneda->id}}]" value="permission[{{$p_moneda->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_empleados" name="chk_empleados">
                                                <h6 class="card-title text-white">Empleados</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_empleados as $p_empleado)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-empleados" type="checkbox" id="permission[{{$p_empleado->id}}]" name="permission[{{$p_empleado->id}}]" value="permission[{{$p_empleado->id}}]">
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
                                <div class="col-xxl-3 col-lg-12">
                                    <div class="card">
                                        <div class="card-info card-header">
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_noticias_avisos" name="chk_noticias_avisos">
                                                <h6 class="card-title text-white">Noticias y Avisos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_noticias_avisos as $p_noticia_aviso)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-noticias_avisos" type="checkbox" id="permission[{{$p_noticia_aviso->id}}]" name="permission[{{$p_noticia_aviso->id}}]" value="permission[{{$p_noticia_aviso->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_empresas" name="chk_empresas">
                                                <h6 class="card-title text-white">La Empresa</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_empresas as $p_empresa)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-empresas" type="checkbox" id="permission[{{$p_empresa->id}}]" name="permission[{{$p_empresa->id}}]" value="permission[{{$p_empresa->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_puntos_impresiones" name="chk_puntos_impresiones">
                                                <h6 class="card-title text-white">Puntos de Impresión</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_puntos_impresiones as $p_punto_impresion)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-puntos_impresiones" type="checkbox" id="permission[{{$p_punto_impresion->id}}]" name="permission[{{$p_punto_impresion->id}}]" value="permission[{{$p_punto_impresion->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_timbrados" name="chk_timbrados">
                                                <h6 class="card-title text-white">Timbrados</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_timbrados as $p_timbrado)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-timbrados" type="checkbox" id="permission[{{$p_timbrado->id}}]" name="permission[{{$p_timbrado->id}}]" value="permission[{{$p_timbrado->id}}]">
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_paises" name="chk_paises">
                                                <h6 class="card-title text-white">Países</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_paises as $p_pais)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-paises" type="checkbox" id="permission[{{$p_pais->id}}]" name="permission[{{$p_pais->id}}]" value="permission[{{$p_pais->id}}]">
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
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_tipos_movimientos" name="chk_tipos_movimientos">
                                                <h6 class="card-title text-white">Tipos de Movimientos</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($p_tipos_movimientos as $p_tipo_movimiento)
                                                <li class="list-group-item">
                                                    <div class="form-check form-check-info">
                                                        <input class="form-check-input hijo hijo-tipos_movimientos" type="checkbox" id="permission[{{$p_tipo_movimiento->id}}]" name="permission[{{$p_tipo_movimiento->id}}]" value="permission[{{$p_tipo_movimiento->id}}]">
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
                                            <div class="form-check form-check-dark mb-3">
                                                <input type="checkbox" class="form-check-input float-end fs-xxs padre" id="chk_formas_conocimientos" name="chk_formas_conocimientos">
                                                <h6 class="card-title text-white">Formas de Conocer USIL</h6>
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
