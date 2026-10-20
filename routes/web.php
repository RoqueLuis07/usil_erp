<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Sin registro público: las cuentas las crea Extensión (usuarios, alumnos y docentes); sin verificación por correo.
Auth::routes(['register' => false, 'verify' => false]);
//Dashboard
    Route::get('/', 'HomeController@root')->name('root');

//Academico
    //Alumnos
        Route::get('alumnos', 'AlumnoController@index')->name('alumnos.index');
        Route::post('alumnos', 'AlumnoController@index');
        Route::get('alumnos/ver/{id}', 'AlumnoController@show')->name('alumnos.show');
        Route::get('alumnos/extensiones/{id}', 'AlumnoController@show_extensiones')->name('alumnos.show_extensiones');
        Route::get('alumnos/reporte_extensiones/{id}', 'AlumnoController@reporte_extensiones')->name('alumnos.reporte_extensiones');
        Route::get('alumnos/agregar', 'AlumnoController@create')->name('alumnos.create');
        Route::post('alumnos/agregar', 'AlumnoController@store')->name('alumnos.store');
        Route::get('alumnos/editar/{id}', 'AlumnoController@edit')->name('alumnos.edit');
        Route::get('alumnos/actualizar/{id}', 'AlumnoController@update')->name('alumnos.update');
        Route::post('alumnos/actualizar/{id}', 'AlumnoController@update');
        Route::post('alumnos/inactivar/{id}', 'AlumnoController@unactivate')->name('alumnos.unactivate');
        Route::post('alumnos/activar/{id}', 'AlumnoController@activate')->name('alumnos.activate');
        Route::get('alumnos/eliminar/{id}', 'AlumnoController@destroy')->name('alumnos.get_destroy');
        Route::delete('alumnos/eliminar/{id}', 'AlumnoController@destroy')->name('alumnos.destroy');
        Route::get('alumnos/ver_legajo/{id}', 'AlumnoController@ver_legajo')->name('alumnos.ver_legajo');
        Route::post('alumnos/subir_legajo/{id}', 'AlumnoController@subir_legajo')->name('alumnos.subir_legajo');
        Route::get('alumnos/eliminar_legajo/{id}', 'AlumnoController@eliminar_legajo')->name('alumnos.get_eliminar_legajo');
        Route::delete('alumnos/eliminar_legajo/{id}', 'AlumnoController@eliminar_legajo')->name('alumnos.eliminar_legajo');
    //Docentes
        Route::get('docentes', 'DocenteController@index')->name('docentes.index');
        Route::post('docentes', 'DocenteController@index');
        Route::get('docentes/ver/{id}', 'DocenteController@show')->name('docentes.show');
        Route::get('docentes/agregar', 'DocenteController@create')->name('docentes.create');
        Route::post('docentes/agregar', 'DocenteController@store')->name('docentes.store');
        Route::get('docentes/editar/{id}', 'DocenteController@edit')->name('docentes.edit');
        Route::post('docentes/actualizar/{id}', 'DocenteController@update')->name('docentes.update');
        Route::post('docentes/inactivar/{id}', 'DocenteController@unactivate')->name('docentes.unactivate');
        Route::post('docentes/activar/{id}', 'DocenteController@activate')->name('docentes.activate');
        Route::get('docentes/eliminar/{id}', 'DocenteController@destroy')->name('docentes.get_destroy');
        Route::delete('docentes/eliminar/{id}', 'DocenteController@destroy')->name('docentes.destroy');
        Route::get('docentes/ver_legajo/{id}', 'DocenteController@ver_legajo')->name('docentes.ver_legajo');
        Route::post('docentes/subir_legajo/{id}', 'DocenteController@subir_legajo')->name('docentes.subir_legajo');
        Route::get('docentes/eliminar_legajo/{id}', 'DocenteController@eliminar_legajo')->name('docentes.get_eliminar_legajo');
        Route::delete('docentes/eliminar_legajo/{id}', 'DocenteController@eliminar_legajo')->name('docentes.eliminar_legajo');
    //Extensiones Universitarias
        //Extensiones Universitarias
            Route::get('extensiones_universitarias', 'ExtensionUniversitariaController@index')->name('extensiones_universitarias.index');
            Route::get('extensiones_universitarias/ver/{id}', 'ExtensionUniversitariaController@show')->name('extensiones_universitarias.show');
            Route::get('extensiones_universitarias/agregar', 'ExtensionUniversitariaController@create')->name('extensiones_universitarias.create');
            Route::post('extensiones_universitarias/agregar', 'ExtensionUniversitariaController@store')->name('extensiones_universitarias.store');
            Route::get('extensiones_universitarias/editar/{id}', 'ExtensionUniversitariaController@edit')->name('extensiones_universitarias.edit');
            Route::post('extensiones_universitarias/actualizar/{id}', 'ExtensionUniversitariaController@update')->name('extensiones_universitarias.update');
            Route::get('extensiones_universitarias/agregar_horas/{id}', 'ExtensionUniversitariaController@edit_hours')->name('extensiones_universitarias.edit_hours');
            Route::post('extensiones_universitarias/guardar_horas/{id}', 'ExtensionUniversitariaController@update_hours')->name('extensiones_universitarias.update_hours');
            Route::post('extensiones_universitarias/aprobar/{id}', 'ExtensionUniversitariaController@approve')->name('extensiones_universitarias.approve');
            Route::post('extensiones_universitarias/rechazar/{id}', 'ExtensionUniversitariaController@reject')->name('extensiones_universitarias.reject');
            Route::post('extensiones_universitarias/anular_rechazo/{id}', 'ExtensionUniversitariaController@unreject')->name('extensiones_universitarias.unreject');
            Route::post('extensiones_universitarias/desaprobar/{id}', 'ExtensionUniversitariaController@unapprove')->name('extensiones_universitarias.unapprove');
            Route::post('extensiones_universitarias/postulaciones/aprobar/{id}', 'ExtensionUniversitariaController@aprobar_postulacion')->name('extensiones_universitarias.aprobar_postulacion');
            Route::post('extensiones_universitarias/postulaciones/rechazar/{id}', 'ExtensionUniversitariaController@rechazar_postulacion')->name('extensiones_universitarias.rechazar_postulacion');
            Route::post('extensiones_universitarias/{id}/agregar_alumno', 'ExtensionUniversitariaController@agregar_alumno')->name('extensiones_universitarias.agregar_alumno');
            Route::delete('extensiones_universitarias/quitar_alumno/{id}', 'ExtensionUniversitariaController@quitar_alumno')->name('extensiones_universitarias.quitar_alumno');
            Route::post('extensiones_universitarias/finalizar/{id}', 'ExtensionUniversitariaController@finish')->name('extensiones_universitarias.finish');
            Route::get('extensiones_universitarias/cargar_informe/{id}', 'ExtensionUniversitariaController@cargar_informe')->name('extensiones_universitarias.cargar_informe');
            Route::post('extensiones_universitarias/cargar_informe/{id}', 'ExtensionUniversitariaController@cargar_informe');
            Route::get('extensiones_universitarias/cambiar_adjuntos/{id}', 'ExtensionUniversitariaController@change_adjuntos')->name('extensiones_universitarias.change_adjuntos');
            Route::post('extensiones_universitarias/cambiar_adjuntos/{id}', 'ExtensionUniversitariaController@change_adjuntos');
            Route::get('extensiones_universitarias/eliminar/{id}', 'ExtensionUniversitariaController@destroy')->name('extensiones_universitarias.get_destroy');
            Route::delete('extensiones_universitarias/eliminar/{id}', 'ExtensionUniversitariaController@destroy')->name('extensiones_universitarias.destroy');
            Route::get('extensiones_universitarias/ver_reporte', 'ExtensionUniversitariaController@show_reporte')->name('extensiones_universitarias.show_reporte');
            Route::get('extensiones_universitarias/generar_reporte', 'ExtensionUniversitariaController@generate_reporte')->name('extensiones_universitarias.generate_reporte');
            Route::get('extensiones_universitarias/ver_reporte_carrera_semestre', 'ExtensionUniversitariaController@show_reporte_carrera_semestre')->name('extensiones_universitarias.show_reporte_carrera_semestre');
            Route::get('extensiones_universitarias/generar_reporte_carrera_semestre', 'ExtensionUniversitariaController@generate_reporte_carrera_semestre')->name('extensiones_universitarias.generate_reporte_carrera_semestre');
    //Parametros
        Route::get('parametros_academicos', 'ParametroAcademicoController@index')->name('parametros_academicos.index');
        //Programas
            Route::get('programas', 'ProgramaController@index')->name('programas.index');
            Route::get('programas/all', 'ProgramaController@index_ajax')->name('programas.index_ajax');
            Route::get('programas/show/{id}', 'ProgramaController@show')->name('programas.show');
            Route::post('programas', 'ProgramaController@store')->name('programas.store');
            Route::get('programas/{id}', 'ProgramaController@edit')->name('programas.edit');
            Route::post('programas/{id}', 'ProgramaController@update')->name('programas.update');
            Route::get('programas/inactivar/{id}', 'ProgramaController@get_unactivate')->name('programas.get_unactivate');
            Route::post('programas/inactivar/{id}', 'ProgramaController@unactivate')->name('programas.unactivate');
            Route::get('programas/activar/{id}', 'ProgramaController@get_activate')->name('programas.get_activate');
            Route::post('programas/activar/{id}', 'ProgramaController@activate')->name('programas.activate');
            Route::get('programas/eliminar/{id}', 'ProgramaController@get_destroy')->name('programas.get_destroy');
            Route::delete('programas/eliminar/{id}', 'ProgramaController@destroy')->name('programas.destroy');
        //Facultades
            Route::get('facultades', 'FacultadController@index')->name('facultades.index');
            Route::get('facultades/all', 'FacultadController@index_ajax')->name('facultades.index_ajax');
            Route::get('facultades/show/{id}', 'FacultadController@show')->name('facultades.show');
            Route::post('facultades', 'FacultadController@store')->name('facultades.store');
            Route::get('facultades/{id}', 'FacultadController@edit')->name('facultades.edit');
            Route::post('facultades/{id}', 'FacultadController@update')->name('facultades.update');
            Route::get('facultades/eliminar/{id}', 'FacultadController@get_destroy')->name('facultades.get_destroy');
            Route::delete('facultades/eliminar/{id}', 'FacultadController@destroy')->name('facultades.destroy');
        //Carreras
            Route::get('carreras', 'CarreraController@index')->name('carreras.index');
            Route::get('carreras/agregar', 'CarreraController@create')->name('carreras.create');
            Route::post('carreras/agregar', 'CarreraController@store')->name('carreras.store');
            Route::get('carreras/editar/{id}', 'CarreraController@edit')->name('carreras.edit');
            Route::post('carreras/actualizar/{id}', 'CarreraController@update')->name('carreras.update');
            Route::post('carreras/inactivar/{id}', 'CarreraController@unactivate')->name('carreras.unactivate');
            Route::post('carreras/activar/{id}', 'CarreraController@activate')->name('carreras.activate');
            Route::get('carreras/eliminar/{id}', 'CarreraController@destroy')->name('carreras.get_destroy');
            Route::delete('carreras/eliminar/{id}', 'CarreraController@destroy')->name('carreras.destroy');
        //Tipos de Carreras
            Route::get('tipos_carreras', 'TipoCarreraController@index')->name('tipos_carreras.index');
            Route::get('tipos_carreras/all', 'TipoCarreraController@index_ajax')->name('tipos_carreras.index_ajax');
            Route::get('tipos_carreras/show/{id}', 'TipoCarreraController@show')->name('tipos_carreras.show');
            Route::post('tipos_carreras', 'TipoCarreraController@store')->name('tipos_carreras.store');
            Route::get('tipos_carreras/{id}', 'TipoCarreraController@edit')->name('tipos_carreras.edit');
            Route::post('tipos_carreras/{id}', 'TipoCarreraController@update')->name('tipos_carreras.update');
            Route::get('tipos_carreras/inactivar/{id}', 'TipoCarreraController@get_unactivate')->name('tipos_carreras.get_unactivate');
            Route::post('tipos_carreras/inactivar/{id}', 'TipoCarreraController@unactivate')->name('tipos_carreras.unactivate');
            Route::get('tipos_carreras/activar/{id}', 'TipoCarreraController@get_activate')->name('tipos_carreras.get_activate');
            Route::post('tipos_carreras/activar/{id}', 'TipoCarreraController@activate')->name('tipos_carreras.activate');
            Route::get('tipos_carreras/eliminar/{id}', 'TipoCarreraController@get_destroy')->name('tipos_carreras.get_destroy');
            Route::delete('tipos_carreras/eliminar/{id}', 'TipoCarreraController@destroy')->name('tipos_carreras.destroy');
        //Materias
            Route::get('materias', 'MateriaController@index')->name('materias.index');
            Route::get('materias/all', 'MateriaController@index_ajax')->name('materias.index_ajax');
            Route::get('materias/show/{id}', 'MateriaController@show')->name('materias.show');
            Route::post('materias', 'MateriaController@store')->name('materias.store');
            Route::get('materias/{id}', 'MateriaController@edit')->name('materias.edit');
            Route::post('materias/{id}', 'MateriaController@update')->name('materias.update');
            Route::get('materias/inactivar/{id}', 'MateriaController@get_unactivate')->name('materias.get_unactivate');
            Route::post('materias/inactivar/{id}', 'MateriaController@unactivate')->name('materias.unactivate');
            Route::get('materias/activar/{id}', 'MateriaController@get_activate')->name('materias.get_activate');
            Route::post('materias/activar/{id}', 'MateriaController@activate')->name('materias.activate');
            Route::get('materias/eliminar/{id}', 'MateriaController@get_destroy')->name('materias.get_destroy');
            Route::delete('materias/eliminar/{id}', 'MateriaController@destroy')->name('materias.destroy');
        //Semestres
            Route::get('semestres', 'SemestreController@index')->name('semestres.index');
            Route::get('semestres/agregar', 'SemestreController@create')->name('semestres.create');
            Route::post('semestres/agregar', 'SemestreController@store')->name('semestres.store');
            Route::post('semestres/inactivar/{id}', 'SemestreController@unactivate')->name('semestres.unactivate');
            Route::post('semestres/activar/{id}', 'SemestreController@activate')->name('semestres.activate');
            Route::get('semestres/eliminar/{id}', 'SemestreController@destroy')->name('semestres.get_destroy');
            Route::delete('semestres/eliminar/{id}', 'SemestreController@destroy')->name('semestres.destroy');
        //Modalidades
            Route::get('modalidades', 'ModalidadController@index')->name('modalidades.index');
            Route::get('modalidades/all', 'ModalidadController@index_ajax')->name('modalidades.index_ajax');
            Route::get('modalidades/show/{id}', 'ModalidadController@show')->name('modalidades.show');
            Route::post('modalidades', 'ModalidadController@store')->name('modalidades.store');
            Route::get('modalidades/{id}', 'ModalidadController@edit')->name('modalidades.edit');
            Route::post('modalidades/{id}', 'ModalidadController@update')->name('modalidades.update');
            Route::get('modalidades/inactivar/{id}', 'ModalidadController@get_unactivate')->name('modalidades.get_unactivate');
            Route::post('modalidades/inactivar/{id}', 'ModalidadController@unactivate')->name('modalidades.unactivate');
            Route::get('modalidades/activar/{id}', 'ModalidadController@get_activate')->name('modalidades.get_activate');
            Route::post('modalidades/activar/{id}', 'ModalidadController@activate')->name('modalidades.activate');
            Route::get('modalidades/eliminar/{id}', 'ModalidadController@get_destroy')->name('modalidades.get_destroy');
            Route::delete('modalidades/eliminar/{id}', 'ModalidadController@destroy')->name('modalidades.destroy');
        //Tipos de Actividades - Extension Universitaria
            Route::get('tipos_extensiones_universitarias', 'TipoExtensionUniversitariaController@index')->name('tipos_extensiones_universitarias.index');
            Route::get('tipos_extensiones_universitarias/agregar', 'TipoExtensionUniversitariaController@create')->name('tipos_extensiones_universitarias.create');
            Route::post('tipos_extensiones_universitarias/agregar', 'TipoExtensionUniversitariaController@store')->name('tipos_extensiones_universitarias.store');
            Route::get('tipos_extensiones_universitarias/editar/{id}', 'TipoExtensionUniversitariaController@edit')->name('tipos_extensiones_universitarias.edit');
            Route::post('tipos_extensiones_universitarias/actualizar/{id}', 'TipoExtensionUniversitariaController@update')->name('tipos_extensiones_universitarias.update');
            Route::post('tipos_extensiones_universitarias/inactivar/{id}', 'TipoExtensionUniversitariaController@unactivate')->name('tipos_extensiones_universitarias.unactivate');
            Route::post('tipos_extensiones_universitarias/activar/{id}', 'TipoExtensionUniversitariaController@activate')->name('tipos_extensiones_universitarias.activate');
            Route::get('tipos_extensiones_universitarias/eliminar/{id}', 'TipoExtensionUniversitariaController@destroy')->name('tipos_extensiones_universitarias.get_destroy');
            Route::delete('tipos_extensiones_universitarias/eliminar/{id}', 'TipoExtensionUniversitariaController@destroy')->name('tipos_extensiones_universitarias.destroy');
        //Requerimientos - Extension Universitaria
            Route::get('requerimientos_extensiones_universitarias', 'RequerimientoExtensionUniversitariaController@show')->name('requerimientos_extensiones_universitarias.show');
            Route::get('requerimientos_extensiones_universitarias/agregar', 'RequerimientoExtensionUniversitariaController@create')->name('requerimientos_extensiones_universitarias.create');
            Route::post('requerimientos_extensiones_universitarias/agregar', 'RequerimientoExtensionUniversitariaController@store')->name('requerimientos_extensiones_universitarias.store');
            Route::get('requerimientos_extensiones_universitarias/editar/{id}', 'RequerimientoExtensionUniversitariaController@edit')->name('requerimientos_extensiones_universitarias.edit');
            Route::post('requerimientos_extensiones_universitarias/actualizar/{id}', 'RequerimientoExtensionUniversitariaController@update')->name('requerimientos_extensiones_universitarias.update');
            Route::delete('requerimientos_extensiones_universitarias/eliminar/{id}', 'RequerimientoExtensionUniversitariaController@destroy')->name('requerimientos_extensiones_universitarias.destroy');

            //Parámetros de Extensión (facultades, carreras y materias simplificadas)
            Route::get('parametros_extension', 'ParametrosExtensionController@index')->name('parametros_extension.index');
            Route::post('parametros_extension/facultades', 'ParametrosExtensionController@storeFacultad')->name('parametros_extension.facultades.store');
            Route::post('parametros_extension/facultades/{id}', 'ParametrosExtensionController@updateFacultad')->name('parametros_extension.facultades.update');
            Route::post('parametros_extension/facultades/{id}/estado', 'ParametrosExtensionController@toggleFacultad')->name('parametros_extension.facultades.toggle');
            Route::post('parametros_extension/carreras', 'ParametrosExtensionController@storeCarrera')->name('parametros_extension.carreras.store');
            Route::post('parametros_extension/carreras/{id}', 'ParametrosExtensionController@updateCarrera')->name('parametros_extension.carreras.update');
            Route::post('parametros_extension/carreras/{id}/estado', 'ParametrosExtensionController@toggleCarrera')->name('parametros_extension.carreras.toggle');
            Route::delete('parametros_extension/facultades/{id}', 'ParametrosExtensionController@destroyFacultad')->name('parametros_extension.facultades.destroy');
            Route::delete('parametros_extension/carreras/{id}', 'ParametrosExtensionController@destroyCarrera')->name('parametros_extension.carreras.destroy');
            Route::post('parametros_extension/materias', 'ParametrosExtensionController@storeMateria')->name('parametros_extension.materias.store');
            Route::post('parametros_extension/materias/{id}', 'ParametrosExtensionController@updateMateria')->name('parametros_extension.materias.update');
            Route::post('parametros_extension/materias/{id}/estado', 'ParametrosExtensionController@toggleMateria')->name('parametros_extension.materias.toggle');
            Route::delete('parametros_extension/materias/{id}', 'ParametrosExtensionController@destroyMateria')->name('parametros_extension.materias.destroy');

            //Carga masiva de alumnos y docentes (CSV)
            Route::get('importaciones', 'ImportacionMasivaController@index')->name('importaciones.index');
            Route::get('importaciones/plantilla/{tipo}', 'ImportacionMasivaController@plantilla')->name('importaciones.plantilla');
            Route::post('importaciones/revertir', 'ImportacionMasivaController@revertir')->name('importaciones.revertir');
            Route::post('importaciones/{tipo}', 'ImportacionMasivaController@procesar')->name('importaciones.procesar');

            //Reportes de gestión (extensiones y docentes)
            Route::get('reportes_extension/extensiones', 'ReporteExtensionController@extensiones')->name('reportes_extension.extensiones');
            Route::get('reportes_extension/docentes', 'ReporteExtensionController@docentes')->name('reportes_extension.docentes');
        //Formacion Academicas - Alumnos Formaciones
            Route::get('formaciones_academicas', 'AlumnoFormacionController@index')->name('alumnos_formaciones.index');
            Route::get('formaciones_academicas/all', 'AlumnoFormacionController@index_ajax')->name('alumnos_formaciones.index_ajax');
            Route::get('formaciones_academicas/show/{id}', 'AlumnoFormacionController@show')->name('alumnos_formaciones.show');
            Route::post('formaciones_academicas', 'AlumnoFormacionController@store')->name('alumnos_formaciones.store');
            Route::get('formaciones_academicas/{id}', 'AlumnoFormacionController@edit')->name('alumnos_formaciones.edit');
            Route::post('formaciones_academicas/{id}', 'AlumnoFormacionController@update')->name('alumnos_formaciones.update');
            Route::get('formaciones_academicas/inactivar/{id}', 'AlumnoFormacionController@get_unactivate')->name('alumnos_formaciones.get_unactivate');
            Route::post('formaciones_academicas/inactivar/{id}', 'AlumnoFormacionController@unactivate')->name('alumnos_formaciones.unactivate');
            Route::get('formaciones_academicas/activar/{id}', 'AlumnoFormacionController@get_activate')->name('alumnos_formaciones.get_activate');
            Route::post('formaciones_academicas/activar/{id}', 'AlumnoFormacionController@activate')->name('alumnos_formaciones.activate');
            Route::get('formaciones_academicas/eliminar/{id}', 'AlumnoFormacionController@get_destroy')->name('alumnos_formaciones.get_destroy');
            Route::delete('formaciones_academicas/eliminar/{id}', 'AlumnoFormacionController@destroy')->name('alumnos_formaciones.destroy');
        //Instituciones Educativas
            Route::get('instituciones_educativas', 'InstitucionEducativaController@index')->name('instituciones_educativas.index');
            Route::get('instituciones_educativas/all', 'InstitucionEducativaController@index_ajax')->name('instituciones_educativas.index_ajax');
            Route::get('instituciones_educativas/show/{id}', 'InstitucionEducativaController@show')->name('instituciones_educativas.show');
            Route::post('instituciones_educativas', 'InstitucionEducativaController@store')->name('instituciones_educativas.store');
            Route::get('instituciones_educativas/{id}', 'InstitucionEducativaController@edit')->name('instituciones_educativas.edit');
            Route::post('instituciones_educativas/{id}', 'InstitucionEducativaController@update')->name('instituciones_educativas.update');
            Route::get('instituciones_educativas/inactivar/{id}', 'InstitucionEducativaController@get_unactivate')->name('instituciones_educativas.get_unactivate');
            Route::post('instituciones_educativas/inactivar/{id}', 'InstitucionEducativaController@unactivate')->name('instituciones_educativas.unactivate');
            Route::get('instituciones_educativas/activar/{id}', 'InstitucionEducativaController@get_activate')->name('instituciones_educativas.get_activate');
            Route::post('instituciones_educativas/activar/{id}', 'InstitucionEducativaController@activate')->name('instituciones_educativas.activate');
            Route::get('instituciones_educativas/eliminar/{id}', 'InstitucionEducativaController@get_destroy')->name('instituciones_educativas.get_destroy');
            Route::delete('instituciones_educativas/eliminar/{id}', 'InstitucionEducativaController@destroy')->name('instituciones_educativas.destroy');
        //Areas de Conocimiento
            Route::get('areas_conocimientos', 'AreaConocimientoController@index')->name('areas_conocimientos.index');
            Route::get('areas_conocimientos/all', 'AreaConocimientoController@index_ajax')->name('areas_conocimientos.index_ajax');
            Route::get('areas_conocimientos/show/{id}', 'AreaConocimientoController@show')->name('areas_conocimientos.show');
            Route::post('areas_conocimientos', 'AreaConocimientoController@store')->name('areas_conocimientos.store');
            Route::get('areas_conocimientos/{id}', 'AreaConocimientoController@edit')->name('areas_conocimientos.edit');
            Route::post('areas_conocimientos/{id}', 'AreaConocimientoController@update')->name('areas_conocimientos.update');
            Route::get('areas_conocimientos/eliminar/{id}', 'AreaConocimientoController@get_destroy')->name('areas_conocimientos.get_destroy');
            Route::delete('areas_conocimientos/eliminar/{id}', 'AreaConocimientoController@destroy')->name('areas_conocimientos.destroy');

//Usuarios
    //Lista de Usuarios
        Route::get('usuarios', 'UserController@index')->name('usuarios.index');
        Route::post('usuarios', 'UserController@index');
        Route::get('usuarios/agregar', 'UserController@create')->name('usuarios.create');
        Route::post('usuarios/agregar', 'UserController@store')->name('usuarios.store');
        Route::get('usuarios/editar/{id}', 'UserController@edit')->name('usuarios.edit');
        Route::post('usuarios/actualizar/{id}', 'UserController@update')->name('usuarios.update');
        Route::post('usuarios/inactivar/{id}', 'UserController@unactivate')->name('usuarios.unactivate');
        Route::post('usuarios/activar/{id}', 'UserController@activate')->name('usuarios.activate');
        Route::get('usuarios/eliminar/{id}', 'UserController@destroy')->name('usuarios.get_destroy');
        Route::delete('usuarios/eliminar/{id}', 'UserController@destroy')->name('usuarios.destroy');
    //Roles y Permisos
        //Roles
            Route::get('roles', 'RoleController@index')->name('roles.index');
            Route::get('roles/ver/{id}', 'RoleController@show')->name('roles.show');
            Route::get('roles/agregar', 'RoleController@create')->name('roles.create');
            Route::post('roles/agregar', 'RoleController@store')->name('roles.store');
            Route::get('roles/editar/{id}', 'RoleController@edit')->name('roles.edit');
            Route::post('roles/editar/{id}', 'RoleController@update')->name('roles.update');
            Route::post('roles/inactivar/{id}', 'RoleController@unactivate')->name('roles.unactivate');
            Route::post('roles/activar/{id}', 'RoleController@activate')->name('roles.activate');
            Route::get('roles/eliminar/{id}', 'RoleController@destroy')->name('roles.get_destroy');
            Route::delete('roles/eliminar/{id}', 'RoleController@destroy')->name('roles.destroy');
        //Permisos
            Route::get('permisos', 'PermissionController@index')->name('permisos.index');
    //Auditoría
        Route::get('auditoria', 'AuditoriaController@index')->name('auditoria.index');
        Route::get('auditoria/ver/{id}', 'AuditoriaController@show')->name('auditoria.show');

//Parametros
    //Nacionalidades
        Route::get('nacionalidades', 'NacionalidadController@index')->name('nacionalidades.index');
        Route::get('nacionalidades/all', 'NacionalidadController@index_ajax')->name('nacionalidades.index_ajax');
        Route::get('nacionalidades/show/{id}', 'NacionalidadController@show')->name('nacionalidades.show');
        Route::post('nacionalidades', 'NacionalidadController@store')->name('nacionalidades.store');
        Route::get('nacionalidades/{id}', 'NacionalidadController@edit')->name('nacionalidades.edit');
        Route::post('nacionalidades/{id}', 'NacionalidadController@update')->name('nacionalidades.update');
        Route::get('nacionalidades/eliminar/{id}', 'NacionalidadController@get_destroy')->name('nacionalidades.get_destroy');
        Route::delete('nacionalidades/eliminar/{id}', 'NacionalidadController@destroy')->name('nacionalidades.destroy');
    //Departamentos Paraguay
        Route::get('departamentos_paraguay', 'DepartamentoParaguayController@index')->name('departamentos_paraguay.index');
        Route::get('departamentos_paraguay/all', 'DepartamentoParaguayController@index_ajax')->name('departamentos_paraguay.index_ajax');
        Route::get('departamentos_paraguay/show/{id}', 'DepartamentoParaguayController@show')->name('departamentos_paraguay.show');
        Route::post('departamentos_paraguay', 'DepartamentoParaguayController@store')->name('departamentos_paraguay.store');
        Route::get('departamentos_paraguay/{id}', 'DepartamentoParaguayController@edit')->name('departamentos_paraguay.edit');
        Route::post('departamentos_paraguay/{id}', 'DepartamentoParaguayController@update')->name('departamentos_paraguay.update');
        Route::get('departamentos_paraguay/eliminar/{id}', 'DepartamentoParaguayController@get_destroy')->name('departamentos_paraguay.get_destroy');
        Route::delete('departamentos_paraguay/eliminar/{id}', 'DepartamentoParaguayController@destroy')->name('departamentos_paraguay.destroy');
    //Ciudades
        Route::get('ciudades', 'CiudadController@index')->name('ciudades.index');
        Route::get('ciudades/all', 'CiudadController@index_ajax')->name('ciudades.index_ajax');
        Route::get('ciudades/show/{id}', 'CiudadController@show')->name('ciudades.show');
        Route::post('ciudades', 'CiudadController@store')->name('ciudades.store');
        Route::get('ciudades/{id}', 'CiudadController@edit')->name('ciudades.edit');
        Route::post('ciudades/{id}', 'CiudadController@update')->name('ciudades.update');
        Route::get('ciudades/eliminar/{id}', 'CiudadController@get_destroy')->name('ciudades.get_destroy');
        Route::delete('ciudades/eliminar/{id}', 'CiudadController@destroy')->name('ciudades.destroy');
    //Barrios
        Route::get('barrios', 'BarrioController@index')->name('barrios.index');
        Route::get('barrios/all', 'BarrioController@index_ajax')->name('barrios.index_ajax');
        Route::get('barrios/show/{id}', 'BarrioController@show')->name('barrios.show');
        Route::post('barrios', 'BarrioController@store')->name('barrios.store');
        Route::get('barrios/{id}', 'BarrioController@edit')->name('barrios.edit');
        Route::post('barrios/{id}', 'BarrioController@update')->name('barrios.update');
        Route::get('barrios/eliminar/{id}', 'BarrioController@get_destroy')->name('barrios.get_destroy');
        Route::delete('barrios/eliminar/{id}', 'BarrioController@destroy')->name('barrios.destroy');
    //Formas de Conocer la Universidad
        Route::get('formas_conocimientos', 'FormaConocimientoController@index')->name('formas_conocimientos.index');
        Route::get('formas_conocimientos/all', 'FormaConocimientoController@index_ajax')->name('formas_conocimientos.index_ajax');
        Route::get('formas_conocimientos/ver/{id}', 'FormaConocimientoController@show')->name('formas_conocimientos.show');
        Route::post('formas_conocimientos', 'FormaConocimientoController@store')->name('formas_conocimientos.store');
        Route::get('formas_conocimientos/{id}', 'FormaConocimientoController@edit')->name('formas_conocimientos.edit');
        Route::post('formas_conocimientos/{id}', 'FormaConocimientoController@update')->name('formas_conocimientos.update');
        Route::get('formas_conocimientos/eliminar/{id}', 'FormaConocimientoController@get_destroy')->name('formas_conocimientos.get_destroy');
        Route::delete('formas_conocimientos/eliminar/{id}', 'FormaConocimientoController@destroy')->name('formas_conocimientos.destroy');

//Perfiles
    Route::get('perfil/{id}', 'PerfilController@show')->name('perfiles.show');
    Route::post('perfil/{id}', 'PerfilController@update')->name('perfiles.update');
//Configuraciones de diseño
    Route::post('/modo_claro/{id}', 'ConfiguracionController@light_mode')->name('configuraciones.light_mode');
    Route::post('/modo_oscuro/{id}', 'ConfiguracionController@dark_mode')->name('configuraciones.dark_mode');

//Pantallas Alumnos
    Route::get('inicio_alumnos/{id}', 'PantallaAlumnoController@extensiones_universitarias')->name('pantallas_alumnos.index');
    Route::get('extension_universitaria/{id}', 'PantallaAlumnoController@extensiones_universitarias')->name('pantallas_alumnos.extensiones_universitarias');
    Route::post('extension_universitaria/adjuntar_certificado/{id}', 'PantallaAlumnoController@adjuntar_certificado_extensiones_universitarias')->name('pantallas_alumnos.adjuntar_certificado_extensiones_universitarias');
    Route::get('extension_universitaria/eliminar_certificado/{id}', 'PantallaAlumnoController@eliminar_certificado_extensiones_universitarias')->name('pantallas_alumnos.get_eliminar_certificado_extensiones_universitarias');
    Route::delete('extension_universitaria/eliminar_certificado/{id}', 'PantallaAlumnoController@eliminar_certificado_extensiones_universitarias')->name('pantallas_alumnos.eliminar_certificado_extensiones_universitarias');
    Route::get('extension_universitaria/catalogo/{id}', 'PantallaAlumnoController@catalogo_extensiones_universitarias')->name('pantallas_alumnos.catalogo_extensiones_universitarias');
    Route::post('extension_universitaria/postular/{id}/{idExtension}', 'PantallaAlumnoController@postular_extension_universitaria')->name('pantallas_alumnos.postular_extension_universitaria');
    Route::delete('extension_universitaria/cancelar_postulacion/{id}/{idDetalle}', 'PantallaAlumnoController@cancelar_postulacion_extension_universitaria')->name('pantallas_alumnos.cancelar_postulacion_extension_universitaria');

//Pantallas Docentes
    Route::get('inicio_docentes/{id}', 'PantallaDocenteController@extensiones_universitarias')->name('pantallas_docentes.index');
    Route::get('extensiones_universitarias/{id}', 'PantallaDocenteController@extensiones_universitarias')->name('pantallas_docentes.extensiones_universitarias');
    Route::get('ver_extension_universitaria/{id}', 'PantallaDocenteController@show_extensiones_universitarias')->name('pantallas_docentes.show_extensiones_universitarias');
    Route::get('crear_extensiones_universitarias/{id}', 'PantallaDocenteController@create_extensiones_universitarias')->name('pantallas_docentes.create_extensiones_universitarias');
