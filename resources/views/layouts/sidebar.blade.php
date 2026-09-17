<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{route('root')}}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{asset('storage/logos/logo-dark-sm.png')}}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{asset('storage/logos/logo-dark.png')}}" alt="" height="50">
            </span>
        </a>
        <a href="{{route('root')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{asset('storage/logos/logo-light-sm.png')}}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{asset('storage/logos/logo-light.png')}}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover shadow-none" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @unless(Auth::user()->hasAnyRole(['ALUMNO', 'DOCENTE', 'ENCARGADO_DOCENTE']))
                    <li class="menu-title"><span data-key="t-menu">Menú Principal</span></li>
                    @if (Auth::user()->can('crear_ventas_cajero') || Auth::user()->can('ver_arqueos_cajero') || Auth::user()->can('crear_arqueos_cajas_cajero'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarATC" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarATC">
                                <i class="ri-group-line"></i> <span data-key="t-atc">ATC</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('cajero.*', 'arqueo_caja.*', 'clientes.*') ? 'show' : '' }}" id="sidebarATC">
                                <ul class="nav nav-sm flex-column">
                                    @can('crear_ventas_cajero')
                                        <li class="nav-item">
                                            <a href="{{route('cajero.create')}}" class="nav-link {{ request()->routeIs('cajero.*') ? 'active' : '' }}" data-key="t-cajero">Caja</a>
                                        </li>
                                    @endcan
                                    @can('crear_arqueos_cajas_cajero')
                                        <li class="nav-item">
                                            <a href="{{route('arqueo_caja.create')}}" class="nav-link {{ request()->routeIs('arqueo_caja.create') ? 'active' : '' }}" data-key="t-arqueo-caja">Arqueo de Caja</a>
                                        </li>
                                    @endcan
                                    @can('ver_arqueos_cajas_cajero')
                                        <li class="nav-item">
                                            <a href="{{route('arqueo_caja.index')}}" class="nav-link {{ request()->routeIs('arqueo_caja.index') ? 'active' : '' }}" data-key="t-mis-arqueo-caja">Mis Arqueos</a>
                                        </li>
                                    @endcan
                                    @can('ver_clientes')
                                        <li class="nav-item">
                                            <a href="{{route('clientes.index')}}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" data-key="t-clientes">Clientes</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_alumnos') || Auth::user()->can('ver_matriculaciones') || Auth::user()->can('ver_inscricpiones_matriculaciones') ||  Auth::user()->can('ver_docentes') || Auth::user()->can('ver_examenes_suficiencias') || Auth::user()->can('ver_materias_semestres') || Auth::user()->can('ver_evalaciones_materias_semestres') || Auth::user()->can('ver_actas') || Auth::user()->can('ver_materias_clases') || Auth::user()->can('ver_salarios_docentes') || Auth::user()->can('ver_convalidaciones_externas') || Auth::user()->can('ver_convalidaciones_internas') || Auth::user()->can('ver_tesis') || Auth::user()->can('ver_tutorias') || Auth::user()->can('ver_clases_tutorias') || Auth::user()->can('ver_evaluaciones_tutorias') || Auth::user()->can('ver_precios_tutorias') || Auth::user()->can('ver_extensiones_universitarias') || Auth::user()->can('ver_solicitudes') || Auth::user()->can('ver_encuestas') || Auth::user()->can('ver_parametros_academicos'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarAcademico" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAcademico">
                                <i class="bi bi-mortarboard"></i> <span data-key="t-academico">Académico</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('alumnos.*', 'certificados_estudios.*', 'matriculaciones.*', 'inscripciones.*', 'docentes.*', 'examenes_suficiencia.*', 'materias_semestres.*', 'materias_evaluaciones.*', 'actas_evaluaciones.*', 'clases_materias.*', 'alumnos_asistencias.*', 'docentes_salarios.*', 'convalidaciones.*', 'convalidaciones_externas.*', 'convalidaciones_internas.*', 'tesis.*', 'inscripciones_temas_tesis.*', 'anteproyectos_tesis.*', 'proyectos_tesis.*', 'borradores_tesis.*', 'fechas_defensas_tesis.*', 'tipos_tesis.*', 'areas_tesis.*', 'lineas_tesis.*', 'requerimientos_entregas_tesis.*', 'bloques_anteproyectos_tesis.*', 'bloques_proyectos_tesis.*', 'bloques_borradores_tesis.*', 'rubricas_tesis.*', 'tutorias.*', 'tutorias_clases.*', 'tutorias_evaluaciones.*', 'tutorias_precios.*', 'extensiones_universitarias.*', 'solicitudes.*', 'encuestas.*', 'reportes_academicos.*', 'parametros_academicos.*', 'programas.*', 'facultades.*', 'carreras.*', 'tipos_carreras.*', 'mallas.*', 'materias.*',  'semestres.*', 'semestre_mallas.*', 'semestres_mallas_materias.*', 'semestres_mallas_materias_horarios.*', 'modalidades.*', 'mallas_espejos.*', 'escalas.*', 'evaluaciones.*', 'tipos_evaluaciones.*', 'tipos_extensiones_universitarias.*', 'requerimientos_extensiones_universitarias.*', 'alumnos_formaciones.*', 'instituciones_educativas.*', 'tipos_solicitudes.*') ? 'show' : '' }}" id="sidebarAcademico">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_alumnos')
                                        <li class="nav-item">
                                            <a href="{{route('alumnos.index')}}" class="nav-link {{ request()->routeIs('alumnos.*', 'certificados_estudios.*') ? 'active' : '' }}" data-key="t-alumnos">Alumnos</a>
                                        </li>
                                    @endcan
                                    @can('ver_matriculaciones')
                                    <li class="nav-item">
                                        <a href="{{route('matriculaciones.index')}}" class="nav-link {{ request()->routeIs('matriculaciones.*', 'inscripciones.*') ? 'active' : '' }}" data-key="t-matriculaciones">Matriculaciones</a>
                                    </li>
                                    @endcan
                                    @can('ver_docentes')
                                    <li class="nav-item">
                                        <a href="{{route('docentes.index')}}" class="nav-link {{ request()->routeIs('docentes.*') ? 'active' : '' }}" data-key="t-docentes">Docentes</a>
                                    </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_examenes_suficiencia') || Auth::user()->can('ver_materias_semestres') || Auth::user()->can('ver_evalauciones_materias_semestres') || Auth::user()->can('ver_actas') || Auth::user()->can('ver_materias_clases') || Auth::user()->can('ver_anulaciones_correlatividades') || Auth::user()->can('ver_salarios_docentes'))
                                        <li class="nav-item">
                                            <a href="#sidebarGestiones" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarGestiones" data-key="t-gestiones">Gestiones</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('examenes_suficiencia.*', 'materias_semestres.*', 'materias_evaluaciones.*', 'actas_evaluaciones.*', 'clases_materias.*', 'alumnos_asistencias.*', 'anulaciones_correlatividades.*', 'docentes_salarios.*') ? 'show' : '' }}" id="sidebarGestiones">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_examenes_suficiencia')
                                                        <li class="nav-item">
                                                            <a href="{{route('examenes_suficiencia.index')}}" class="nav-link {{ request()->routeIs('examenes_suficiencia.*') ? 'active' : '' }}" data-key="t-examenes-suficiencias">Exámenes de Suficiencia</a>
                                                        </li>
                                                    @endcan
                                                    @if (Auth::user()->can('ver_materias_semestres') || Auth::user()->can('ver_evaluaciones_materias_semestres') || Auth::user()->can('ver_actas') || Auth::user()->can('ver_materias_clases'))
                                                        <li class="nav-item">
                                                            <a href="{{route('materias_semestres.index')}}" class="nav-link {{ request()->routeIs('materias_semestres.*', 'materias_evaluaciones.*', 'alumnos_asistencias.*') ? 'active' : '' }}" data-key="t-materias-semestres">Materias por Semestres</a>
                                                        </li>
                                                    @endif
                                                    @can('ver_actas')
                                                        <li class="nav-item">
                                                            <a href="{{route('actas_evaluaciones.index')}}" class="nav-link {{ request()->routeIs('actas_evaluaciones.*') ? 'active' : '' }}" data-key="t-actas-evaluaciones">Actas</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_materias_clases')
                                                        <li class="nav-item">
                                                            <a href="{{route('clases_materias.index')}}" class="nav-link {{ request()->routeIs('clases_materias.*') ? 'active' : '' }}" data-key="t-clases-materias">Clases</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="{{route('seguimiento_docente.index')}}" class="nav-link {{ request()->routeIs('seguimiento_docente.*') ? 'active' : '' }}" data-key="t-seguimiento-docente">Seguimiento Docente</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_anulaciones_correlatividades')
                                                        <li class="nav-item">
                                                            <a href="{{route('anulaciones_correlatividades.index')}}" class="nav-link {{ request()->routeIs('anuluacion_correlatividades.*') ? 'active' : '' }}" data-key="t-anulaciones-correlatividades">Anulaciones de Correlatividades</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_salarios_docentes')
                                                        <li class="nav-item">
                                                            <a href="{{route('docentes_salarios.index')}}" class="nav-link {{ request()->routeIs('docentes_salarios.*') ? 'active' : '' }}" data-key="t-docentes-salarios">Salarios de Docentes</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @if (Auth::user()->can('ver_convalidaciones_externas') || Auth::user()->can('ver_convalidaciones_internas'))
                                        <li class="nav-item">
                                            <a href="#sidebarConvalidaciones" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarConvalidaciones" data-key="t-convalidaciones">Convalidaciones</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('convalidaciones.*', 'convalidaciones_externas.*', 'convalidaciones_internas.*') ? 'show' : '' }}" id="sidebarConvalidaciones">
                                                <ul class="nav nav-sm flex-column">
                                                    @if (Auth::user()->can('ver_convalidaciones_externas') || Auth::user()->can('ver_convalidaciones_internas'))
                                                        <li class="nav-item">
                                                            <a href="{{route('convalidaciones.index')}}" class="nav-link {{ request()->routeIs('convalidaciones.*') ? 'active' : '' }}" data-key="t-convalidaciones">Todas</a>
                                                        </li>
                                                    @endif
                                                    @can('ver_convalidaciones_externas')
                                                        <li class="nav-item">
                                                            <a href="{{route('convalidaciones_externas.index')}}" class="nav-link {{ request()->routeIs('convalidaciones_externas.*') ? 'active' : '' }}" data-key="t-convalidaciones-externas">Externas</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_convalidaciones_internas')
                                                        <li class="nav-item">
                                                            <a href="{{route('convalidaciones_internas.index')}}" class="nav-link {{ request()->routeIs('convalidaciones_internas.*') ? 'active' : '' }}" data-key="t-convalidaciones-internas">Internas</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_tesis')
                                        <li class="nav-item">
                                            <a href="{{route('tesis.index')}}" class="nav-link {{ request()->routeIs('tesis.*', 'inscripciones_temas_tesis.*', 'anteproyectos_tesis.*', 'proyectos_tesis.*', 'borradores_tesis.*', 'fechas_defensas_tesis.*', 'tipos_tesis.*', 'areas_tesis.*', 'lineas_tesis.*', 'requerimientos_entregas_tesis.*', 'bloques_anteproyectos_tesis.*', 'bloques_proyectos_tesis.*', 'bloques_borradores_tesis.*', 'rubricas_tesis.*') ? 'active' : '' }}" data-key="t-tesis">Trabajos Finales de Grado</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_tutorias') || Auth::user()->can('ver_precios_tutorias'))
                                        <li class="nav-item">
                                            <a href="#sidebarTutorias" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTutorias" data-key="t-convalidaciones">Tutorías</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('tutorias.*', 'tutorias_clases.*', 'tutorias_evaluaciones.*', 'tutorias_precios.*') ? 'show' : '' }}" id="sidebarTutorias">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_tutorias')
                                                        <li class="nav-item">
                                                            <a href="{{route('tutorias.index')}}" class="nav-link {{ request()->routeIs('tutorias.*', 'tutorias_clases.*', 'tutorias_evaluaciones.*') ? 'active' : '' }}" data-key="t-tutorias">Lista de Tutorías</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_precios_tutorias')
                                                    <li class="nav-item">
                                                        <a href="{{route('tutorias_precios.index')}}" class="nav-link {{ request()->routeIs('tutorias_precios.*') ? 'active' : '' }}" data-key="t-tutorias-precios">Precio de Tutorías</a>
                                                    </li>
                                                @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_extensiones_universitarias')
                                        @if (Auth::user()->can('ver_tipos_extensiones_universitarias') || Auth::user()->can('ver_requerimientos_extensiones_universitarias'))
                                            <li class="nav-item">
                                                <a class="nav-link menu-link collapsed" href="#sidebarExtensionesUniversitarias" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarExtensionesUniversitarias" data-key="t-extensiones-universitarias">Extensiones Univ.</a>
                                                <div class="menu-dropdown collapse {{ request()->routeIs('extensiones_universitarias.*', 'tipos_extensiones_universitarias.*', 'requerimientos_extensiones_universitarias.*') ? 'show' : '' }}" id="sidebarExtensionesUniversitarias">
                                                    <ul class="nav nav-sm flex-column">
                                                        <li class="nav-item">
                                                            <a href="{{route('extensiones_universitarias.index')}}" class="nav-link {{ request()->routeIs('extensiones_universitarias.*') ? 'active' : '' }}" data-key="t-extensiones-universitarias-actividades">Actividades</a>
                                                        </li>
                                                        @can('ver_tipos_extensiones_universitarias')
                                                            <li class="nav-item">
                                                                <a href="{{route('tipos_extensiones_universitarias.index')}}" class="nav-link {{ request()->routeIs('tipos_extensiones_universitarias.*') ? 'active' : '' }}" data-key="t-tipos-extensiones-universitarias">Tipos de Actividad</a>
                                                            </li>
                                                        @endcan
                                                        @can('ver_requerimientos_extensiones_universitarias')
                                                            <li class="nav-item">
                                                                <a href="{{route('requerimientos_extensiones_universitarias.show')}}" class="nav-link {{ request()->routeIs('requerimientos_extensiones_universitarias.*') ? 'active' : '' }}" data-key="t-requerimientos-extensiones-universitarias">Requerimientos</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </li>
                                        @else
                                            <li class="nav-item">
                                                <a href="{{route('extensiones_universitarias.index')}}" class="nav-link {{ request()->routeIs('extensiones_universitarias.*') ? 'active' : '' }}" data-key="t-extensiones-universitarias">Extensiones Univ.</a>
                                            </li>
                                        @endif
                                    @endcan
                                    @can('ver_solicitudes')
                                        <li class="nav-item">
                                            <a href="{{route('solicitudes.index')}}" class="nav-link {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}" data-key="t-solicitudes">Solicitudes de Alumnos</a>
                                        </li>
                                    @endcan
                                    @can('ver_encuestas')
                                        <li class="nav-item">
                                            <a href="{{route('encuestas.index')}}" class="nav-link {{ request()->routeIs('encuestas.*') ? 'active' : '' }}" data-key="t-encuestas">Encuestas</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_reportes_academicos_alumnos_ingresos'))
                                        <li class="nav-item">
                                            <a href="#sidebarReportesAcademicos" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarReportesAcademicos" data-key="t-gestiones">Reportes</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('reportes_academicos.*') ? 'show' : '' }}" id="sidebarReportesAcademicos">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_reportes_academicos_alumnos_ingresos')
                                                        <li class="nav-item">
                                                            <a href="{{route('reportes_academicos.create_alumnos_ingresos')}}" class="nav-link {{ request()->routeIs('reportes_academicos.create_alumnos_ingresos') ? 'active' : '' }}" data-key="t-reportes-alumnos-ingresos">Año Ingreso Alumnos</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_reportes_academicos_salas_clases')
                                                        <li class="nav-item">
                                                            <a href="{{route('reportes_academicos.create_salas_clases')}}" class="nav-link {{ request()->routeIs('reportes_academicos.create_salas_clases') ? 'active' : '' }}" data-key="t-reportes-salas-clases">Salas de Clases</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_reportes_academicos_fechas_examenes')
                                                        <li class="nav-item">
                                                            <a href="{{route('reportes_academicos.create_fechas_examenes')}}" class="nav-link {{ request()->routeIs('reportes_academicos.create_fechas_examenes') ? 'active' : '' }}" data-key="t-reportes-fechas-examenes">Fechas de Exámenes</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_parametros_academicos')
                                        <li class="nav-item">
                                            <a href="{{route('parametros_academicos.index')}}" class="nav-link {{ request()->routeIs('parametros_academicos.*', 'programas.*', 'facultades.*', 'carreras.*', 'tipos_carreras.*', 'mallas.*', 'materias.*', 'correlatividades.*', 'semestres.*', 'modalidades.*', 'mallas_espejos.*', 'escalas.*', 'evaluaciones.*', 'tipos_evaluaciones.*', 'tipos_extensiones_universitarias.*', 'requerimientos_extensiones_universitarias.*', 'alumnos_formaciones.*', 'instituciones_educativas.*', 'tipos_solicitudes.*') ? 'active' : '' }}" data-key="t-parametros-academicos">Parámetros</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_alumnos_ubs') || Auth::user()->can('ver_inscripciones_ubs') || Auth::user()->can('ver_cursos_ubs') || Auth::user()->can('ver_maestrias_ubs') || Auth::user()->can('ver_docentes_ubs'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarUBS" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUBS">
                                <i class="ri-group-line"></i> <span data-key="t-ubs">Escuela de Negocios</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('alumnos_ubs.*', 'certificados_estudios_ubs.*', 'inscripciones_ubs.*', 'cursos.*', 'modulos.*', 'alumnos_asistencias_ubs.*', 'cursos_certificados.*', 'tipos_cursos.*', 'maestrias.*', 'modulos_maestrias.*', 'alumnos_notas_ubs.*', 'actas_evaluaciones_ubs.*', 'extensiones_universitarias_ubs.*', 'tipos_extensiones_universitarias_ubs.*', 'requerimientos_extensiones_universitarias_ubs.*', 'tesis_ubs.*', 'tesis_parametros_ubs.*', 'docentes_ubs.*') ? 'show' : '' }}" id="sidebarUBS">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_alumnos_ubs')
                                        <li class="nav-item">
                                            <a href="{{route('alumnos_ubs.index')}}" class="nav-link {{ request()->routeIs('alumnos_ubs.*', 'certificados_estudios_ubs.*') ? 'active' : '' }}" data-key="t-alumnos-ubs">Alumnos</a>
                                        </li>
                                    @endcan
                                    @can('ver_inscripciones_ubs')
                                        <li class="nav-item">
                                            <a href="{{route('inscripciones_ubs.index')}}" class="nav-link {{ request()->routeIs('inscripciones_ubs.*') ? 'active' : '' }}" data-key="t-inscripciones-ubs">Inscripciones</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_cursos_ubs') || Auth::user()->can('ver_modulos_ubs') || Auth::user()->can('ver_tipos_cursos_ubs'))
                                        <li class="nav-item">
                                            <a href="#sidebarCursos" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCursos" data-key="t-cursos">Cursos</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('cursos.*', 'alumnos_asistencias_ubs.*', 'cursos_certificados.*', 'modulos.*', 'tipos_cursos.*') ? 'show' : '' }}" id="sidebarCursos">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_cursos_ubs')
                                                        <li class="nav-item">
                                                            <a href="{{route('cursos.index')}}" class="nav-link {{ request()->routeIs('cursos.*', 'alumnos_asistencias_ubs.*', 'cursos_certificados.*') ? 'active' : '' }}" data-key="t-cursos">Lista de Cursos</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_modulos_ubs')
                                                        <li class="nav-item">
                                                            <a href="{{route('modulos.index')}}" class="nav-link {{ request()->routeIs('modulos.*') ? 'active' : '' }}" data-key="t-modulos">Módulos</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_tipos_cursos_ubs')
                                                        <li class="nav-item">
                                                            <a href="{{route('tipos_cursos.index')}}" class="nav-link {{ request()->routeIs('tipos_cursos.*') ? 'active' : '' }}" data-key="t-tipos-cursos">Tipos de Cursos</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @if (Auth::user()->can('ver_maestrias_ubs') || Auth::user()->can('ver_modulos_maestrias_ubs') || Auth::user()->can('ver_extensiones_ubs') || Auth::user()->can('ver_tesis_ubs'))
                                        <li class="nav-item">
                                            <a href="#sidebarMaestrias" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMaestrias" data-key="t-maestrias">Maestrías</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('maestrias.*', 'modulos_maestrias.*', 'alumnos_notas_ubs.*', 'actas_evaluaciones_ubs.*', 'extensiones_universitarias_ubs.*', 'tipos_extensiones_universitarias_ubs.*', 'requerimientos_extensiones_universitarias_ubs.*', 'tesis_ubs.*', 'tesis_parametros_ubs.*') ? 'show' : '' }}" id="sidebarMaestrias">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver maestrias_ubs')
                                                        <li class="nav-item">
                                                            <a href="{{route('maestrias.index')}}" class="nav-link {{ request()->routeIs('maestrias.*', 'alumnos_notas_ubs.*', 'actas_evaluaciones_ubs.*') ? 'active' : '' }}" data-key="t-maestrias">Lista de Maestrías</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_modulos_maestrias_ubs')
                                                        <li class="nav-item">
                                                            <a href="{{route('modulos_maestrias.index')}}" class="nav-link {{ request()->routeIs('modulos_maestrias.*') ? 'active' : '' }}" data-key="t-modulos-maestrias">Módulos</a>
                                                        </li>
                                                    @endcan
                                                    @if (Auth::user()->can('ver_extensiones_ubs') || Auth::user()->can('ver_tipos_extensiones_ubs') || Auth::user()->can('ver_requerimientos_extensiones_ubs'))
                                                        <li class="nav-item">
                                                            <a href="#sidebarExtensionesUniversitariasUbs" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarExtensionesUniversitariasUbs" data-key="t-extensiones-universitarias-ubs">Extensiones Univ.</a>
                                                            <div class="collapse menu-dropdown {{ request()->routeIs('extensiones_universitarias_ubs.*', 'tipos_extensiones_universitarias_ubs.*', 'requerimientos_extensiones_universitarias_ubs.*') ? 'show' : '' }}" id="sidebarExtensionesUniversitariasUbs">
                                                                <ul class="nav nav-sm flex-column">
                                                                    @can('ver_extensiones_ubs')
                                                                        <li class="nav-item">
                                                                            <a href="{{route('extensiones_universitarias_ubs.index')}}" class="nav-link {{ request()->routeIs('extensiones_universitarias_ubs.*') ? 'active' : '' }}" data-key="t-extensiones-universitarias-ubs">Lista de Ext. Univ.</a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('ver_tipos_extensiones_ubs')
                                                                        <li class="nav-item">
                                                                            <a href="{{route('tipos_extensiones_universitarias_ubs.index')}}" class="nav-link {{ request()->routeIs('tipos_extensiones_universitarias_ubs.*') ? 'active' : '' }}" data-key="t-tipos-extensiones-universitarias-ubs">Tipos de Activ.</a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('ver_requerimientos_extensiones_ubs')
                                                                        <li class="nav-item">
                                                                            <a href="{{route('requerimientos_extensiones_universitarias_ubs.show')}}" class="nav-link {{ request()->routeIs('requerimientos_extensiones_universitarias_ubs.*') ? 'active' : '' }}" data-key="t-requerimientos-extensiones-universitarias-ubs">Requerimientos</a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endif
                                                    @if (Auth::user()->can('ver_tesis_ubs') || Auth::user()->can('ver_parametros_tesis_ubs'))
                                                        <li class="nav-item">
                                                            <a href="#sidebarTesisUbs" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTesisUbs" data-key="t-tesis-ubs">Tesis</a>
                                                            <div class="collapse menu-dropdown {{ request()->routeIs('tesis_ubs.*', 'tesis_parametros_ubs.*') ? 'show' : '' }}" id="sidebarTesisUbs">
                                                                <ul class="nav nav-sm flex-column">
                                                                    @can('ver_tesis_ubs')
                                                                        <li class="nav-item">
                                                                            <a href="{{route('tesis_ubs.index')}}" class="nav-link {{ request()->routeIs('tesis_ubs.*') ? 'active' : '' }}" data-key="t-lista-tesis-ubs">Lista de Tesis</a>                                                                </li>
                                                                    @endcan
                                                                    @can('ver_parametros_tesis_ubs')
                                                                        <li class="nav-item">
                                                                            <a href="{{route('tesis_parametros_ubs.index')}}" class="nav-link {{ request()->routeIs('tesis_parametros_ubs.*') ? 'active' : '' }}" data-key="t-tesis-parametros-ubs">Parámetros</a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_docentes_ubs')
                                        <li class="nav-item">
                                            <a href="{{route('docentes_ubs.index')}}" class="nav-link {{ request()->routeIs('docentes_ubs.*') ? 'active' : '' }}" data-key="t-docentes-ubs">Docentes</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_ventas') || Auth::user()->can('ver_recibos') || Auth::user()->can('ver_cobros') || Auth::user()->can('ver_notas_creditos') || Auth::user()->can('ver_cajas_arqueos'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarVentas" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarVentas">
                                <i class="ri-hand-coin-line"></i> <span data-key="t-ventas">Ventas</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('ventas.*', 'recibos.*', 'cobros.*', 'notas_creditos.*', 'arqueos_cajas.*') ? 'show' : '' }}" id="sidebarVentas">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_ventas')
                                        <li class="nav-item">
                                            <a href="{{route('ventas.index')}}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" data-key="t-ventas">Lista de Ventas</a>
                                        </li>
                                    @endcan
                                    @can('ver_recibos')
                                        <li class="nav-item">
                                            <a href="{{route('recibos.index')}}" class="nav-link {{ request()->routeIs('recibos.*') ? 'active' : '' }}" data-key="t-recibos">Recibos</a>
                                        </li>
                                    @endcan
                                    @can('ver_cobros')
                                        <li class="nav-item">
                                            <a href="{{route('cobros.index')}}" class="nav-link {{ request()->routeIs('cobros.*') ? 'active' : '' }}" data-key="t-cobros">Cobros</a>
                                        </li>
                                    @endcan
                                    @can('ver_notas_creditos')
                                        <li class="nav-item">
                                            <a href="{{route('notas_creditos.index')}}" class="nav-link {{ request()->routeIs('notas_creditos.*') ? 'active' : '' }}" data-key="t-notas_creditos">Notas de Crédito</a>
                                        </li>
                                    @endcan
                                    @can('ver_cajas_arqueos')
                                        <li class="nav-item">
                                            <a href="{{route('arqueos_cajas.index')}}" class="nav-link {{ request()->routeIs('arqueos_cajas.*') ? 'active' : '' }}" data-key="t-arqueos-cajas">Arqueos Generados</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_compras_ordenes') || Auth::user()->can('ver_compras') || Auth::user()->can('ver_pagos_ordenes') || Auth::user()->can('ver_pagos') || Auth::user()->can('ver_proveedores') || Auth::user()->can('ver_timbrados_proveedores') || Auth::user()->can('ver_categorias_proveedores'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarCompras" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCompras">
                                <i class="ri-shopping-cart-line"></i> <span data-key="t-compras">Compras</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('ordenes_compras.*', 'compras.*', 'ordenes_pagos.*', 'pagos.*', 'proveedores.*', 'timbrados_proveedores.*', 'categorias_proveedores.*') ? 'show' : '' }}" id="sidebarCompras">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_compras_ordenes')
                                        <li class="nav-item">
                                            <a href="{{ route('ordenes_compras.index') }}" class="nav-link {{ request()->routeIs('ordenes_compras.*') ? 'active' : '' }}" data-key="t-ordenes_compras">Ordenes de Compras</a>
                                        </li>
                                    @endcan
                                    @can('ver_compras')
                                        <li class="nav-item">
                                            <a href="{{ route('compras.index') }}" class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}" data-key="t-compras">Lista de Compras</a>
                                        </li>
                                    @endcan
                                    @can('ver_pagos_ordenes')
                                        <li class="nav-item">
                                            <a href="{{ route('ordenes_pagos.index') }}" class="nav-link {{ request()->routeIs('ordenes_pagos.*') ? 'active' : '' }}" data-key="t-ordenes-pagos">Ordenes de Pagos</a>
                                        </li>
                                    @endcan
                                    @can('ver_pagos')
                                        <li class="nav-item">
                                            <a href="{{ route('pagos.index') }}" class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}" data-key="t-pagos">Pagos</a>
                                        </li>
                                    @endcan
                                    @can('ver_proveedores')
                                        <li class="nav-item">
                                            <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" data-key="t-proveedores">Proveedores</a>
                                        </li>
                                    @endcan
                                    @can('ver_timbrados_proveedores')
                                        <li class="nav-item">
                                            <a href="{{ route('timbrados_proveedores.index') }}" class="nav-link {{ request()->routeIs('timbrados_proveedores.*') ? 'active' : '' }}" data-key="t-timbrados-proveedores">Timbrados de Prov.</a>
                                        </li>
                                    @endcan
                                    @can('ver_categorias_proveedores')
                                        <li class="nav-item">
                                            <a href="{{ route('categorias_proveedores.index') }}" class="nav-link {{ request()->routeIs('categorias_proveedores.*') ? 'active' : '' }}" data-key="t-categorias-proveedores">Categorías de Prov.</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_asientos_contables') || Auth::user()->can('ver_articulos') || Auth::user()->can('ver_cuentas_contables') || Auth::user()->can('ver_centros_costos_contables') || Auth::user()->can('ver_unidades_negocios_contables') || Auth::user()->can('ver_tipos_documentos_contables'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarContabilidad" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarContabilidad">
                                <i class="ri-coins-line"></i> <span data-key="t-contabilidad">Contabilidad</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('asientos_contables.*', 'articulos.*', 'cuentas_contables_saldos.*', 'cuentas_contables.*', 'centros_costos_contables.*', 'unidades_negocios_contables.*', 'tipos_documentos_contables.*') ? 'show' : '' }}" id="sidebarContabilidad">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_asientos_contables')
                                        <li class="nav-item">
                                            <a href="{{route('asientos_contables.index')}}" class="nav-link {{ request()->routeIs('asientos_contables.*') ? 'active' : '' }}" data-key="t-asientos-contables">Asientos</a>
                                        </li>
                                    @endcan
                                    @can('ver_articulos')
                                        <li class="nav-item">
                                            <a href="{{route('articulos.index')}}" class="nav-link {{ request()->routeIs('articulos.*') ? 'active' : '' }}" data-key="t-articulos">Artículos</a>
                                        </li>
                                    @endcan
                                    @can('ver_cuentas_contables_saldos')
                                        <li class="nav-item">
                                            <a href="{{route('cuentas_contables_saldos.index')}}" class="nav-link {{ request()->routeIs('cuentas_contables_saldos.*') ? 'active' : '' }}" data-key="t-cuentas-contables-saldos">Maestro de Saldos</a>
                                        </li>
                                    @endcan
                                    @can('ver_cuentas_contables')
                                        <li class="nav-item">
                                            <a href="{{route('cuentas_contables.index')}}" class="nav-link {{ request()->routeIs('cuentas_contables.*') ? 'active' : '' }}" data-key="t-cuentas-contables">Cuentas Contables</a>
                                        </li>
                                    @endcan
                                    @can('ver_centros_costos_contables')
                                        <li class="nav-item">
                                            <a href="{{route('centros_costos_contables.index')}}" class="nav-link {{ request()->routeIs('centros_costos_contables.*') ? 'active' : '' }}" data-key="t-centros_costos-contables">Centros de Costos</a>
                                        </li>
                                    @endcan
                                    @can('ver_unidades_negocios_contables')
                                        <li class="nav-item">
                                            <a href="{{route('unidades_negocios_contables.index')}}" class="nav-link {{ request()->routeIs('unidades_negocios_contables.*') ? 'active' : '' }}" data-key="t-unidades_negocios-contables">Unidades de Negocio</a>
                                        </li>
                                    @endcan
                                    @can('ver_tipos_documentos_contables')
                                        <li class="nav-item">
                                            <a href="{{route('tipos_documentos_contables.index')}}" class="nav-link {{ request()->routeIs('tipos_documentos_contables.*') ? 'active' : '' }}" data-key="t-tipos-movimientos">Tipos de Documentos Contables</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_cajas') || Auth::user()->can('ver_cuentas_bancarias') || Auth::user()->can('ver_cajas_movimientos') || Auth::user()->can('ver_movimientos_cuentas') || Auth::user()->can('ver_cajas_cuentas_movimientos') || Auth::user()->can('ver_bancos') || Auth::user()->can('ver_cotizaciones') || Auth::user()->can('ver_convenios') || Auth::user()->can('ver_pagos_formas') || Auth::user()->can('ver_monedas'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarTesoreria" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTesoreria">
                                <i class="ri-bank-line"></i> <span data-key="t-tesoreria">Tesorería</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('cajas.*', 'cuentas_bancarias.*', 'movimientos_cajas.*', 'movimientos_bancos.*', 'movimientos_cajas_bancos.*', 'bancos.*', 'cotizaciones.*', 'convenios.*', 'formas_pagos.*', 'monedas.*') ? 'show' : '' }}" id="sidebarTesoreria">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_cajas')
                                        <li class="nav-item">
                                            <a href="{{route('cajas.index')}}" class="nav-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}" data-key="t-cajas">Cajas</a>
                                        </li>
                                    @endcan
                                    @can('ver_cuentas_bancarias')
                                        <li class="nav-item">
                                            <a href="{{route('cuentas_bancarias.index')}}" class="nav-link {{ request()->routeIs('cuentas_bancarias.*') ? 'active' : '' }}" data-key="t-cuentas-bancarias">Cuentas Bancarias</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_cajas_movimientos') || Auth::user()->can('ver_movimientos_cuentas') || Auth::user()->can('ver_cajas_cuentas_movimientos'))
                                        <li class="nav-item">
                                            <a href="#sidebarMovimientosTesoreria" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarMovimientosTesoreria" data-key="t-movimientos-tesoreria">Movimientos</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('movimientos_cajas.*', 'movimientos_bancos.*', 'movimientos_cajas_bancos.*') ? 'show' : '' }}" id="sidebarMovimientosTesoreria">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_cajas_movimientos')
                                                        <li class="nav-item">
                                                            <a href="{{route('movimientos_cajas.index')}}" class="nav-link {{ request()->routeIs('movimientos_cajas.*') ? 'active' : '' }}" data-key="t-movimientos-cajas">Mov. de Cajas</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_movimientos_cuentas')
                                                        <li class="nav-item">
                                                            <a href="{{route('movimientos_bancos.index')}}" class="nav-link {{ request()->routeIs('movimientos_bancos.*') ? 'active' : '' }}" data-key="t-movimientos-bancos">Mov. de Bancos</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_cajas_cuentas_movimientos')
                                                        <li class="nav-item">
                                                            <a href="{{route('movimientos_cajas_bancos.index')}}" class="nav-link {{ request()->routeIs('movimientos_cajas_bancos.*') ? 'active' : '' }}" data-key="t-movimientos-cajas-bancos">Mov. entre Cajas y Bancos</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_bancos')
                                        <li class="nav-item">
                                            <a href="{{route('bancos.index')}}" class="nav-link {{ request()->routeIs('bancos.*') ? 'active' : '' }}" data-key="t-bancos">Bancos</a>
                                        </li>
                                    @endcan
                                    @can('ver_cotizaciones')
                                        <li class="nav-item">
                                            <a href="{{route('cotizaciones.index')}}" class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}" data-key="t-cotizaciones">Cotizaciones</a>
                                        </li>
                                    @endcan
                                    @can('ver_convenios')
                                        <li class="nav-item">
                                            <a href="{{route('convenios.index')}}" class="nav-link {{ request()->routeIs('convenios.*') ? 'active' : '' }}" data-key="t-convenios">Convenios</a>
                                        </li>
                                    @endcan
                                    @can('ver_pagos_formas')
                                        <li class="nav-item">
                                            <a href="{{route('formas_pagos.index')}}" class="nav-link {{ request()->routeIs('formas_pagos.*') ? 'active' : '' }}" data-key="t-formas-pagos">Formas de Pagos</a>
                                        </li>
                                    @endcan
                                    @can('ver_monedas')
                                        <li class="nav-item">
                                            <a href="{{route('monedas.index')}}" class="nav-link {{ request()->routeIs('monedas.*') ? 'active' : '' }}" data-key="t-monedas">Monedas</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_empleados'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarRRHH" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarRRHH">
                                <i class="ri-team-line"></i> <span data-key="t-rrhh">Recursos Humanos</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('empleados.*') ? 'show' : '' }}" id="sidebarRRHH">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_empleados')
                                        <li class="nav-item">
                                            <a href="#" class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}" data-key="t-empleados">Empleados</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_usuarios') || Auth::user()->can('ver_roles') || Auth::user()->can('ver_permisos'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarUsuarios" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsuarios">
                                <i class="ri-group-line"></i> <span data-key="t-usuarios">Usuarios</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('usuarios.*', 'roles.*', 'permisos.*') ? 'show' : '' }}" id="sidebarUsuarios">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_usuarios')
                                        <li class="nav-item">
                                            <a href="{{route('usuarios.index')}}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" data-key="t-usuarios">Lista de Usuarios</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->can('ver_roles') || Auth::user()->can('ver_permisos'))
                                        <li class="nav-item">
                                            <a href="#sidebarRolesPermisos" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarRolesPermisos" data-key="t-roles-permisos">Roles y Permisos</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('roles.*', 'permisos.*') ? 'show' : '' }}" id="sidebarRolesPermisos">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('ver_roles')
                                                        <li class="nav-item">
                                                            <a href="{{route('roles.index')}}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" data-key="t-roles">Roles</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_permisos')
                                                        <li class="nav-item">
                                                            <a href="{{route('permisos.index')}}" class="nav-link {{ request()->routeIs('permisos.*') ? 'active' : '' }}" data-key="t-permisos">Permisos</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif
                    @can('ver_noticias_avisos')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('noticias_avisos.*') ? 'active' : '' }}" href="{{route('noticias_avisos.index')}}">
                                <i class="ri-newspaper-line"></i> <span data-key="t-noticias-avisos">Noticias y Avisos</span>
                            </a>
                        </li>
                    @endcan
                    @if (Auth::user()->can('editar_empresa') || Auth::user()->can('ver_puntos_impresiones') || Auth::user()->can('ver_timbrados') || Auth::user()->can('ver_nacionalidades') || Auth::user()->can('ver_paises') || Auth::user()->can('ver_departamentos_paraguay') || Auth::user()->can('ver_ciudades') || Auth::user()->can('ver_barrios') || Auth::user()->can('ver_tipos_movimientos') || Auth::user()->can('ver_tipos_documentos_contables') || Auth::user()->can('ver_formas_conocimientos'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarParametrosGenerales" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarParametrosGenerales">
                                <i class="ri-settings-3-line"></i> <span data-key="t-parametros-generales">Parámetros</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('empresas.*', 'puntos_impresiones.*', 'timbrados.*', 'nacionalidades.*', 'paises.*', 'departamentos_paraguay.*', 'ciudades.*', 'barrios.*', 'tipos_movimentos.*', 'formas_conocimientos.*') ? 'show' : '' }}" id="sidebarParametrosGenerales">
                                <ul class="nav nav-sm flex-column">
                                    @if (Auth::user()->can('editar_empresa') || Auth::user()->can('ver_puntos_impresiones') || Auth::user()->can('ver_timbrados'))
                                        <li class="nav-item">
                                            <a href="#sidebarEmpresa" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmpresa" data-key="t-empresa">La Empresa</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('empresas.*', 'puntos_impresiones.*', 'timbrados.*') ? 'show' : '' }}" id="sidebarEmpresa">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('editar_empresa')
                                                        <li class="nav-item">
                                                            <a href="{{route('empresas.index')}}" class="nav-link {{ request()->routeIs('empresas.*') ? 'active' : '' }}" data-key="t-empresas">Datos de la Empresa</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_puntos_impresiones')
                                                        <li class="nav-item">
                                                            <a href="{{route('puntos_impresiones.index')}}" class="nav-link {{ request()->routeIs('puntos_impresiones.*') ? 'active' : '' }}" data-key="t-puntos-impresiones">Puntos de Impresión</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_timbrados')
                                                        <li class="nav-item">
                                                            <a href="{{route('timbrados.index')}}" class="nav-link {{ request()->routeIs('timbrados.*') ? 'active' : '' }}" data-key="t-timbrados">Timbrados</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    @can('ver_nacionalidades')
                                        <li class="nav-item">
                                            <a href="{{route('nacionalidades.index')}}" class="nav-link {{ request()->routeIs('nacionalidades.*') ? 'active' : '' }}" data-key="t-nacionalidades">Nacionalidades</a>
                                        </li>
                                    @endcan
                                    @can('ver_paises')
                                        <li class="nav-item">
                                            <a href="{{route('paises.index')}}" class="nav-link {{ request()->routeIs('paises.*') ? 'active' : '' }}" data-key="t-paises">Países</a>
                                        </li>
                                    @endcan
                                    @can('ver_departamentos_paraguay')
                                        <li class="nav-item">
                                            <a href="{{route('departamentos_paraguay.index')}}" class="nav-link {{ request()->routeIs('departamentos_paraguay.*') ? 'active' : '' }}" data-key="t-departamentos-paraguay">Dptos. del Paraguay</a>
                                        </li>
                                    @endcan
                                    @can('ver_ciudades')
                                        <li class="nav-item">
                                            <a href="{{route('ciudades.index')}}" class="nav-link {{ request()->routeIs('ciudades.*') ? 'active' : '' }}" data-key="t-ciudades">Ciudades</a>
                                        </li>
                                    @endcan
                                    @can('ver_barrios')
                                        <li class="nav-item">
                                            <a href="{{route('barrios.index')}}" class="nav-link {{ request()->routeIs('barrios.*') ? 'active' : '' }}" data-key="t-barrios">Barrios</a>
                                        </li>
                                    @endcan
                                    @can('ver_tipos_movimientos')
                                        <li class="nav-item">
                                            <a href="{{route('tipos_movimientos.index')}}" class="nav-link {{ request()->routeIs('tipos_movimientos.*') ? 'active' : '' }}" data-key="t-tipos-movimientos">Tipos de Movimientos</a>
                                        </li>
                                    @endcan
                                    @can('ver_formas_conocimientos')
                                        <li class="nav-item">
                                            <a href="{{route('formas_conocimientos.index')}}" class="nav-link {{ request()->routeIs('formas_conocimientos.*') ? 'active' : '' }}" data-key="t-formas-conocimientos">Formas de Conocer USIL</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                @endunless
                @hasanyrole(['ALUMNO', 'SUPERADMIN'])
                    <li class="menu-title"><span data-key="t-menu-alumnos">Menú Alumnos</span></li>
                    @can('ver_dashboard_alumnos_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_alumnos.index', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.index') ? 'active' : '' }}" data-key="t-dashboard-alumnos"><i class="ri-home-7-line"></i> Inicio</a>
                        </li>
                    @endcan
                    @can('ver_materias_alumnos_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_alumnos.materias', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.materias') ? 'active' : '' }}" data-key="t-materias-alumnos"><i class="ri-booklet-line"></i> Mis Materias</a>
                        </li>
                    @endcan
                    @can('ver_fechas_examenes_alumnos_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_alumnos.fechas_examenes', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.fechas_examenes') ? 'active' : '' }}" data-key="t-fechas-examenes-alumnos"><i class="ri-calendar-line"></i> Fechas de Exámenes</a>
                        </li>
                    @endcan
                    @if (Auth::user()->can('ver_puntajes_alumnos_pantalla') || Auth::user()->can('ver_calificaciones_alumnos_pantalla') || Auth::user()->can('ver_extensiones_alumnos_pantalla'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarCalificacionesAlumnos" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCalificacionesAlumnos">
                                <i class="ri-check-double-line"></i> <span data-key="t-calificaciones-alumnos">Calificaciones</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('pantallas_alumnos.puntajes', 'pantallas_alumnos.calificaciones', 'pantallas_alumnos.extensiones_universitarias') ? 'show' : '' }}" id="sidebarCalificacionesAlumnos">
                                <ul class="nav nav-sm flex-column">
                                    @can('ver_puntajes_alumnos_pantalla')
                                        <li class="nav-item">
                                            <a href="{{route('pantallas_alumnos.puntajes', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.puntajes') ? 'active' : '' }}" data-key="t-puntajes-alumnos">Evaluación Continua</a>
                                        </li>
                                    @endcan
                                    @can('ver_calificaciones_alumnos_pantalla')
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.calificaciones', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.calificaciones') ? 'active' : '' }}" data-key="t-calificaciones-alumnos">Finales</a>
                                    </li>
                                    @endcan
                                    @can('ver_extensiones_alumnos_pantalla')
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.extensiones_universitarias', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.extensiones_universitarias') ? 'active' : '' }}" data-key="t-extensiones-universitarias-alumnos">Extensión Universitaria</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if (Auth::user()->can('ver_inscripciones_tesis_alumnos_pantalla') || Auth::user()->can('ver_anteproyectos_tesis_alumnos_pantalla') || Auth::user()->can('ver_proyectos_tesis_alumnos_pantalla') || Auth::user()->can('ver_borradores_tesis_alumnos_pantalla'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarTesisAlumnos" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTesisAlumnos">
                                <i class="bi bi-mortarboard"></i> <span data-key="t-tesis-alumnos">Trabajo Final de Grado</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('pantallas_alumnos.inscripciones_tesis', 'pantallas_alumnos.create_inscripciones_tesis', 'pantallas_alumnos.anteproyectos_tesis', 'pantallas_alumnos.show_anteproyectos_tesis', 'pantallas_alumnos.show_entregas_anteproyectos_tesis', 'pantallas_alumnos.proyectos_tesis', 'pantallas_alumnos.show_proyectos_tesis', 'pantallas_alumnos.show_entregas_proyectos_tesis', 'pantallas_alumnos.borradores_tesis', 'pantallas_alumnos.show_borradores_tesis', 'pantallas_alumnos.show_entregas_borradores_tesis') ? 'show' : '' }}" id="sidebarTesisAlumnos">
                                <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="#sidebarInscripcionesTesisAlumnos" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInscripcionesTesisAlumnos" data-key="t-convalidaciones">Inscripciones de Temas</a>
                                            <div class="collapse menu-dropdown {{ request()->routeIs('pantallas_alumnos.create_inscripciones_tesis', 'pantallas_alumnos.inscripciones_tesis') ? 'show' : '' }}" id="sidebarInscripcionesTesisAlumnos">
                                                <ul class="nav nav-sm flex-column">
                                                    @can('crear_inscripciones_tesis_alumnos_pantalla')
                                                        <li class="nav-item">
                                                            <a href="{{route('pantallas_alumnos.create_inscripciones_tesis', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.create_inscripciones_tesis') ? 'active' : '' }}" data-key="t-inscripciones-tesis-alumnos">Nueva Inscripción</a>
                                                        </li>
                                                    @endcan
                                                    @can('ver_inscripciones_tesis_alumnos_pantalla')
                                                        <li class="nav-item">
                                                            <a href="{{route('pantallas_alumnos.inscripciones_tesis', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.inscripciones_tesis') ? 'active' : '' }}" data-key="t-puntajes-alumnos">Inscripciones Realizadas</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </li>
                                    @can('ver_anteproyectos_tesis_alumnos_pantalla')
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.anteproyectos_tesis', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.anteproyectos_tesis', 'pantallas_alumnos.show_anteproyectos_tesis', 'pantallas_alumnos.show_entregas_anteproyectos_tesis') ? 'active' : '' }}" data-key="t-anteproyectos-tesis-alumnos">Anteproyectos</a>
                                    </li>
                                    @endcan
                                    @can('ver_proyectos_tesis_alumnos_pantalla')
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.proyectos_tesis', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.proyectos_tesis', 'pantallas_alumnos.show_proyectos_tesis', 'pantallas_alumnos.show_entregas_proyectos_tesis') ? 'active' : '' }}" data-key="t-proyectos-tesis-alumnos">Proyectos</a>
                                    </li>
                                    @endcan
                                    @can('ver_borradores_tesis_alumnos_pantalla')
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.borradores_tesis', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.borradores_tesis', 'pantallas_alumnos.show_borradores_tesis', 'pantallas_alumnos.show_entregas_borradores_tesis') ? 'active' : '' }}" data-key="t-borradores-tesis-alumnos">Borradores</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @can('ver_noticias_avisos_alumnos_pantalla')
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarNoticiaAvisosAlumnos" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarNoticiaAvisosAlumnos">
                                <i class="ri-newspaper-line"></i> <span data-key="t-noticias-avisos-alumnos">Noticias y Avisos</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('pantallas_alumnos.noticias', 'pantallas_alumnos.avisos', 'pantallas_alumnos.show_noticia', 'pantallas_alumnos.show_aviso') ? 'show' : '' }}" id="sidebarNoticiaAvisosAlumnos">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.noticias', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.noticias', 'pantallas_alumnos.show_noticia') ? 'active' : '' }}" data-key="t-noticias-alumnos">Noticias</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('pantallas_alumnos.avisos', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.avisos', 'pantallas_alumnos.show_aviso') ? 'active' : '' }}" data-key="t-avisos-alumnos">Avisos</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                    @if (Auth::user()->can('ver_solicitudes_alumnos_pantalla') || Auth::user()->can('crear_solicitudes_alumnos_pantalla'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarSolicitudesAlumnos" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSolicitudesAlumnos">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-noticias-avisos-alumnos">Solicitudes</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('pantallas_alumnos.solicitudes', 'pantallas_alumnos.create_solicitudes') ? 'show' : '' }}" id="sidebarSolicitudesAlumnos">
                                <ul class="nav nav-sm flex-column">
                                    @can('crear_solicitudes_alumnos_pantalla')
                                        <li class="nav-item">
                                            <a href="{{route('pantallas_alumnos.create_solicitudes', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.create_solicitudes') ? 'active' : '' }}" data-key="t-create-solicitud-alumnos">Nueva Solicitud</a>
                                        </li>
                                    @endcan
                                    @can('ver_solicitudes_alumnos_pantalla')
                                        <li class="nav-item">
                                            <a href="{{route('pantallas_alumnos.solicitudes', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.solicitudes') ? 'active' : '' }}" data-key="t-solicitudes-alumnos">Mis Solicitudes</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @can('ver_encuestas_alumnos_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_alumnos.encuestas', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.encuestas') ? 'active' : '' }}" data-key="t-encuestas-docentes"><i class="ri-question-answer-line"></i> Encuestas</a>
                        </li>
                    @endcan
                    @can('ver_estado_cuenta_alumnos_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_alumnos.estado_cuenta', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_alumnos.estado_cuenta') ? 'active' : '' }}" data-key="t-estado-cuenta-alumnos"><i class="ri-money-dollar-circle-line"></i> Estado de Cuenta</a>
                        </li>
                    @endcan
                @endhasanyrole
                @hasanyrole(['DOCENTE', 'ENCARGADO_DOCENTE', 'SUPERADMIN'])
                    <li class="menu-title"><span data-key="t-menu-alumnos">Menú Docentes</span></li>
                    @can('ver_dashboard_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_docentes.index', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.index') ? 'active' : '' }}" data-key="t-dashboard-docentes"><i class="ri-home-7-line"></i> Inicio</a>
                        </li>
                    @endcan
                    @can('ver_clases_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_docentes.clases', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.clases', 'pantallas_docentes.show_clases') ? 'active' : '' }}" data-key="t-clases-docentes"><i class="ri-book-open-line"></i> Clases Generadas</a>
                        </li>
                    @endcan
                    @can('ver_fechas_examenes_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_docentes.fechas_examenes', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.fechas_examenes') ? 'active' : '' }}" data-key="t-fechas-examenes-docentes"><i class="ri-calendar-line"></i> Fechas de Exámenes</a>
                        </li>
                    @endcan
                    @if (Auth::user()->can('ver_planes_clases_docentes_pantalla') || Auth::user()->can('ver_programas_clases_docentes_pantalla'))
                        <li class="nav-item">
                            <a href="{{route('planes_programas_clases_docentes.index', Auth::id())}}" class="nav-link {{ request()->routeIs('planes_programas_clases_docentes.*') ? 'active' : '' }}" data-key="t-planes-programas-clases-docentes"><i class="ri-list-ordered"></i> Planes de Clases y Programas de Estudio</a>
                        </li>
                    @endif
                    @can('ver_tutorias_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{route('tutorias_docentes.index', Auth::id())}}" class="nav-link {{ request()->routeIs('tutorias_docentes.*') ? 'active' : '' }}" data-key="t-tutorias-docentes"><i class="ri-contacts-book-line"></i> Tutorías</a>
                        </li>
                    @endcan
                    @if (Auth::user()->can('ver_extensiones_docentes_pantalla') || Auth::user()->can('crear_extensiones_docentes_pantalla'))
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarExtensionesUniversitariasDocentes" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarExtensionesUniversitariasDocentes">
                                <i class="ri-check-double-line"></i> <span data-key="t-extensiones-universitarias-docentes">Extensiones Univ.</span>
                            </a>
                            <div class="menu-dropdown collapse {{ request()->routeIs('pantallas_docentes.extensiones_universitarias', 'pantallas_docentes.show_extensiones_universitarias', 'pantallas_docentes.create_extensiones_universitarias') ? 'show' : '' }}" id="sidebarExtensionesUniversitariasDocentes">
                                <ul class="nav nav-sm flex-column">
                                    @can('crear_extensiones_docentes_pantalla')
                                        <li class="nav-item">
                                            <a href="{{route('pantallas_docentes.create_extensiones_universitarias', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.create_extensiones_universitarias') ? 'active' : '' }}" data-key="t-create-extensiones-universitarias-docentes">Nueva Extensión</a>
                                        </li>
                                    @endcan
                                    @can('ver_extensiones_docentes_pantalla')
                                        <li class="nav-item">
                                            <a href="{{route('pantallas_docentes.extensiones_universitarias', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.extensiones_universitarias', 'pantallas_docentes.show_extensiones_universitarias') ? 'active' : '' }}" data-key="t-extensiones-universitarias-docentes">Mis Extensiones</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                    @can('ver_encuestas_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{route('pantallas_docentes.encuestas', Auth::id())}}" class="nav-link {{ request()->routeIs('pantallas_docentes.encuestas') ? 'active' : '' }}" data-key="t-encuestas-docentes"><i class="ri-question-answer-line"></i> Encuestas</a>
                        </li>
                    @endcan
                    @can('ver_cobros_docentes_pantalla')
                        <li class="nav-item">
                            <a href="{{ route('pantallas_docentes.cobros', Auth::id()) }}" class="nav-link {{ request()->routeIs('pantallas_docentes.cobros') ? 'active' : '' }}" data-key="t-cobros-docentes"><i class="ri-money-dollar-circle-line"></i> Cobros</a>
                        </li>
                    @endcan
                @endhasanyrole
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
