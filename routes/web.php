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

Auth::routes();
//Dashboard
    Route::get('/', 'HomeController@root')->name('root');

//ATC
    //Cajero
        Route::get('caja/agregar', 'CajeroController@create')->name('cajero.create');
        Route::post('caja/agregar', 'CajeroController@store')->name('cajero.store');
        Route::get('caja/get_clientes/{alumno}', 'CajeroController@get_clientes')->name('cajero.get_clientes');
        Route::get('caja/get_pagos/{alumno}', 'CajeroController@get_pagos')->name('cajero.get_pagos');
        Route::get('caja/get_notas_creditos/{venta}', 'CajeroController@get_notas_creditos')->name('cajero.get_notas_creditos');

    //Cajero
        Route::get('arqueos', 'CajeroController@arqueos_index')->name('arqueo_caja.index');
        Route::get('arqueo/nuevo', 'CajeroController@create_arqueo')->name('arqueo_caja.create');
        Route::get('arqueo/generar', 'CajeroController@generate_arqueo')->name('arqueo_caja.generate_arqueo');

    //Clientes
        Route::get('clientes', 'ClienteController@index')->name('clientes.index');
        Route::get('clientes/ver/{id}', 'ClienteController@show')->name('clientes.show');
        Route::get('clientes/agregar', 'ClienteController@create')->name('clientes.create');
        Route::post('clientes/agregar', 'ClienteController@store')->name('clientes.store');
        Route::get('clientes/editar/{id}', 'ClienteController@edit')->name('clientes.edit');
        Route::post('clientes/actualizar/{id}', 'ClienteController@update')->name('clientes.update');
        Route::post('clientes/inactivar/{id}', 'ClienteController@unactivate')->name('clientes.unactivate');
        Route::post('clientes/activar/{id}', 'ClienteController@activate')->name('clientes.activate');
        Route::get('clientes/eliminar/{id}', 'ClienteController@destroy')->name('clientes.get_destroy');
        Route::delete('clientes/eliminar/{id}', 'ClienteController@destroy')->name('clientes.destroy');

//Academico
    //Alumnos
        Route::get('alumnos', 'AlumnoController@index')->name('alumnos.index');
        Route::post('alumnos', 'AlumnoController@index');
        Route::get('alumnos/ver/{id}', 'AlumnoController@show')->name('alumnos.show');
        Route::get('alumnos/notas/{id}', 'AlumnoController@show_notas')->name('alumnos.show_notas');
        Route::get('alumnos/notas_espejo/{id}', 'AlumnoController@show_notas_espejo')->name('alumnos.show_notas_espejo');
        Route::get('alumnos/asistencias/{id}', 'AlumnoController@show_asistencias')->name('alumnos.show_asistencias');
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
    //Certificados de Estudios
        Route::get('alumnos/certificado_estudios/{id}/{tipo}', 'CertificadoEstudioController@show')->name('certificados_estudios.show');
        Route::get('alumnos/certificado_estudios/generar/{id}/{tipo}', 'CertificadoEstudioController@generar')->name('certificados_estudios.generar');
    //Matriculaciones
        Route::get('matriculaciones', 'MatriculacionController@index')->name('matriculaciones.index');
        Route::post('matriculaciones', 'MatriculacionController@index');
        Route::get('matriculaciones/ver/{id}', 'MatriculacionController@show')->name('matriculaciones.show');
        Route::get('matriculaciones/agregar', 'MatriculacionController@create')->name('matriculaciones.create');
        Route::post('matriculaciones/agregar', 'MatriculacionController@store')->name('matriculaciones.store');
        Route::post('matriculaciones/inactivar/{id}', 'MatriculacionController@unactivate')->name('matriculaciones.unactivate');
        Route::post('matriculaciones/activar/{id}', 'MatriculacionController@activate')->name('matriculaciones.activate');
        Route::get('matriculaciones/eliminar/{id}', 'MatriculacionController@destroy')->name('matriculaciones.get_destroy');
        Route::delete('matriculaciones/eliminar/{id}', 'MatriculacionController@destroy')->name('matriculaciones.destroy');
        Route::get('matriculaciones/get_carrera/{id}', 'MatriculacionController@get_carrera')->name('matriculaciones.get_carrera');
        Route::get('matriculaciones/validate_carrera_siu/{alumno}/{carrera}/{programa}', 'MatriculacionController@validate_carrera_siu')->name('matriculaciones.validate_carrera_siu');
        Route::get('matriculaciones/contrato/{id}', 'MatriculacionController@pdf_contrato')->name('matriculaciones.pdf_contrato');
        Route::get('matriculaciones/ver_horarios', 'MatriculacionController@show_horarios')->name('matriculaciones.show_horarios');
        Route::get('matriculaciones/horarios', 'MatriculacionController@pdf_horarios')->name('matriculaciones.pdf_horarios');
        Route::post('matriculaciones/agregar_convenio/{id}', 'MatriculacionController@agregar_convenio')->name('matriculaciones.agregar_convenio');
        Route::post('matriculaciones/eliminar_convenio/{id}', 'MatriculacionController@eliminar_convenio')->name('matriculaciones.eliminar_convenio');
    //Inscripciones
        Route::get('inscripciones/ver/{id}', 'InscripcionController@show')->name('inscripciones.show');
        Route::get('inscripciones/editar/{id}', 'InscripcionController@edit')->name('inscripciones.edit');
        Route::post('inscripciones/actualizar/{id}', 'InscripcionController@update')->name('inscripciones.update');
        Route::get('inscripciones/ver_horario/{id}', 'InscripcionController@show_horarios')->name('inscripciones.show_horarios');
        Route::get('inscripciones/imprimir_horario/{id}', 'InscripcionController@pdf_horarios')->name('inscripciones.pdf_horarios');
        Route::post('inscripciones/desmatricular/{id}', 'InscripcionController@unactivate')->name('inscripciones.unactivate');
        Route::post('inscripciones/anular_desmatriculacion/{id}', 'InscripcionController@activate')->name('inscripciones.activate');
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
        Route::get('docentes/show_horas/{id}', 'DocenteController@show_horas')->name('docentes.get_show_horas');
        Route::post('docentes/show_horas/{id}', 'DocenteController@show_horas')->name('docentes.show_horas');
        Route::post('docentes/reporte_horas/{id}', 'DocenteController@reporte_horas')->name('docentes.reporte_horas');
        //Salarios
            Route::get('docentes/salarios', 'DocenteSalarioController@index')->name('docentes_salarios.index');
            Route::post('docentes/salarios', 'DocenteSalarioController@index');
            Route::post('docentes/rechazar_salario/{id}', 'DocenteSalarioController@reject')->name('docentes_salarios.reject');
            Route::post('docentes/anular_rechazo_salario/{id}', 'DocenteSalarioController@unreject')->name('docentes_salarios.unreject');
            Route::post('docentes/aprobar_salario/{id}', 'DocenteSalarioController@approve')->name('docentes_salarios.approve');
            Route::post('docentes/anular_aprobacion_salario/{id}', 'DocenteSalarioController@unapprove')->name('docentes_salarios.unapprove');
            Route::get('docentes/eliminar_salario/{id}', 'DocenteSalarioController@destroy')->name('docentes_salarios.get_destroy');
            Route::post('docentes/eliminar_salario/{id}', 'DocenteSalarioController@destroy')->name('docentes_salarios.destroy');
    //Gestiones
        //Examenes de Suficiencia
            Route::get('examenes_suficiencia/', 'ExamenSuficienciaController@index')->name('examenes_suficiencia.index');
            Route::get('examenes_suficiencia/ver/{id}', 'ExamenSuficienciaController@show')->name('examenes_suficiencia.show');
            Route::get('examenes_suficiencia/agregar', 'ExamenSuficienciaController@create')->name('examenes_suficiencia.create');
            Route::post('examenes_suficiencia/agregar', 'ExamenSuficienciaController@store')->name('examenes_suficiencia.store');
            Route::get('examenes_suficiencia/editar/{id}', 'ExamenSuficienciaController@edit')->name('examenes_suficiencia.edit');
            Route::post('examenes_suficiencia/actualizar/{id}', 'ExamenSuficienciaController@update')->name('examenes_suficiencia.update');
            Route::get('examenes_suficiencia/cargar_puntaje/{acta}', 'ExamenSuficienciaController@create_puntaje')->name('examenes_suficiencia.create_puntaje');
            Route::post('examenes_suficiencia/guardar_puntaje/{acta}', 'ExamenSuficienciaController@store_puntaje')->name('examenes_suficiencia.store_puntaje');
            Route::post('examenes_suficiencia/editar_puntaje/{acta_alumno}', 'ExamenSuficienciaController@update_puntaje')->name('examenes_suficiencia.update_puntaje');
            Route::get('examenes_suficiencia/eliminar/{id}', 'ExamenSuficienciaController@destroy')->name('examenes_suficiencia.get_destroy');
            Route::delete('examenes_suficiencia/eliminar/{id}', 'ExamenSuficienciaController@destroy')->name('examenes_suficiencia.destroy');
            Route::get('examenes_suficiencia/actas/', 'ExamenSuficienciaActaEvaluacionController@index')->name('examenes_suficiencia.index_actas');
            Route::get('examenes_suficiencia/actas/ver/{id}', 'ExamenSuficienciaActaEvaluacionController@show')->name('examenes_suficiencia.show_acta');
            Route::get('examenes_suficiencia/actas/generar', 'ExamenSuficienciaActaEvaluacionController@generate')->name('examenes_suficiencia.generate_acta');
            Route::post('examenes_suficiencia/actas/generar', 'ExamenSuficienciaActaEvaluacionController@generate');
            Route::get('examenes_suficiencia/actas/eliminar/{id}', 'ExamenSuficienciaActaEvaluacionController@destroy')->name('examenes_suficiencia.get_destroy_acta');
            Route::delete('examenes_suficiencia/actas/eliminar/{id}', 'ExamenSuficienciaActaEvaluacionController@destroy')->name('examenes_suficiencia.destroy_acta');
            Route::post('examenes_suficiencia/actas/subir/{id}', 'ExamenSuficienciaActaEvaluacionController@subir_acta')->name('examenes_suficiencia.subir_acta');
            Route::get('examenes_suficiencia/actas/eliminar_acta/{id}', 'ExamenSuficienciaActaEvaluacionController@eliminar_acta')->name('examenes_suficiencia.eliminar_acta');
            Route::delete('examenes_suficiencia/actas/eliminar_acta/{id}', 'ExamenSuficienciaActaEvaluacionController@eliminar_acta');
        //Materias por Semestres
            Route::get('materias_semestres', 'MateriaSemestreController@index')->name('materias_semestres.index');
            //Evaluaciones
                Route::get('materias_evaluaciones/{materia}/{semestre}/{carrera}', 'MateriaEvaluacionController@show')->name('materias_evaluaciones.show');
                Route::get('materias_evaluaciones/crear/{materia}/{semestre}/{carrera}', 'MateriaEvaluacionController@create')->name('materias_evaluaciones.create');
                Route::get('materias_evaluaciones/get_create/{tipo}/{materia}/{semestre}/{carrera}', 'MateriaEvaluacionController@get_create_ajax')->name('materias_evaluaciones.get_create_ajax');
                Route::get('materias_evaluaciones/agregar', 'MateriaEvaluacionController@store')->name('materias_evaluaciones.store');
                Route::post('materias_evaluaciones/agregar', 'MateriaEvaluacionController@store');
                Route::get('materias_evaluaciones/actualizar/{id}', 'MateriaEvaluacionController@update')->name('materias_evaluaciones.update');
                Route::post('materias_evaluaciones/actualizar/{id}', 'MateriaEvaluacionController@update');
            //Asistencias
                Route::get('alumnos_asistencias', 'AlumnoAsistenciaController@index')->name('alumnos_asistencias.index');
                Route::get('alumnos_asistencias/ver/{id}', 'AlumnoAsistenciaController@show')->name('alumnos_asistencias.show');
                Route::get('alumnos_asistencias/agregar_administrativo/{materia}/{semestre}/{docente}', 'AlumnoAsistenciaController@create')->name('alumnos_asistencias.create');
                Route::get('alumnos_asistencias/agregar_por_materia/{materia}/{semestre}/{carrera}', 'AlumnoAsistenciaController@create_materia')->name('alumnos_asistencias.create_materia');
                Route::get('alumnos_asistencias/agregar_por_carrera/{materia}/{semestre}/{carrera}', 'AlumnoAsistenciaController@create_carrera')->name('alumnos_asistencias.create_carrera');
                Route::post('alumnos_asistencias/agregar', 'AlumnoAsistenciaController@store')->name('alumnos_asistencias.store');
                Route::get('alumnos_asistencias/editar/{id}', 'AlumnoAsistenciaController@edit')->name('alumnos_asistencias.edit');
                Route::post('alumnos_asistencias/actualizar/{id}', 'AlumnoAsistenciaController@update')->name('alumnos_asistencias.update');
                Route::get('alumnos_asistencias/editar_observacion/{id}', 'AlumnoAsistenciaController@edit_observacion')->name('alumnos_asistencias.edit_observacion');
                Route::post('alumnos_asistencias/actualizar_observacion/{id}', 'AlumnoAsistenciaController@update_observacion')->name('alumnos_asistencias.update_observacion');
        //Actas
            Route::get('actas_evaluaciones', 'ActaEvaluacionController@index')->name('actas_evaluaciones.index');
            Route::get('actas_evaluaciones/ver/{id}', 'ActaEvaluacionController@show')->name('actas_evaluaciones.show');
            Route::get('actas_evaluaciones/agregar', 'ActaEvaluacionController@create')->name('actas_evaluaciones.create');
            Route::post('actas_evaluaciones/agregar', 'ActaEvaluacionController@store')->name('actas_evaluaciones.store');
            Route::get('actas_evaluaciones/eliminar/{id}', 'ActaEvaluacionController@destroy')->name('actas_evaluaciones.get_destroy');
            Route::delete('actas_evaluaciones/eliminar/{id}', 'ActaEvaluacionController@destroy')->name('actas_evaluaciones.destroy');
            Route::post('actas_evaluaciones/subir_acta/{id}', 'ActaEvaluacionController@subir_acta')->name('actas_evaluaciones.subir_acta');
            Route::get('actas_evaluaciones/eliminar_acta/{id}', 'ActaEvaluacionController@eliminar_acta')->name('actas_evaluaciones.eliminar_acta');
            Route::delete('actas_evaluaciones/eliminar_acta/{id}', 'ActaEvaluacionController@eliminar_acta');
            Route::get('actas_evaluaciones/ver_actas/{materia}/{semestre}/{carrera}', 'ActaEvaluacionController@show_actas')->name('actas_evaluaciones.show_actas');
            Route::get('actas_evaluaciones/generar_acta/{materia}/{semestre}/{carrera}/{tipo}', 'ActaEvaluacionController@generar_actas')->name('actas_evaluaciones.generar_actas');
        //Clases
            Route::get('clases_materias', 'ClaseMateriaController@index')->name('clases_materias.index');
            Route::post('clases_materias', 'ClaseMateriaController@index');
            Route::get('clases_materias/ver/{id}', 'ClaseMateriaController@show')->name('clases_materias.show');
            Route::get('clases_materias/agregar_por_materia/{materia}/{semestre}/{carrera}', 'ClaseMateriaController@create_materia')->name('clases_materias.create_materia');
            Route::get('clases_materias/agregar_por_carrera/{materia}/{semestre}/{carrera}', 'ClaseMateriaController@create_carrera')->name('clases_materias.create_carrera');
            Route::post('clases_materias/agregar', 'ClaseMateriaController@store')->name('clases_materias.store');
            Route::get('clases_materias/eliminar/{id}', 'ClaseMateriaController@destroy')->name('clases_materias.get_destroy');
            Route::delete('clases_materias/eliminar/{id}', 'ClaseMateriaController@destroy')->name('clases_materias.destroy');
        //Seguimiento Docente
            Route::get('seguimiento_docente', 'SeguimientoDocenteController@index')->name('seguimiento_docente.index');
            Route::post('seguimiento_docente', 'SeguimientoDocenteController@index');
            Route::get('seguimiento_docente/ver/{id}', 'SeguimientoDocenteController@show')->name('seguimiento_docente.show');
            Route::get('seguimiento_docente/agregar_por_materia/{materia}/{semestre}/{carrera}', 'SeguimientoDocenteController@create_materia')->name('seguimiento_docente.create_materia');
            Route::get('seguimiento_docente/agregar_por_carrera/{materia}/{semestre}/{carrera}', 'SeguimientoDocenteController@create_carrera')->name('seguimiento_docente.create_carrera');
            Route::post('seguimiento_docente/agregar', 'SeguimientoDocenteController@store')->name('seguimiento_docente.store');
            Route::get('seguimiento_docente/eliminar/{id}', 'SeguimientoDocenteController@destroy')->name('seguimiento_docente.get_destroy');
            Route::delete('seguimiento_docente/eliminar/{id}', 'SeguimientoDocenteController@destroy')->name('seguimiento_docente.destroy');
            Route::get('seguimiento_docente/generar_pdf/{filtro_mes}/{filtro_periodo}/{filtro_programa}', 'SeguimientoDocenteController@generate_pdf')->name('seguimiento_docente.generate_pdf');
        //Anulaciones de Correlatividades
        Route::get('anulaciones_correlatividades', 'AnulacionCorrelatividadController@index')->name('anulaciones_correlatividades.index');
        Route::get('anulaciones_correlatividades/all', 'AnulacionCorrelatividadController@index_ajax')->name('anulaciones_correlatividades.index_ajax');
        Route::get('anulaciones_correlatividades/ver/{id}', 'AnulacionCorrelatividadController@show')->name('anulaciones_correlatividades.show');
        Route::post('anulaciones_correlatividades', 'AnulacionCorrelatividadController@store')->name('anulaciones_correlatividades.store');
        Route::get('anulaciones_correlatividades/inactivar/{id}', 'AnulacionCorrelatividadController@get_unactivate')->name('anulaciones_correlatividades.get_unactivate');
        Route::post('anulaciones_correlatividades/inactivar/{id}', 'AnulacionCorrelatividadController@unactivate')->name('anulaciones_correlatividades.unactivate');
        Route::get('anulaciones_correlatividades/activar/{id}', 'AnulacionCorrelatividadController@get_activate')->name('anulaciones_correlatividades.get_activate');
        Route::post('anulaciones_correlatividades/activar/{id}', 'AnulacionCorrelatividadController@activate')->name('anulaciones_correlatividades.activate');
        Route::get('anulaciones_correlatividades/eliminar/{id}', 'AnulacionCorrelatividadController@get_destroy')->name('anulaciones_correlatividades.get_destroy');
        Route::delete('anulaciones_correlatividades/eliminar/{id}', 'AnulacionCorrelatividadController@destroy')->name('anulaciones_correlatividades.destroy');
    //Convalidaciones
        //Todos
            Route::get('convalidaciones', 'ConvalidacionController@index')->name('convalidaciones.index');
            Route::get('convalidaciones/ver/{id}', 'ConvalidacionController@show')->name('convalidaciones.show');
        //Externas
            Route::get('convalidaciones_externas', 'ConvalidacionExternaController@index')->name('convalidaciones_externas.index');
            Route::get('convalidaciones_externas/ver/{id}', 'ConvalidacionExternaController@show')->name('convalidaciones_externas.show');
            Route::get('convalidaciones_externas/agregar', 'ConvalidacionExternaController@create')->name('convalidaciones_externas.create');
            Route::post('convalidaciones_externas/agregar', 'ConvalidacionExternaController@store')->name('convalidaciones_externas.store');
            Route::get('convalidaciones_externas/editar/{id}', 'ConvalidacionExternaController@edit')->name('convalidaciones_externas.edit');
            Route::post('convalidaciones_externas/actualizar/{id}', 'ConvalidacionExternaController@update')->name('convalidaciones_externas.update');
            Route::post('convalidaciones_externas/cambiar_certificado/{id}', 'ConvalidacionExternaController@change_certificado')->name('convalidaciones_externas.change_certificado');
            Route::post('convalidaciones_externas/dictaminar/{id}', 'ConvalidacionExternaController@dictaminar')->name('convalidaciones_externas.dictaminar');
            Route::post('convalidaciones_externas/aprobar/{id}', 'ConvalidacionExternaController@aprobar')->name('convalidaciones_externas.aprobar');
            Route::get('convalidaciones_externas/eliminar/{id}', 'ConvalidacionExternaController@destroy')->name('convalidaciones_externas.get_destroy');
            Route::delete('convalidaciones_externas/eliminar/{id}', 'ConvalidacionExternaController@destroy')->name('convalidaciones_externas.destroy');
            Route::get('convalidaciones_externas/get_carrera/{alumno}', 'ConvalidacionExternaController@get_carrera')->name('convalidaciones_externas.get_carrera');
            Route::get('convalidaciones_externas/eliminar_dictamen/{id}', 'ConvalidacionExternaController@destroy_dictamen')->name('convalidaciones_externas.get_destroy_dictamen');
            Route::delete('convalidaciones_externas/eliminar_dictamen/{id}', 'ConvalidacionExternaController@destroy_dictamen')->name('convalidaciones_externas.destroy_dictamen');
            Route::get('convalidaciones_externas/eliminar_resolucion/{id}', 'ConvalidacionExternaController@destroy_resolucion')->name('convalidaciones_externas.get_destroy_resolucion');
            Route::delete('convalidaciones_externas/eliminar_resolucion/{id}', 'ConvalidacionExternaController@destroy_resolucion')->name('convalidaciones_externas.destroy_resolucion');
            Route::get('convalidaciones_externas/cencelar_dictamen/{id}', 'ConvalidacionExternaController@cancel_dictamen')->name('convalidaciones_externas.get_cancel_dictamen');
            Route::post('convalidaciones_externas/cancelar_dictamen/{id}', 'ConvalidacionExternaController@cancel_dictamen')->name('convalidaciones_externas.cancel_dictamen');
            Route::get('convalidaciones_externas/cencelar_resolucion/{id}', 'ConvalidacionExternaController@cancel_resolucion')->name('convalidaciones_externas.get_cancel_resolucion');
            Route::post('convalidaciones_externas/cancelar_resolucion/{id}', 'ConvalidacionExternaController@cancel_resolucion')->name('convalidaciones_externas.cancel_resolucion');
            Route::post('convalidaciones_externas/convalidar/{id}', 'ConvalidacionExternaController@convalidar')->name('convalidaciones_externas.convalidar');
            Route::get('convalidaciones_externas/get_numero_dictamen/{id}', 'ConvalidacionExternaController@get_numero_dictamen')->name('convalidaciones_externas.get_numero_dictamen');
            Route::get('convalidaciones_externas/get_numero_resolucion/{id}', 'ConvalidacionExternaController@get_numero_resolucion')->name('convalidaciones_externas.get_numero_resolucion');
        //Internas
            Route::get('convalidaciones_internas', 'ConvalidacionInternaController@index')->name('convalidaciones_internas.index');
            Route::get('convalidaciones_internas/ver/{id}', 'ConvalidacionInternaController@show')->name('convalidaciones_internas.show');
            Route::get('convalidaciones_internas/agregar', 'ConvalidacionInternaController@create')->name('convalidaciones_internas.create');
            Route::post('convalidaciones_internas/agregar', 'ConvalidacionInternaController@store')->name('convalidaciones_internas.store');
            Route::get('convalidaciones_internas/editar/{id}', 'ConvalidacionInternaController@edit')->name('convalidaciones_internas.edit');
            Route::post('convalidaciones_internas/actualizar/{id}', 'ConvalidacionInternaController@update')->name('convalidaciones_internas.update');
            Route::post('convalidaciones_internas/cambiar_certificado/{id}', 'ConvalidacionInternaController@change_certificado')->name('convalidaciones_internas.change_certificado');
            Route::post('convalidaciones_internas/dictaminar/{id}', 'ConvalidacionInternaController@dictaminar')->name('convalidaciones_internas.dictaminar');
            Route::post('convalidaciones_internas/aprobar/{id}', 'ConvalidacionInternaController@aprobar')->name('convalidaciones_internas.aprobar');
            Route::get('convalidaciones_internas/eliminar/{id}', 'ConvalidacionInternaController@destroy')->name('convalidaciones_internas.get_destroy');
            Route::delete('convalidaciones_internas/eliminar/{id}', 'ConvalidacionInternaController@destroy')->name('convalidaciones_internas.destroy');
            Route::get('convalidaciones_internas/get_carrera/{alumno}', 'ConvalidacionInternaController@get_carrera')->name('convalidaciones_internas.get_carrera');
            Route::get('convalidaciones_internas/get_materias/{carrera}', 'ConvalidacionInternaController@get_materias')->name('convalidaciones_internas.get_materias');
            Route::get('convalidaciones_internas/get_nota/{alumno}/{materia}', 'ConvalidacionInternaController@get_nota')->name('convalidaciones_internas.get_nota');
            Route::get('convalidaciones_internas/eliminar_dictamen/{id}', 'ConvalidacionInternaController@destroy_dictamen')->name('convalidaciones_internas.get_destroy_dictamen');
            Route::delete('convalidaciones_internas/eliminar_dictamen/{id}', 'ConvalidacionInternaController@destroy_dictamen')->name('convalidaciones_internas.destroy_dictamen');
            Route::get('convalidaciones_internas/eliminar_resolucion/{id}', 'ConvalidacionInternaController@destroy_resolucion')->name('convalidaciones_internas.get_destroy_resolucion');
            Route::delete('convalidaciones_internas/eliminar_resolucion/{id}', 'ConvalidacionInternaController@destroy_resolucion')->name('convalidaciones_internas.destroy_resolucion');
            Route::get('convalidaciones_internas/cencelar_dictamen/{id}', 'ConvalidacionInternaController@cancel_dictamen')->name('convalidaciones_internas.get_cancel_dictamen');
            Route::post('convalidaciones_internas/cancelar_dictamen/{id}', 'ConvalidacionInternaController@cancel_dictamen')->name('convalidaciones_internas.cancel_dictamen');
            Route::get('convalidaciones_internas/cencelar_resolucion/{id}', 'ConvalidacionInternaController@cancel_resolucion')->name('convalidaciones_internas.get_cancel_resolucion');
            Route::post('convalidaciones_internas/cancelar_resolucion/{id}', 'ConvalidacionInternaController@cancel_resolucion')->name('convalidaciones_internas.cancel_resolucion');
            Route::post('convalidaciones_internas/convalidar/{id}', 'ConvalidacionInternaController@convalidar')->name('convalidaciones_internas.convalidar');
            Route::get('convalidaciones_internas/get_numero_dictamen/{id}', 'ConvalidacionInternaController@get_numero_dictamen')->name('convalidaciones_internas.get_numero_dictamen');
            Route::get('convalidaciones_internas/get_numero_resolucion/{id}', 'ConvalidacionInternaController@get_numero_resolucion')->name('convalidaciones_internas.get_numero_resolucion');
        //Trabajos Finales de Grado
            //Tesis
            Route::get('tesis', 'TesisController@index')->name('tesis.index');
            //Inscripciones Temas
                Route::get('inscripciones_temas_tesis', 'InscripcionTemaTesisController@index')->name('inscripciones_temas_tesis.index');
                Route::get('inscripciones_temas_tesis/ver/{id}', 'InscripcionTemaTesisController@show')->name('inscripciones_temas_tesis.show');
                Route::get('inscripciones_temas_tesis/agregar', 'InscripcionTemaTesisController@create')->name('inscripciones_temas_tesis.create');
                Route::post('inscripciones_temas_tesis/agregar', 'InscripcionTemaTesisController@store')->name('inscripciones_temas_tesis.store');
                Route::get('inscripciones_temas_tesis/editar/{id}', 'InscripcionTemaTesisController@edit')->name('inscripciones_temas_tesis.edit');
                Route::post('inscripciones_temas_tesis/actualizar/{id}', 'InscripcionTemaTesisController@update')->name('inscripciones_temas_tesis.update');
                Route::post('inscripciones_temas_tesis/aprobar_coordinacion/{id}', 'InscripcionTemaTesisController@aprobar_coordinacion')->name('inscripciones_temas_tesis.aprobar_coordinacion');
                Route::post('inscripciones_temas_tesis/aprobar_tutor/{id}', 'InscripcionTemaTesisController@aprobar_tutor')->name('inscripciones_temas_tesis.aprobar_tutor');
                Route::post('inscripciones_temas_tesis/anular_aprobacion_coordinacion/{id}', 'InscripcionTemaTesisController@anular_aprobacion_coordinacion')->name('inscripciones_temas_tesis.anular_aprobacion_coordinacion');
                Route::post('inscripciones_temas_tesis/anular_aprobacion_tutor/{id}', 'InscripcionTemaTesisController@anular_aprobacion_tutor')->name('inscripciones_temas_tesis.anular_aprobacion_tutor');
                Route::post('inscripciones_temas_tesis/rechazar/{id}', 'InscripcionTemaTesisController@rechazar')->name('inscripciones_temas_tesis.rechazar');
                Route::post('inscripciones_temas_tesis/anular_rechazo/{id}', 'InscripcionTemaTesisController@anular_rechazo')->name('inscripciones_temas_tesis.anular_rechazo');
                Route::get('inscripciones_temas_tesis/eliminar/{id}', 'InscripcionTemaTesisController@destroy')->name('inscripciones_temas_tesis.get_destroy');
                Route::delete('inscripciones_temas_tesis/eliminar/{id}', 'InscripcionTemaTesisController@destroy')->name('inscripciones_temas_tesis.destroy');
                Route::get('acta_evaluacion_tesis/generar/{id}', 'InscripcionTemaTesisController@generate_acta')->name('inscripciones_temas_tesis.generate_acta');
            //Anteproyectos
                Route::get('anteproyectos_tesis', 'AnteproyectoTesisController@index')->name('anteproyectos_tesis.index');
                Route::get('anteproyectos_tesis/ver/{id}', 'AnteproyectoTesisController@show')->name('anteproyectos_tesis.show');
                Route::post('anteproyectos_tesis/aprobar/{id}', 'AnteproyectoTesisController@aprobar')->name('anteproyectos_tesis.aprobar');
                Route::post('anteproyectos_tesis/anular_aprobacion/{id}', 'AnteproyectoTesisController@anular_aprobacion')->name('anteproyectos_tesis.anular_aprobacion');
                Route::get('anteproyectos_tesis/show_entregas/{id}', 'AnteproyectoTesisController@show_entregas')->name('anteproyectos_tesis.show_entregas');
                Route::post('anteproyectos_tesis/save_entrega/{id}', 'AnteproyectoTesisController@save_entrega')->name('anteproyectos_tesis.save_entrega');
                Route::post('anteproyectos_tesis/save_correccion/{id}', 'AnteproyectoTesisController@save_correccion')->name('anteproyectos_tesis.save_correccion');
                Route::post('anteproyectos_tesis/delete_entrega/{id}', 'AnteproyectoTesisController@delete_entrega')->name('anteproyectos_tesis.delete_entrega');
            //Proyectos
                Route::get('proyectos_tesis', 'ProyectoTesisController@index')->name('proyectos_tesis.index');
                Route::get('proyectos_tesis/ver/{id}', 'ProyectoTesisController@show')->name('proyectos_tesis.show');
                Route::post('proyectos_tesis/aprobar/{id}', 'ProyectoTesisController@aprobar')->name('proyectos_tesis.aprobar');
                Route::post('proyectos_tesis/anular_aprobacion/{id}', 'ProyectoTesisController@anular_aprobacion')->name('proyectos_tesis.anular_aprobacion');
                Route::get('proyectos_tesis/show_entregas/{id}', 'ProyectoTesisController@show_entregas')->name('proyectos_tesis.show_entregas');
                Route::post('proyectos_tesis/save_entrega/{id}', 'ProyectoTesisController@save_entrega')->name('proyectos_tesis.save_entrega');
                Route::post('proyectos_tesis/save_correccion/{id}', 'ProyectoTesisController@save_correccion')->name('proyectos_tesis.save_correccion');
                Route::post('proyectos_tesis/delete_entrega/{id}', 'ProyectoTesisController@delete_entrega')->name('proyectos_tesis.delete_entrega');
            //Borradores
                Route::get('borradores_tesis', 'BorradorTesisController@index')->name('borradores_tesis.index');
                Route::get('borradores_tesis/ver/{id}', 'BorradorTesisController@show')->name('borradores_tesis.show');
                Route::post('borradores_tesis/aprobar/{id}', 'BorradorTesisController@aprobar')->name('borradores_tesis.aprobar');
                Route::post('borradores_tesis/anular_aprobacion/{id}', 'BorradorTesisController@anular_aprobacion')->name('borradores_tesis.anular_aprobacion');
                Route::post('borradores_tesis/aprobar_todos/{id}', 'BorradorTesisController@aprobar_todos')->name('borradores_tesis.aprobar_todos');
                Route::post('borradores_tesis/desaprobar_todos/{id}', 'BorradorTesisController@desaprobar_todos')->name('borradores_tesis.desaprobar_todos');
                Route::get('borradores_tesis/show_entregas/{id}', 'BorradorTesisController@show_entregas')->name('borradores_tesis.show_entregas');
                Route::post('borradores_tesis/save_entrega/{id}', 'BorradorTesisController@save_entrega')->name('borradores_tesis.save_entrega');
                Route::post('borradores_tesis/save_correccion/{id}', 'BorradorTesisController@save_correccion')->name('borradores_tesis.save_correccion');
                Route::post('borradores_tesis/delete_entrega/{id}', 'BorradorTesisController@delete_entrega')->name('borradores_tesis.delete_entrega');
            //Cargar Rubricas TFG
                Route::get('cargar_rubricas_tesis/ver_proceso/{id}', 'RubricaAlumnoTesisController@show_proceso')->name('cargar_rubricas_tesis.show_proceso');
                Route::get('cargar_rubricas_tesis/agregar_proceso/{id}', 'RubricaAlumnoTesisController@create_proceso')->name('cargar_rubricas_tesis.create_proceso');
                Route::post('cargar_rubricas_tesis/guardar_proceso/{id}', 'RubricaAlumnoTesisController@store_proceso')->name('cargar_rubricas_tesis.store_proceso');
                Route::get('cargar_rubricas_tesis/editar_proceso/{id}', 'RubricaAlumnoTesisController@edit_proceso')->name('cargar_rubricas_tesis.edit_proceso');
                Route::post('cargar_rubricas_tesis/actualizar_proceso/{id}', 'RubricaAlumnoTesisController@update_proceso')->name('cargar_rubricas_tesis.update_proceso');
                Route::get('cargar_rubricas_tesis/ver_defensa/{id}', 'RubricaAlumnoTesisController@show_defensa')->name('cargar_rubricas_tesis.show_defensa');
                Route::get('cargar_rubricas_tesis/agregar_defensa/{id}', 'RubricaAlumnoTesisController@create_defensa')->name('cargar_rubricas_tesis.create_defensa');
                Route::post('cargar_rubricas_tesis/guardar_defensa/{id}', 'RubricaAlumnoTesisController@store_defensa')->name('cargar_rubricas_tesis.store_defensa');
            //Fechas de Defensa
                Route::get('fechas_defensas_tesis', 'FechaDefensaTesisController@index')->name('fechas_defensas_tesis.index');
                Route::get('fechas_defensas_tesis/ver/{id}', 'FechaDefensaTesisController@show')->name('fechas_defensas_tesis.show');
                Route::get('fechas_defensas_tesis/agregar', 'FechaDefensaTesisController@create')->name('fechas_defensas_tesis.create');
                Route::post('fechas_defensas_tesis/agregar', 'FechaDefensaTesisController@store')->name('fechas_defensas_tesis.store');
                Route::get('fechas_defensas_tesis/editar/{id}', 'FechaDefensaTesisController@edit')->name('fechas_defensas_tesis.edit');
                Route::post('fechas_defensas_tesis/actualizar/{id}', 'FechaDefensaTesisController@update')->name('fechas_defensas_tesis.update');
                Route::get('fechas_defensas_tesis/eliminar/{id}', 'FechaDefensaTesisController@destroy')->name('fechas_defensas_tesis.get_destroy');
                Route::delete('fechas_defensas_tesis/eliminar/{id}', 'FechaDefensaTesisController@destroy')->name('fechas_defensas_tesis.destroy');
            //Tipos de T.F.G.
                Route::get('tipos_tesis', 'TipoTesisController@index')->name('tipos_tesis.index');
                Route::get('tipos_tesis/all', 'TipoTesisController@index_ajax')->name('tipos_tesis.index_ajax');
                Route::get('tipos_tesis/show/{id}', 'TipoTesisController@show')->name('tipos_tesis.show');
                Route::post('tipos_tesis', 'TipoTesisController@store')->name('tipos_tesis.store');
                Route::get('tipos_tesis/{id}', 'TipoTesisController@edit')->name('tipos_tesis.edit');
                Route::post('tipos_tesis/{id}', 'TipoTesisController@update')->name('tipos_tesis.update');
                Route::get('tipos_tesis/inactivar/{id}', 'TipoTesisController@get_unactivate')->name('tipos_tesis.get_unactivate');
                Route::post('tipos_tesis/inactivar/{id}', 'TipoTesisController@unactivate')->name('tipos_tesis.unactivate');
                Route::get('tipos_tesis/activar/{id}', 'TipoTesisController@get_activate')->name('tipos_tesis.get_activate');
                Route::post('tipos_tesis/activar/{id}', 'TipoTesisController@activate')->name('tipos_tesis.activate');
                Route::get('tipos_tesis/eliminar/{id}', 'TipoTesisController@get_destroy')->name('tipos_tesis.get_destroy');
                Route::delete('tipos_tesis/eliminar/{id}', 'TipoTesisController@destroy')->name('tipos_tesis.destroy');
            //Areas de T.F.G
                Route::get('areas_tesis', 'AreaTesisController@index')->name('areas_tesis.index');
                Route::get('areas_tesis/all', 'AreaTesisController@index_ajax')->name('areas_tesis.index_ajax');
                Route::get('areas_tesis/show/{id}', 'AreaTesisController@show')->name('areas_tesis.show');
                Route::post('areas_tesis', 'AreaTesisController@store')->name('areas_tesis.store');
                Route::get('areas_tesis/{id}', 'AreaTesisController@edit')->name('areas_tesis.edit');
                Route::post('areas_tesis/{id}', 'AreaTesisController@update')->name('areas_tesis.update');
                Route::get('areas_tesis/inactivar/{id}', 'AreaTesisController@get_unactivate')->name('areas_tesis.get_unactivate');
                Route::post('areas_tesis/inactivar/{id}', 'AreaTesisController@unactivate')->name('areas_tesis.unactivate');
                Route::get('areas_tesis/activar/{id}', 'AreaTesisController@get_activate')->name('areas_tesis.get_activate');
                Route::post('areas_tesis/activar/{id}', 'AreaTesisController@activate')->name('areas_tesis.activate');
                Route::get('areas_tesis/eliminar/{id}', 'AreaTesisController@get_destroy')->name('areas_tesis.get_destroy');
                Route::delete('areas_tesis/eliminar/{id}', 'AreaTesisController@destroy')->name('areas_tesis.destroy');
            //Lineas de T.F.G
                Route::get('lineas_tesis', 'LineaTesisController@index')->name('lineas_tesis.index');
                Route::get('lineas_tesis/all', 'LineaTesisController@index_ajax')->name('lineas_tesis.index_ajax');
                Route::get('lineas_tesis/show/{id}', 'LineaTesisController@show')->name('lineas_tesis.show');
                Route::post('lineas_tesis', 'LineaTesisController@store')->name('lineas_tesis.store');
                Route::get('lineas_tesis/{id}', 'LineaTesisController@edit')->name('lineas_tesis.edit');
                Route::post('lineas_tesis/{id}', 'LineaTesisController@update')->name('lineas_tesis.update');
                Route::get('lineas_tesis/inactivar/{id}', 'LineaTesisController@get_unactivate')->name('lineas_tesis.get_unactivate');
                Route::post('lineas_tesis/inactivar/{id}', 'LineaTesisController@unactivate')->name('lineas_tesis.unactivate');
                Route::get('lineas_tesis/activar/{id}', 'LineaTesisController@get_activate')->name('lineas_tesis.get_activate');
                Route::post('lineas_tesis/activar/{id}', 'LineaTesisController@activate')->name('lineas_tesis.activate');
                Route::get('lineas_tesis/eliminar/{id}', 'LineaTesisController@get_destroy')->name('lineas_tesis.get_destroy');
                Route::delete('lineas_tesis/eliminar/{id}', 'LineaTesisController@destroy')->name('lineas_tesis.destroy');
            //Requerimientos de Entregas de T.F.G
                Route::get('requerimientos_entregas_tesis', 'RequerimientoEntregaTesisController@show')->name('requerimientos_entregas_tesis.show');
                Route::get('requerimientos_entregas_tesis/agregar', 'RequerimientoEntregaTesisController@create')->name('requerimientos_entregas_tesis.create');
                Route::post('requerimientos_entregas_tesis/agregar', 'RequerimientoEntregaTesisController@store')->name('requerimientos_entregas_tesis.store');
                Route::get('requerimientos_entregas_tesis/editar/{id}', 'RequerimientoEntregaTesisController@edit')->name('requerimientos_entregas_tesis.edit');
                Route::post('requerimientos_entregas_tesis/actualizar/{id}', 'RequerimientoEntregaTesisController@update')->name('requerimientos_entregas_tesis.update');
            //Bloques Anteproyectos
                Route::get('bloques_anteproyectos_tesis', 'BloqueAnteproyectoTesisController@index')->name('bloques_anteproyectos_tesis.index');
                Route::get('bloques_anteproyectos_tesis/all', 'BloqueAnteproyectoTesisController@index_ajax')->name('bloques_anteproyectos_tesis.index_ajax');
                Route::get('bloques_anteproyectos_tesis/show/{id}', 'BloqueAnteproyectoTesisController@show')->name('bloques_anteproyectos_tesis.show');
                Route::post('bloques_anteproyectos_tesis', 'BloqueAnteproyectoTesisController@store')->name('bloques_anteproyectos_tesis.store');
                Route::get('bloques_anteproyectos_tesis/{id}', 'BloqueAnteproyectoTesisController@edit')->name('bloques_anteproyectos_tesis.edit');
                Route::post('bloques_anteproyectos_tesis/{id}', 'BloqueAnteproyectoTesisController@update')->name('bloques_anteproyectos_tesis.update');
                Route::get('bloques_anteproyectos_tesis/inactivar/{id}', 'BloqueAnteproyectoTesisController@get_unactivate')->name('bloques_anteproyectos_tesis.get_unactivate');
                Route::post('bloques_anteproyectos_tesis/inactivar/{id}', 'BloqueAnteproyectoTesisController@unactivate')->name('bloques_anteproyectos_tesis.unactivate');
                Route::get('bloques_anteproyectos_tesis/activar/{id}', 'BloqueAnteproyectoTesisController@get_activate')->name('bloques_anteproyectos_tesis.get_activate');
                Route::post('bloques_anteproyectos_tesis/activar/{id}', 'BloqueAnteproyectoTesisController@activate')->name('bloques_anteproyectos_tesis.activate');
                Route::get('bloques_anteproyectos_tesis/eliminar/{id}', 'BloqueAnteproyectoTesisController@get_destroy')->name('bloques_anteproyectos_tesis.get_destroy');
                Route::delete('bloques_anteproyectos_tesis/eliminar/{id}', 'BloqueAnteproyectoTesisController@destroy')->name('bloques_anteproyectos_tesis.destroy');
            //Bloques Proyectos
                Route::get('bloques_proyectos_tesis', 'BloqueProyectoTesisController@index')->name('bloques_proyectos_tesis.index');
                Route::get('bloques_proyectos_tesis/all', 'BloqueProyectoTesisController@index_ajax')->name('bloques_proyectos_tesis.index_ajax');
                Route::get('bloques_proyectos_tesis/show/{id}', 'BloqueProyectoTesisController@show')->name('bloques_proyectos_tesis.show');
                Route::post('bloques_proyectos_tesis', 'BloqueProyectoTesisController@store')->name('bloques_proyectos_tesis.store');
                Route::get('bloques_proyectos_tesis/{id}', 'BloqueProyectoTesisController@edit')->name('bloques_proyectos_tesis.edit');
                Route::post('bloques_proyectos_tesis/{id}', 'BloqueProyectoTesisController@update')->name('bloques_proyectos_tesis.update');
                Route::get('bloques_proyectos_tesis/inactivar/{id}', 'BloqueProyectoTesisController@get_unactivate')->name('bloques_proyectos_tesis.get_unactivate');
                Route::post('bloques_proyectos_tesis/inactivar/{id}', 'BloqueProyectoTesisController@unactivate')->name('bloques_proyectos_tesis.unactivate');
                Route::get('bloques_proyectos_tesis/activar/{id}', 'BloqueProyectoTesisController@get_activate')->name('bloques_proyectos_tesis.get_activate');
                Route::post('bloques_proyectos_tesis/activar/{id}', 'BloqueProyectoTesisController@activate')->name('bloques_proyectos_tesis.activate');
                Route::get('bloques_proyectos_tesis/eliminar/{id}', 'BloqueProyectoTesisController@get_destroy')->name('bloques_proyectos_tesis.get_destroy');
                Route::delete('bloques_proyectos_tesis/eliminar/{id}', 'BloqueProyectoTesisController@destroy')->name('bloques_proyectos_tesis.destroy');
            //Bloques Borradores
                Route::get('bloques_borradores_tesis', 'BloqueBorradorTesisController@index')->name('bloques_borradores_tesis.index');
                Route::get('bloques_borradores_tesis/all', 'BloqueBorradorTesisController@index_ajax')->name('bloques_borradores_tesis.index_ajax');
                Route::get('bloques_borradores_tesis/show/{id}', 'BloqueBorradorTesisController@show')->name('bloques_borradores_tesis.show');
                Route::post('bloques_borradores_tesis', 'BloqueBorradorTesisController@store')->name('bloques_borradores_tesis.store');
                Route::get('bloques_borradores_tesis/{id}', 'BloqueBorradorTesisController@edit')->name('bloques_borradores_tesis.edit');
                Route::post('bloques_borradores_tesis/{id}', 'BloqueBorradorTesisController@update')->name('bloques_borradores_tesis.update');
                Route::get('bloques_borradores_tesis/inactivar/{id}', 'BloqueBorradorTesisController@get_unactivate')->name('bloques_borradores_tesis.get_unactivate');
                Route::post('bloques_borradores_tesis/inactivar/{id}', 'BloqueBorradorTesisController@unactivate')->name('bloques_borradores_tesis.unactivate');
                Route::get('bloques_borradores_tesis/activar/{id}', 'BloqueBorradorTesisController@get_activate')->name('bloques_borradores_tesis.get_activate');
                Route::post('bloques_borradores_tesis/activar/{id}', 'BloqueBorradorTesisController@activate')->name('bloques_borradores_tesis.activate');
                Route::get('bloques_borradores_tesis/eliminar/{id}', 'BloqueBorradorTesisController@get_destroy')->name('bloques_borradores_tesis.get_destroy');
                Route::delete('bloques_borradores_tesis/eliminar/{id}', 'BloqueBorradorTesisController@destroy')->name('bloques_borradores_tesis.destroy');
            //Rubricas de TFG
                Route::get('rubricas_tesis', 'RubricaTesisController@index')->name('rubricas_tesis.index');
                Route::get('rubricas_tesis/ver/{id}', 'RubricaTesisController@show')->name('rubricas_tesis.show');
                Route::get('rubricas_tesis/agregar', 'RubricaTesisController@create')->name('rubricas_tesis.create');
                Route::post('rubricas_tesis/agregar', 'RubricaTesisController@store')->name('rubricas_tesis.store');
                Route::get('rubricas_tesis/editar/{id}', 'RubricaTesisController@edit')->name('rubricas_tesis.edit');
                Route::post('rubricas_tesis/actualizar/{id}', 'RubricaTesisController@update')->name('rubricas_tesis.update');
                Route::post('rubricas_tesis/inactivar/{id}', 'RubricaTesisController@unactivate')->name('rubricas_tesis.unactivate');
                Route::post('rubricas_tesis/activar/{id}', 'RubricaTesisController@activate')->name('rubricas_tesis.activate');
                Route::get('rubricas_tesis/eliminar/{id}', 'RubricaTesisController@destroy')->name('rubricas_tesis.get_destroy');
                Route::delete('rubricas_tesis/eliminar/{id}', 'RubricaTesisController@destroy')->name('rubricas_tesis.destroy');
        //Tutorias
            //Lista de Tutorias
                Route::get('tutorias', 'TutoriaController@index')->name('tutorias.index');
                Route::get('tutorias/ver/{id}', 'TutoriaController@show')->name('tutorias.show');
                Route::get('tutorias/agregar', 'TutoriaController@create')->name('tutorias.create');
                Route::post('tutorias/agregar', 'TutoriaController@store')->name('tutorias.store');
                Route::get('tutorias/editar/{id}', 'TutoriaController@edit')->name('tutorias.edit');
                Route::post('tutorias/actualizar/{id}', 'TutoriaController@update')->name('tutorias.update');
                Route::get('tutorias/editar_alumnos/{id}', 'TutoriaController@edit_alumnos')->name('tutorias.edit_alumnos');
                Route::post('tutorias/actualizar_alumnos/{id}', 'TutoriaController@update_alumnos')->name('tutorias.update_alumnos');
                Route::get('tutorias/editar_horario/{id}', 'TutoriaController@edit_horario')->name('tutorias.edit_horario');
                Route::post('tutorias/actualizar_horario/{id}', 'TutoriaController@update_horario')->name('tutorias.update_horario');
                Route::get('tutorias/get_carrera/{id}', 'TutoriaController@get_carrera')->name('tutorias.get_carrera');
                Route::get('tutorias/eliminar/{id}', 'TutoriaController@destroy')->name('tutorias.get_destroy');
                Route::delete('tutorias/eliminar/{id}', 'TutoriaController@destroy')->name('tutorias.destroy');
                //Tutorias - Clases
                    Route::get('tutorias_clases/{id}', 'TutoriaClaseController@index')->name('tutorias_clases.index');
                    Route::get('tutorias_clases/ver/{id}', 'TutoriaClaseController@show')->name('tutorias_clases.show');
                    Route::get('tutorias_clases/agregar/{id}', 'TutoriaClaseController@create')->name('tutorias_clases.create');
                    Route::post('tutorias_clases/agregar/{id}', 'TutoriaClaseController@store')->name('tutorias_clases.store');
                    Route::get('tutorias_clases/eliminar/{id}', 'TutoriaClaseController@destroy')->name('tutorias_clases.get_destroy');
                    Route::delete('tutorias_clases/eliminar/{id}', 'TutoriaClaseController@destroy')->name('tutorias_clases.destroy');
                    Route::get('tutorias_clases/editar/{id}', 'TutoriaClaseController@edit')->name('tutorias_clases.edit');
                    Route::post('tutorias_clases/actualizar/{id}', 'TutoriaClaseController@update')->name('tutorias_clases.update');
                    Route::get('tutorias_clases/actualizar_observacion/{id}', 'TutoriaClaseController@update_observacion')->name('tutorias_clases.update_observacion');
                    Route::post('tutorias_clases/actualizar_observacion/{id}', 'TutoriaClaseController@update_observacion');
                //Tutorias - Evaluaciones
                    Route::get('tutorias_evaluaciones/ver/{id}', 'TutoriaEvaluacionController@show')->name('tutorias_evaluaciones.show');
                    Route::get('tutorias_evaluaciones/agregar/{id}', 'TutoriaEvaluacionController@create')->name('tutorias_evaluaciones.create');
                    Route::post('tutorias_evaluaciones/guardar/{id}', 'TutoriaEvaluacionController@store')->name('tutorias_evaluaciones.store');
                    Route::get('tutorias_evaluaciones/ver_acta/{id}', 'TutoriaEvaluacionController@show_acta')->name('tutorias_evaluaciones.show_acta');
                    Route::get('tutorias_evaluaciones/generar_acta/{id}', 'TutoriaEvaluacionController@generate_acta')->name('tutorias_evaluaciones.generate_acta');
                    Route::post('tutorias_evaluaciones/subir_acta/{id}', 'TutoriaEvaluacionController@subir_acta')->name('tutorias_evaluaciones.subir_acta');
                    Route::get('tutorias_evaluaciones/eliminar_acta/{id}', 'TutoriaEvaluacionController@eliminar_acta')->name('tutorias_evaluaciones.eliminar_acta');
                    Route::delete('tutorias_evaluaciones/eliminar_acta/{id}', 'TutoriaEvaluacionController@eliminar_acta');
            //Precios
                Route::get('tutorias_precios', 'TutoriaPrecioController@index')->name('tutorias_precios.index');
                Route::get('tutorias_precios/all', 'TutoriaPrecioController@index_ajax')->name('tutorias_precios.index_ajax');
                Route::get('tutorias_precios/show/{id}', 'TutoriaPrecioController@show')->name('tutorias_precios.show');
                Route::post('tutorias_precios', 'TutoriaPrecioController@store')->name('tutorias_precios.store');
                Route::get('tutorias_precios/{id}', 'TutoriaPrecioController@edit')->name('tutorias_precios.edit');
                Route::post('tutorias_precios/{id}', 'TutoriaPrecioController@update')->name('tutorias_precios.update');
                Route::get('tutorias_precios/inactivar/{id}', 'TutoriaPrecioController@get_unactivate')->name('tutorias_precios.get_unactivate');
                Route::post('tutorias_precios/inactivar/{id}', 'TutoriaPrecioController@unactivate')->name('tutorias_precios.unactivate');
                Route::get('tutorias_precios/activar/{id}', 'TutoriaPrecioController@get_activate')->name('tutorias_precios.get_activate');
                Route::post('tutorias_precios/activar/{id}', 'TutoriaPrecioController@activate')->name('tutorias_precios.activate');
                Route::get('tutorias_precios/eliminar/{id}', 'TutoriaPrecioController@get_destroy')->name('tutorias_precios.get_destroy');
                Route::delete('tutorias_precios/eliminar/{id}', 'TutoriaPrecioController@destroy')->name('tutorias_precios.destroy');
                Route::get('tutorias_precios/get_precio/{id}', 'TutoriaPrecioController@get_precio')->name('tutorias_precios.get_precio');
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
        //Solicitudes de Alumnos
            Route::get('solicitudes', 'SolicitudController@index')->name('solicitudes.index');
            Route::get('solicitudes/ver/{id}', 'SolicitudController@show')->name('solicitudes.show');
            Route::post('solicitudes/guardar', 'SolicitudController@store')->name('solicitudes.store');
            Route::get('solicitudes/aprobar/{id}', 'SolicitudController@approve')->name('solicitudes.approve');
            Route::post('solicitudes/aprobar/{id}', 'SolicitudController@approve');
            Route::get('solicitudes/para_entrega/{id}', 'SolicitudController@para_entrega')->name('solicitudes.para_entrega');
            Route::post('solicitudes/para_entrega/{id}', 'SolicitudController@para_entrega');
            Route::get('solicitudes/rechazar/{id}', 'SolicitudController@reject')->name('solicitudes.reject');
            Route::post('solicitudes/rechazar/{id}', 'SolicitudController@reject');
            Route::get('solicitudes/anular_rechazo/{id}', 'SolicitudController@unreject')->name('solicitudes.unreject');
            Route::post('solicitudes/anular_rechazo/{id}', 'SolicitudController@unreject');
            Route::get('solicitudes/desaprobar/{id}', 'SolicitudController@unapprove')->name('solicitudes.unapprove');
            Route::post('solicitudes/desaprobar/{id}', 'SolicitudController@unapprove');
            Route::get('solicitudes/entregar/{id}', 'SolicitudController@deliver')->name('solicitudes.deliver');
            Route::post('solicitudes/entregar/{id}', 'SolicitudController@deliver');
            Route::get('solicitudes/anular_entrega/{id}', 'SolicitudController@undeliver')->name('solicitudes.undeliver');
            Route::post('solicitudes/anular_entrega/{id}', 'SolicitudController@undeliver');
            Route::get('solicitudes/eliminar/{id}', 'SolicitudController@destroy')->name('solicitudes.get_destroy');
            Route::delete('solicitudes/eliminar/{id}', 'SolicitudController@destroy')->name('solicitudes.destroy');
        //Encuestas
            Route::get('encuestas', 'EncuestaController@index')->name('encuestas.index');
            Route::get('encuestas/all', 'EncuestaController@index_ajax')->name('encuestas.index_ajax');
            Route::get('encuestas/show/{id}', 'EncuestaController@show')->name('encuestas.show');
            Route::post('encuestas', 'EncuestaController@store')->name('encuestas.store');
            Route::get('encuestas/{id}', 'EncuestaController@edit')->name('encuestas.edit');
            Route::post('encuestas/{id}', 'EncuestaController@update')->name('encuestas.update');
            Route::get('encuestas/inactivar/{id}', 'EncuestaController@get_unactivate')->name('encuestas.get_unactivate');
            Route::post('encuestas/inactivar/{id}', 'EncuestaController@unactivate')->name('encuestas.unactivate');
            Route::get('encuestas/activar/{id}', 'EncuestaController@get_activate')->name('encuestas.get_activate');
            Route::post('encuestas/activar/{id}', 'EncuestaController@activate')->name('encuestas.activate');
            Route::get('encuestas/eliminar/{id}', 'EncuestaController@get_destroy')->name('encuestas.get_destroy');
            Route::delete('encuestas/eliminar/{id}', 'EncuestaController@destroy')->name('encuestas.destroy');
    //Reportes
        //Año Ingreso Alumnos
            Route::get('reportes_academicos/anho_ingreso_alumnos', 'RptAnoIngresoAlumnoController@create')->name('reportes_academicos.create_alumnos_ingresos');
            Route::get('reportes_academicos/anho_ingreso_alumnos_pdf', 'RptAnoIngresoAlumnoController@pdf')->name('reportes_academicos.pdf_alumnos_ingresos');
        //Salas de Clases
            Route::get('reportes_academicos/salas_clases', 'RptSalaClaseController@create')->name('reportes_academicos.create_salas_clases');
            Route::get('reportes_academicos/salas_clases_pdf', 'RptSalaClaseController@pdf')->name('reportes_academicos.pdf_salas_clases');
        //Salas de Clases
            Route::get('reportes_academicos/fechas_examenes', 'RptFechaExamenController@create')->name('reportes_academicos.create_fechas_examenes');
            Route::get('reportes_academicos/fechas_examenes_pdf', 'RptFechaExamenController@pdf')->name('reportes_academicos.pdf_fechas_examenes');
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
            Route::get('facultades/inactivar/{id}', 'FacultadController@get_unactivate')->name('facultades.get_unactivate');
            Route::post('facultades/inactivar/{id}', 'FacultadController@unactivate')->name('facultades.unactivate');
            Route::get('facultades/activar/{id}', 'FacultadController@get_activate')->name('facultades.get_activate');
            Route::post('facultades/activar/{id}', 'FacultadController@activate')->name('facultades.activate');
            Route::get('facultades/eliminar/{id}', 'FacultadController@get_destroy')->name('facultades.get_destroy');
            Route::delete('facultades/eliminar/{id}', 'FacultadController@destroy')->name('facultades.destroy');
        //Carreras
            Route::get('carreras', 'CarreraController@index')->name('carreras.index');
            Route::get('carreras/ver/{id}', 'CarreraController@show')->name('carreras.show');
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
        //Materias - Suficiencia
            Route::get('materias_suficiencia', 'MateriaSuficienciaController@index')->name('materias_suficiencias.index');
            Route::get('materias_suficiencia/all', 'MateriaSuficienciaController@index_ajax')->name('materias_suficiencias.index_ajax');
            Route::get('materias_suficiencia/show/{id}', 'MateriaSuficienciaController@show')->name('materias_suficiencias.show');
            Route::post('materias_suficiencia', 'MateriaSuficienciaController@store')->name('materias_suficiencias.store');
            Route::get('materias_suficiencia/{id}', 'MateriaSuficienciaController@edit')->name('materias_suficiencias.edit');
            Route::post('materias_suficiencia/{id}', 'MateriaSuficienciaController@update')->name('materias_suficiencias.update');
            Route::get('materias_suficiencia/inactivar/{id}', 'MateriaSuficienciaController@get_unactivate')->name('materias_suficiencias.get_unactivate');
            Route::post('materias_suficiencia/inactivar/{id}', 'MateriaSuficienciaController@unactivate')->name('materias_suficiencias.unactivate');
            Route::get('materias_suficiencia/activar/{id}', 'MateriaSuficienciaController@get_activate')->name('materias_suficiencias.get_activate');
            Route::post('materias_suficiencia/activar/{id}', 'MateriaSuficienciaController@activate')->name('materias_suficiencias.activate');
            Route::get('materias_suficiencia/eliminar/{id}', 'MateriaSuficienciaController@get_destroy')->name('materias_suficiencias.get_destroy');
            Route::delete('materias_suficiencia/eliminar/{id}', 'MateriaSuficienciaController@destroy')->name('materias_suficiencias.destroy');
        //Correlatividades
            Route::get('correlatividades/ver/{id}', 'CorrelatividadController@show')->name('correlatividades.show');
            Route::get('correlatividades/editar/{id}', 'CorrelatividadController@edit')->name('correlatividades.edit');
            Route::post('correlatividades/actualizar/{id}', 'CorrelatividadController@update')->name('correlatividades.update');
            Route::get('correlatividades/eliminar/{id}', 'CorrelatividadController@destroy')->name('correlatividades.get_destroy');
            Route::delete('correlatividades/eliminar/{id}', 'CorrelatividadController@destroy')->name('correlatividades.destroy');
        //Semestres
            Route::get('semestres', 'SemestreController@index')->name('semestres.index');
            Route::get('semestres/ver/{id}', 'SemestreController@show')->name('semestres.show');
            Route::get('semestres/agregar', 'SemestreController@create')->name('semestres.create');
            Route::post('semestres/agregar', 'SemestreController@store')->name('semestres.store');
            Route::post('semestres/inactivar/{id}', 'SemestreController@unactivate')->name('semestres.unactivate');
            Route::post('semestres/activar/{id}', 'SemestreController@activate')->name('semestres.activate');
            Route::get('semestres/eliminar/{id}', 'SemestreController@destroy')->name('semestres.get_destroy');
            Route::delete('semestres/eliminar/{id}', 'SemestreController@destroy')->name('semestres.destroy');
            //SemestresMallas
                Route::get('semestres_carreras/{id}', 'SemestreMallaController@update')->name('semestres_mallas.edit');
                Route::post('semestres_carreras/{id}', 'SemestreMallaController@update')->name('semestres_mallas.update');
            //SemestresMallasMaterias
                Route::get('semestres_carreras_materias/inactivar/{id}', 'SemestreMallaMateriaController@unactivate')->name('semestres_mallas_materias.unactivate');
                Route::post('semestres_carreras_materias/inactivar/{id}', 'SemestreMallaMateriaController@unactivate');
                Route::get('semestres_carreras_materias/activar/{id}', 'SemestreMallaMateriaController@activate')->name('semestres_mallas_materias.activate');
                Route::post('semestres_carreras_materias/activar/{id}', 'SemestreMallaMateriaController@activate');
                Route::get('semestres_carreras_materias/{id}', 'SemestreMallaMateriaController@show')->name('semestres_mallas_materias.show');
                Route::post('semestres_carreras_materias/{id}', 'SemestreMallaMateriaController@update')->name('semestres_mallas_materias.update');
            //SemestresMallasMaterias
                Route::get('semestres_carreras_materias_horarios/editar/{id}', 'SemestreMallaMateriaHorarioController@edit')->name('semestres_mallas_materias_horarios.edit');
                Route::post('semestres_carreras_materias_horarios/actualizar/{id}', 'SemestreMallaMateriaHorarioController@update')->name('semestres_mallas_materias_horarios.update');
        //Mallas
            Route::get('mallas', 'MallaController@index')->name('mallas.index');
            Route::get('mallas/ver/{id}', 'MallaController@show')->name('mallas.show');
            Route::get('mallas/agregar', 'MallaController@create')->name('mallas.create');
            Route::post('mallas/agregar', 'MallaController@store')->name('mallas.store');
            Route::get('mallas/editar/{id}', 'MallaController@edit')->name('mallas.edit');
            Route::post('mallas/actualizar/{id}', 'MallaController@update')->name('mallas.update');
            Route::post('mallas/inactivar/{id}', 'MallaController@unactivate')->name('mallas.unactivate');
            Route::post('mallas/activar/{id}', 'MallaController@activate')->name('mallas.activate');
            Route::get('mallas/eliminar/{id}', 'MallaController@destroy')->name('mallas.get_destroy');
            Route::delete('mallas/eliminar/{id}', 'MallaController@destroy')->name('mallas.destroy');
            Route::get('mallas/imprimir/{id}', 'MallaController@pdf')->name('mallas.pdf');
        //Mallas Espejo
            Route::get('mallas_espejos', 'MallaEspejoController@index')->name('mallas_espejos.index');
            Route::get('mallas_espejos/ver/{id}', 'MallaEspejoController@show')->name('mallas_espejos.show');
            Route::get('mallas_espejos/agregar', 'MallaEspejoController@create')->name('mallas_espejos.create');
            Route::post('mallas_espejos/agregar', 'MallaEspejoController@store')->name('mallas_espejos.store');
            Route::get('mallas_espejos/editar/{id}', 'MallaEspejoController@edit')->name('mallas_espejos.edit');
            Route::post('mallas_espejos/actualizar/{id}', 'MallaEspejoController@update')->name('mallas_espejos.update');
            Route::post('mallas_espejos/inactivar/{id}', 'MallaEspejoController@unactivate')->name('mallas_espejos.unactivate');
            Route::post('mallas_espejos/activar/{id}', 'MallaEspejoController@activate')->name('mallas_espejos.activate');
            Route::get('mallas_espejos/eliminar/{id}', 'MallaEspejoController@destroy')->name('mallas_espejos.get_destroy');
            Route::delete('mallas_espejos/eliminar/{id}', 'MallaEspejoController@destroy')->name('mallas_espejos.destroy');
            Route::get('mallas_espejos/get_materias/{malla}', 'MallaEspejoController@get_materias')->name('mallas_espejos.get_materias');
        //Escalas
            Route::get('escalas', 'EscalaController@index')->name('escalas.index');
            Route::get('escalas/ver/{id}', 'EscalaController@show')->name('escalas.show');
            Route::get('escalas/agregar', 'EscalaController@create')->name('escalas.create');
            Route::post('escalas/agregar', 'EscalaController@store')->name('escalas.store');
            Route::get('escalas/editar/{id}', 'EscalaController@edit')->name('escalas.edit');
            Route::post('escalas/actualizar/{id}', 'EscalaController@update')->name('escalas.update');
            Route::post('escalas/inactivar/{id}', 'EscalaController@unactivate')->name('escalas.unactivate');
            Route::post('escalas/activar/{id}', 'EscalaController@activate')->name('escalas.activate');
            Route::get('escalas/eliminar/{id}', 'EscalaController@destroy')->name('escalas.get_destroy');
            Route::delete('escalas/eliminar/{id}', 'EscalaController@destroy')->name('escalas.destroy');
        //Evaluaciones
            Route::get('evaluaciones', 'EvaluacionController@index')->name('evaluaciones.index');
            Route::get('evaluaciones/ver/{id}', 'EvaluacionController@show')->name('evaluaciones.show');
            Route::get('evaluaciones/agregar', 'EvaluacionController@create')->name('evaluaciones.create');
            Route::post('evaluaciones/agregar', 'EvaluacionController@store')->name('evaluaciones.store');
            Route::get('evaluaciones/editar/{id}', 'EvaluacionController@edit')->name('evaluaciones.edit');
            Route::post('evaluaciones/actualizar/{id}', 'EvaluacionController@update')->name('evaluaciones.update');
            Route::post('evaluaciones/inactivar/{id}', 'EvaluacionController@unactivate')->name('evaluaciones.unactivate');
            Route::post('evaluaciones/activar/{id}', 'EvaluacionController@activate')->name('evaluaciones.activate');
            Route::get('evaluaciones/eliminar/{id}', 'EvaluacionController@destroy')->name('evaluaciones.get_destroy');
            Route::delete('evaluaciones/eliminar/{id}', 'EvaluacionController@destroy')->name('evaluaciones.destroy');
        //Tipos de Evaluaciones
            Route::get('tipos_evaluaciones', 'TipoEvaluacionController@index')->name('tipos_evaluaciones.index');
            Route::get('tipos_evaluaciones/all', 'TipoEvaluacionController@index_ajax')->name('tipos_evaluaciones.index_ajax');
            Route::get('tipos_evaluaciones/show/{id}', 'TipoEvaluacionController@show')->name('tipos_evaluaciones.show');
            Route::post('tipos_evaluaciones', 'TipoEvaluacionController@store')->name('tipos_evaluaciones.store');
            Route::get('tipos_evaluaciones/{id}', 'TipoEvaluacionController@edit')->name('tipos_evaluaciones.edit');
            Route::post('tipos_evaluaciones{id}', 'TipoEvaluacionController@update')->name('tipos_evaluaciones.update');
            Route::get('tipos_evaluaciones/inactivar/{id}', 'TipoEvaluacionController@get_unactivate')->name('tipos_evaluaciones.get_unactivate');
            Route::post('tipos_evaluaciones/inactivar/{id}', 'TipoEvaluacionController@unactivate')->name('tipos_evaluaciones.unactivate');
            Route::get('tipos_evaluaciones/activar/{id}', 'TipoEvaluacionController@get_activate')->name('tipos_evaluaciones.get_activate');
            Route::post('tipos_evaluaciones/activar/{id}', 'TipoEvaluacionController@activate')->name('tipos_evaluaciones.activate');
            Route::get('tipos_evaluaciones/eliminar/{id}', 'TipoEvaluacionController@get_destroy')->name('tipos_evaluaciones.get_destroy');
            Route::delete('tipos_evaluaciones/eliminar/{id}', 'TipoEvaluacionController@destroy')->name('tipos_evaluaciones.destroy');
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
            Route::get('tipos_extensiones_universitarias/ver/{id}', 'TipoExtensionUniversitariaController@show')->name('tipos_extensiones_universitarias.show');
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
        //Tipos de Solicitudes
            Route::get('tipos_solicitudes', 'TipoSolicitudController@index')->name('tipos_solicitudes.index');
            Route::get('tipos_solicitudes/all', 'TipoSolicitudController@index_ajax')->name('tipos_solicitudes.index_ajax');
            Route::get('tipos_solicitudes/show/{id}', 'TipoSolicitudController@show')->name('tipos_solicitudes.show');
            Route::post('tipos_solicitudes', 'TipoSolicitudController@store')->name('tipos_solicitudes.store');
            Route::get('tipos_solicitudes/{id}', 'TipoSolicitudController@edit')->name('tipos_solicitudes.edit');
            Route::post('tipos_solicitudes/{id}', 'TipoSolicitudController@update')->name('tipos_solicitudes.update');
            Route::get('tipos_solicitudes/inactivar/{id}', 'TipoSolicitudController@get_unactivate')->name('tipos_solicitudes.get_unactivate');
            Route::post('tipos_solicitudes/inactivar/{id}', 'TipoSolicitudController@unactivate')->name('tipos_solicitudes.unactivate');
            Route::get('tipos_solicitudes/activar/{id}', 'TipoSolicitudController@get_activate')->name('tipos_solicitudes.get_activate');
            Route::post('tipos_solicitudes/activar/{id}', 'TipoSolicitudController@activate')->name('tipos_solicitudes.activate');
            Route::get('tipos_solicitudes/eliminar/{id}', 'TipoSolicitudController@get_destroy')->name('tipos_solicitudes.get_destroy');
            Route::delete('tipos_solicitudes/eliminar/{id}', 'TipoSolicitudController@destroy')->name('tipos_solicitudes.destroy');
        //Fechas de Desmatriulacion
            Route::get('fechas_desmatriculaciones', 'FechaDesmatriculacionController@index')->name('fechas_desmatriculaciones.index');
            Route::get('fechas_desmatriculaciones/all', 'FechaDesmatriculacionController@index_ajax')->name('fechas_desmatriculaciones.index_ajax');
            Route::get('fechas_desmatriculaciones/show/{id}', 'FechaDesmatriculacionController@show')->name('fechas_desmatriculaciones.show');
            Route::post('fechas_desmatriculaciones', 'FechaDesmatriculacionController@store')->name('fechas_desmatriculaciones.store');
            Route::get('fechas_desmatriculaciones/{id}', 'FechaDesmatriculacionController@edit')->name('fechas_desmatriculaciones.edit');
            Route::post('fechas_desmatriculaciones/{id}', 'FechaDesmatriculacionController@update')->name('fechas_desmatriculaciones.update');
            Route::get('fechas_desmatriculaciones/inactivar/{id}', 'FechaDesmatriculacionController@get_unactivate')->name('fechas_desmatriculaciones.get_unactivate');
            Route::post('fechas_desmatriculaciones/inactivar/{id}', 'FechaDesmatriculacionController@unactivate')->name('fechas_desmatriculaciones.unactivate');
            Route::get('fechas_desmatriculaciones/activar/{id}', 'FechaDesmatriculacionController@get_activate')->name('fechas_desmatriculaciones.get_activate');
            Route::post('fechas_desmatriculaciones/activar/{id}', 'FechaDesmatriculacionController@activate')->name('fechas_desmatriculaciones.activate');
            Route::get('fechas_desmatriculaciones/eliminar/{id}', 'FechaDesmatriculacionController@get_destroy')->name('fechas_desmatriculaciones.get_destroy');
            Route::delete('fechas_desmatriculaciones/eliminar/{id}', 'FechaDesmatriculacionController@destroy')->name('fechas_desmatriculaciones.destroy');
        //Fechas de Solicitudes de Examenes de Suficiencia
            //Fechas de Desmatriulacion
                Route::get('examenes_suficiencias_fechas_solicitudes', 'ExamenSuficienciaFechaSolicitudController@index')->name('examenes_suficiencias_fechas_solicitudes.index');
                Route::get('examenes_suficiencias_fechas_solicitudes/all', 'ExamenSuficienciaFechaSolicitudController@index_ajax')->name('examenes_suficiencias_fechas_solicitudes.index_ajax');
                Route::get('examenes_suficiencias_fechas_solicitudes/show/{id}', 'ExamenSuficienciaFechaSolicitudController@show')->name('examenes_suficiencias_fechas_solicitudes.show');
                Route::post('examenes_suficiencias_fechas_solicitudes', 'ExamenSuficienciaFechaSolicitudController@store')->name('examenes_suficiencias_fechas_solicitudes.store');
                Route::get('examenes_suficiencias_fechas_solicitudes/{id}', 'ExamenSuficienciaFechaSolicitudController@edit')->name('examenes_suficiencias_fechas_solicitudes.edit');
                Route::post('examenes_suficiencias_fechas_solicitudes/{id}', 'ExamenSuficienciaFechaSolicitudController@update')->name('examenes_suficiencias_fechas_solicitudes.update');
                Route::get('examenes_suficiencias_fechas_solicitudes/inactivar/{id}', 'ExamenSuficienciaFechaSolicitudController@get_unactivate')->name('examenes_suficiencias_fechas_solicitudes.get_unactivate');
                Route::post('examenes_suficiencias_fechas_solicitudes/inactivar/{id}', 'ExamenSuficienciaFechaSolicitudController@unactivate')->name('examenes_suficiencias_fechas_solicitudes.unactivate');
                Route::get('examenes_suficiencias_fechas_solicitudes/activar/{id}', 'ExamenSuficienciaFechaSolicitudController@get_activate')->name('examenes_suficiencias_fechas_solicitudes.get_activate');
                Route::post('examenes_suficiencias_fechas_solicitudes/activar/{id}', 'ExamenSuficienciaFechaSolicitudController@activate')->name('examenes_suficiencias_fechas_solicitudes.activate');
                Route::get('examenes_suficiencias_fechas_solicitudes/eliminar/{id}', 'ExamenSuficienciaFechaSolicitudController@get_destroy')->name('examenes_suficiencias_fechas_solicitudes.get_destroy');
                Route::delete('examenes_suficiencias_fechas_solicitudes/eliminar/{id}', 'ExamenSuficienciaFechaSolicitudController@destroy')->name('examenes_suficiencias_fechas_solicitudes.destroy');
        //Areas de Conocimiento
            Route::get('areas_conocimientos', 'AreaConocimientoController@index')->name('areas_conocimientos.index');
            Route::get('areas_conocimientos/all', 'AreaConocimientoController@index_ajax')->name('areas_conocimientos.index_ajax');
            Route::get('areas_conocimientos/show/{id}', 'AreaConocimientoController@show')->name('areas_conocimientos.show');
            Route::post('areas_conocimientos', 'AreaConocimientoController@store')->name('areas_conocimientos.store');
            Route::get('areas_conocimientos/{id}', 'AreaConocimientoController@edit')->name('areas_conocimientos.edit');
            Route::post('areas_conocimientos/{id}', 'AreaConocimientoController@update')->name('areas_conocimientos.update');
            Route::get('areas_conocimientos/eliminar/{id}', 'AreaConocimientoController@get_destroy')->name('areas_conocimientos.get_destroy');
            Route::delete('areas_conocimientos/eliminar/{id}', 'AreaConocimientoController@destroy')->name('areas_conocimientos.destroy');

//Escuela de Negocios
    //Alumnos
        Route::get('alumnos_ubs', 'AlumnoUbsController@index')->name('alumnos_ubs.index');
        Route::post('alumnos_ubs', 'AlumnoUbsController@index');
        Route::get('alumnos_ubs/ver/{id}', 'AlumnoUbsController@show')->name('alumnos_ubs.show');
        Route::get('alumnos_ubs/notas/{id}', 'AlumnoUbsController@show_notas')->name('alumnos_ubs.show_notas');
        Route::get('alumnos_ubs/asistencias/{id}', 'AlumnoUbsController@show_asistencias')->name('alumnos_ubs.show_asistencias');
        Route::get('alumnos_ubs/agregar', 'AlumnoUbsController@create')->name('alumnos_ubs.create');
        Route::post('alumnos_ubs/agregar', 'AlumnoUbsController@store')->name('alumnos_ubs.store');
        Route::get('alumnos_ubs/editar/{id}', 'AlumnoUbsController@edit')->name('alumnos_ubs.edit');
        Route::post('alumnos_ubs/actualizar/{id}', 'AlumnoUbsController@update')->name('alumnos_ubs.update');
        Route::post('alumnos_ubs/inactivar/{id}', 'AlumnoUbsController@unactivate')->name('alumnos_ubs.unactivate');
        Route::post('alumnos_ubs/activar/{id}', 'AlumnoUbsController@activate')->name('alumnos_ubs.activate');
        Route::get('alumnos_ubs/eliminar/{id}', 'AlumnoUbsController@destroy')->name('alumnos_ubs.get_destroy');
        Route::delete('alumnos_ubs/eliminar/{id}', 'AlumnoUbsController@destroy')->name('alumnos_ubs.destroy');
        Route::get('alumnos_ubs/ver_legajo/{id}', 'AlumnoUbsController@ver_legajo')->name('alumnos_ubs.ver_legajo');
        Route::post('alumnos_ubs/subir_legajo/{id}', 'AlumnoUbsController@subir_legajo')->name('alumnos_ubs.subir_legajo');
        Route::get('alumnos_ubs/eliminar_legajo/{id}', 'AlumnoUbsController@eliminar_legajo')->name('alumnos_ubs.get_eliminar_legajo');
        Route::delete('alumnos_ubs/eliminar_legajo/{id}', 'AlumnoUbsController@eliminar_legajo')->name('alumnos_ubs.eliminar_legajo');
        Route::get('alumnos_ubs/get_cedula/{cedula}', 'AlumnoUbsController@get_cedula')->name('alumnos_ubs.get_cedula');
    //Inscripciones
        Route::get('inscripciones_ubs', 'InscripcionUbsController@index')->name('inscripciones_ubs.index');
        Route::post('inscripciones_ubs', 'InscripcionUbsController@index');
        Route::get('inscripciones_ubs/ver/{id}', 'InscripcionUbsController@show')->name('inscripciones_ubs.show');
        Route::get('inscripciones_ubs/agregar', 'InscripcionUbsController@create')->name('inscripciones_ubs.create');
        Route::post('inscripciones_ubs/agregar', 'InscripcionUbsController@store')->name('inscripciones_ubs.store');
        Route::post('inscripciones_ubs/inactivar/{id}', 'InscripcionUbsController@unactivate')->name('inscripciones_ubs.unactivate');
        Route::post('inscripciones_ubs/activar/{id}', 'InscripcionUbsController@activate')->name('inscripciones_ubs.activate');
        Route::get('inscripciones_ubs/eliminar/{id}', 'InscripcionUbsController@destroy')->name('inscripciones_ubs.get_destroy');
        Route::delete('inscripciones_ubs/eliminar/{id}', 'InscripcionUbsController@destroy')->name('inscripciones_ubs.destroy');
        Route::get('inscripciones_ubs/contrato/{id}', 'InscripcionUbsController@pdf_contrato')->name('inscripciones_ubs.pdf_contrato');
        Route::post('inscripciones_ubs/agregar_convenio/{id}', 'InscripcionUbsController@agregar_convenio')->name('inscripciones_ubs.agregar_convenio');
        Route::post('inscripciones_ubs/eliminar_convenio/{id}', 'InscripcionUbsController@eliminar_convenio')->name('inscripciones_ubs.eliminar_convenio');
    //Cursos
        //Lista de Cursos
            Route::get('cursos', 'CursoController@index')->name('cursos.index');
            Route::get('cursos/ver/{id}', 'CursoController@show')->name('cursos.show');
            Route::get('cursos/agregar', 'CursoController@create')->name('cursos.create');
            Route::post('cursos/agregar', 'CursoController@store')->name('cursos.store');
            Route::get('cursos/editar/{id}', 'CursoController@edit')->name('cursos.edit');
            Route::post('cursos/actualizar/{id}', 'CursoController@update')->name('cursos.update');
            Route::post('cursos/inactivar/{id}', 'CursoController@unactivate')->name('cursos.unactivate');
            Route::post('cursos/activar/{id}', 'CursoController@activate')->name('cursos.activate');
            Route::get('cursos/eliminar/{id}', 'CursoController@destroy')->name('cursos.get_destroy');
            Route::delete('cursos/eliminar/{id}', 'CursoController@destroy')->name('cursos.destroy');
            Route::get('cursos/editar_modulos/{id}', 'CursoModuloController@edit')->name('cursos.edit_modulos');
            Route::post('cursos/editar_modulos/{id}', 'CursoModuloController@update')->name('cursos.update_modulos');
            Route::get('cursos/certificados_generados/{id}', 'CursoController@certificados_generados')->name('cursos.certificados_generados');
            Route::get('cursos/certificados_generados/regenerar/{id}', 'CursoController@regenerate_certificados')->name('cursos.regenerate_certificados');
            Route::post('cursos/certificados_generados/editar/{id}', 'CursoController@update_certificados_generados')->name('cursos.update_certificados_generados');
            //Modulos
                Route::get('modulos', 'ModuloController@index')->name('modulos.index');
                Route::get('modulos/all', 'ModuloController@index_ajax')->name('modulos.index_ajax');
                Route::get('modulos/show/{id}', 'ModuloController@show')->name('modulos.show');
                Route::post('modulos', 'ModuloController@store')->name('modulos.store');
                Route::get('modulos/{id}', 'ModuloController@edit')->name('modulos.edit');
                Route::post('modulos/{id}', 'ModuloController@update')->name('modulos.update');
                Route::get('modulos/inactivar/{id}', 'ModuloController@get_unactivate')->name('modulos.get_unactivate');
                Route::post('modulos/inactivar/{id}', 'ModuloController@unactivate')->name('modulos.unactivate');
                Route::get('modulos/activar/{id}', 'ModuloController@get_activate')->name('modulos.get_activate');
                Route::post('modulos/activar/{id}', 'ModuloController@activate')->name('modulos.activate');
                Route::get('modulos/eliminar/{id}', 'ModuloController@get_destroy')->name('modulos.get_destroy');
                Route::delete('modulos/eliminar/{id}', 'ModuloController@destroy')->name('modulos.destroy');
            //Alumnos Asistencias
                Route::get('cursos_asistencias/ver/{curso}', 'AlumnoAsistenciaUbsController@show')->name('alumnos_asistencias_ubs.show');
                Route::get('cursos_asistencias/agregar/{curso}/{modulo}', 'AlumnoAsistenciaUbsController@create')->name('alumnos_asistencias_ubs.create');
                Route::post('cursos_asistencias/guardar/{curso}/{modulo}/{docente}', 'AlumnoAsistenciaUbsController@store')->name('alumnos_asistencias_ubs.store');
                Route::get('cursos_asistencias/agregar/{curso}/{modulo}', 'AlumnoAsistenciaUbsController@create');
                Route::get('cursos_asistencias/editar/{id}', 'AlumnoAsistenciaUbsController@edit')->name('alumnos_asistencias_ubs.edit');
                Route::post('cursos_asistencias/actualizar/{id}', 'AlumnoAsistenciaUbsController@update')->name('alumnos_asistencias_ubs.update');
                Route::get('cursos_asistencias/editar_observacion/{id}', 'AlumnoAsistenciaUbsController@edit_observacion')->name('alumnos_asistencias_ubs.edit_observacion');
                Route::post('cursos_asistencias/actualizar_observacion/{id}', 'AlumnoAsistenciaUbsController@update_observacion')->name('alumnos_asistencias_ubs.update_observacion');
            //Alumnos Notas
                Route::get('cursos_notas/ver/{curso}', 'CursoNotaController@show')->name('cursos_notas_ubs.show');
                Route::get('cursos_notas/agregar/{curso}', 'CursoNotaController@create')->name('cursos_notas_ubs.create');
                Route::post('cursos_notas/guardar/{curso}', 'CursoNotaController@store')->name('cursos_notas_ubs.store');
                Route::get('cursos_notas/actualizar/{id}', 'CursoNotaController@update')->name('cursos_notas_ubs.update');
                Route::post('cursos_notas/actualizar/{id}', 'CursoNotaController@update');
            //Certificados
                Route::get('cursos_certificados/ver/{curso}', 'CursoCertificadoController@show')->name('cursos_certificados.show');
                Route::get('cursos_certificados/generar/{curso}', 'CursoCertificadoController@generate')->name('cursos_certificados.generate');
        //Tipos de Cursos
            Route::get('tipos_cursos', 'TipoCursoController@index')->name('tipos_cursos.index');
            Route::get('tipos_cursos/all', 'TipoCursoController@index_ajax')->name('tipos_cursos.index_ajax');
            Route::get('tipos_cursos/show/{id}', 'TipoCursoController@show')->name('tipos_cursos.show');
            Route::post('tipos_cursos', 'TipoCursoController@store')->name('tipos_cursos.store');
            Route::get('tipos_cursos/{id}', 'TipoCursoController@edit')->name('tipos_cursos.edit');
            Route::post('tipos_cursos/{id}', 'TipoCursoController@update')->name('tipos_cursos.update');
            Route::get('tipos_cursos/inactivar/{id}', 'TipoCursoController@get_unactivate')->name('tipos_cursos.get_unactivate');
            Route::post('tipos_cursos/inactivar/{id}', 'TipoCursoController@unactivate')->name('tipos_cursos.unactivate');
            Route::get('tipos_cursos/activar/{id}', 'TipoCursoController@get_activate')->name('tipos_cursos.get_activate');
            Route::post('tipos_cursos/activar/{id}', 'TipoCursoController@activate')->name('tipos_cursos.activate');
            Route::get('tipos_cursos/eliminar/{id}', 'TipoCursoController@get_destroy')->name('tipos_cursos.get_destroy');
            Route::delete('tipos_cursos/eliminar/{id}', 'TipoCursoController@destroy')->name('tipos_cursos.destroy');
    //Maestrías
        //Lista de Maestrías
            Route::get('maestrias', 'MaestriaController@index')->name('maestrias.index');
            Route::get('maestrias/ver/{id}', 'MaestriaController@show')->name('maestrias.show');
            Route::get('maestrias/agregar', 'MaestriaController@create')->name('maestrias.create');
            Route::post('maestrias/agregar', 'MaestriaController@store')->name('maestrias.store');
            Route::get('maestrias/editar/{id}', 'MaestriaController@edit')->name('maestrias.edit');
            Route::post('maestrias/actualizar/{id}', 'MaestriaController@update')->name('maestrias.update');
            Route::post('maestrias/inactivar/{id}', 'MaestriaController@unactivate')->name('maestrias.unactivate');
            Route::post('maestrias/activar/{id}', 'MaestriaController@activate')->name('maestrias.activate');
            Route::get('maestrias/eliminar/{id}', 'MaestriaController@destroy')->name('maestrias.get_destroy');
            Route::delete('maestrias/eliminar/{id}', 'MaestriaController@destroy')->name('maestrias.destroy');
            Route::get('maestrias/editar_modulos/{id}', 'MaestriaModuloController@edit')->name('maestrias.edit_modulos');
            Route::post('maestrias/editar_modulos/{id}', 'MaestriaModuloController@update')->name('maestrias.update_modulos');
            //Alumnos Asistencias
                Route::get('maestrias_asistencias/ver/{curso}', 'AlumnoAsistenciaMaestriaController@show')->name('alumnos_asistencias_maestrias.show');
                Route::get('maestrias_asistencias/agregar/{curso}/{modulo}', 'AlumnoAsistenciaMaestriaController@create')->name('alumnos_asistencias_maestrias.create');
                Route::post('maestrias_asistencias/guardar/{curso}/{modulo}/{docente}', 'AlumnoAsistenciaMaestriaController@store')->name('alumnos_asistencias_maestrias.store');
                Route::get('maestrias_asistencias/agregar/{curso}/{modulo}', 'AlumnoAsistenciaMaestriaController@create');
                Route::get('maestrias_asistencias/editar/{id}', 'AlumnoAsistenciaMaestriaController@edit')->name('alumnos_asistencias_maestrias.edit');
                Route::post('maestrias_asistencias/actualizar/{id}', 'AlumnoAsistenciaMaestriaController@update')->name('alumnos_asistencias_maestrias.update');
                Route::get('maestrias_asistencias/editar_observacion/{id}', 'AlumnoAsistenciaMaestriaController@edit_observacion')->name('alumnos_asistencias_maestrias.edit_observacion');
                Route::post('maestrias_asistencias/actualizar_observacion/{id}', 'AlumnoAsistenciaMaestriaController@update_observacion')->name('alumnos_asistencias_maestrias.update_observacion');
            //Alumnos Notas
                Route::get('maestrias_notas/ver/{curso}', 'AlumnoNotaUbsController@show')->name('alumnos_notas_ubs.show');
                Route::get('maestrias_notas/agregar/{curso}/{modulo}/{evaluacion}', 'AlumnoNotaUbsController@create')->name('alumnos_notas_ubs.create');
                Route::post('maestrias_notas/guardar/{curso}/{modulo}/{evaluacion}', 'AlumnoNotaUbsController@store')->name('alumnos_notas_ubs.store');
                Route::get('maestrias_notas/get_evaluaciones/{curso}/{modulo}', 'AlumnoNotaUbsController@get_evaluaciones')->name('alumnos_notas_ubs.get_evaluaciones');
            //Actas
                Route::get('maestrias_actas_evaluaciones/{curso}', 'ActaEvaluacionUbsController@index')->name('actas_evaluaciones_ubs.index');
                Route::get('maestrias_actas_evaluaciones/ver/{id}', 'ActaEvaluacionUbsController@show')->name('actas_evaluaciones_ubs.show');
                Route::get('maestrias_actas_evaluaciones/agregar/{curso}/{modulo}', 'ActaEvaluacionUbsController@create')->name('actas_evaluaciones_ubs.create');
                Route::post('maestrias_actas_evaluaciones/agregar', 'ActaEvaluacionUbsController@store')->name('actas_evaluaciones_ubs.store');
                Route::get('maestrias_actas_evaluaciones/eliminar/{id}', 'ActaEvaluacionUbsController@destroy')->name('actas_evaluaciones_ubs.get_destroy');
                Route::delete('maestrias_actas_evaluaciones/eliminar/{id}', 'ActaEvaluacionUbsController@destroy')->name('actas_evaluaciones_ubs.destroy');
                Route::post('maestrias_actas_evaluaciones/subir_acta/{id}', 'ActaEvaluacionUbsController@subir_acta')->name('actas_evaluaciones_ubs.subir_acta');
                Route::get('maestrias_actas_evaluaciones/eliminar_acta/{id}', 'ActaEvaluacionUbsController@eliminar_acta')->name('actas_evaluaciones_ubs.eliminar_acta');
                Route::delete('maestrias_actas_evaluaciones/eliminar_acta/{id}', 'ActaEvaluacionUbsController@eliminar_acta');
                Route::get('maestrias_actas_evaluaciones/ver_actas/{curso}/{modulo}', 'ActaEvaluacionUbsController@show_actas')->name('actas_evaluaciones_ubs.show_actas');
                Route::get('maestrias_actas_evaluaciones/generar_acta/{curso}/{modulo}/{tipo}', 'ActaEvaluacionUbsController@generar_actas')->name('actas_evaluaciones_ubs.generar_actas');
            //Certificados de Estudios
                Route::get('maestrias/certificado_estudios/{maestria}/{alumno}', 'CertificadoEstudioUbsController@show')->name('certificados_estudios_ubs.show');
                Route::get('maestrias/certificado_estudios/generar/{maestria}/{alumno}', 'CertificadoEstudioUbsController@generar')->name('certificados_estudios_ubs.generar');
                Route::get('maestrias_get_alumnos/{id}', 'CertificadoEstudioUbsController@get_alumnos')->name('certificados_estudios_ubs.get_alumnos');
            //Modulos
                Route::get('modulos_maestrias', 'ModuloMaestriaController@index')->name('modulos_maestrias.index');
                Route::get('modulos_maestrias/all', 'ModuloMaestriaController@index_ajax')->name('modulos_maestrias.index_ajax');
                Route::get('modulos_maestrias/show/{id}', 'ModuloMaestriaController@show')->name('modulos_maestrias.show');
                Route::post('modulos_maestrias', 'ModuloMaestriaController@store')->name('modulos_maestrias.store');
                Route::get('modulos_maestrias/{id}', 'ModuloMaestriaController@edit')->name('modulos_maestrias.edit');
                Route::post('modulos_maestrias/{id}', 'ModuloMaestriaController@update')->name('modulos_maestrias.update');
                Route::get('modulos_maestrias/inactivar/{id}', 'ModuloMaestriaController@get_unactivate')->name('modulos_maestrias.get_unactivate');
                Route::post('modulos_maestrias/inactivar/{id}', 'ModuloMaestriaController@unactivate')->name('modulos_maestrias.unactivate');
                Route::get('modulos_maestrias/activar/{id}', 'ModuloMaestriaController@get_activate')->name('modulos_maestrias.get_activate');
                Route::post('modulos_maestrias/activar/{id}', 'ModuloMaestriaController@activate')->name('modulos_maestrias.activate');
                Route::get('modulos_maestrias/eliminar/{id}', 'ModuloMaestriaController@get_destroy')->name('modulos_maestrias.get_destroy');
                Route::delete('modulos_maestrias/eliminar/{id}', 'ModuloMaestriaController@destroy')->name('modulos_maestrias.destroy');
                //Correlatividades
                    Route::get('modulos_correlatividades/ver/{id}', 'ModuloCorrelatividadController@show')->name('modulos_correlatividades.show');
                    Route::get('modulos_correlatividades/editar/{id}', 'ModuloCorrelatividadController@edit')->name('modulos_correlatividades.edit');
                    Route::post('modulos_correlatividades/actualizar/{id}', 'ModuloCorrelatividadController@update')->name('modulos_correlatividades.update');
                    Route::get('modulos_correlatividades/eliminar/{id}', 'ModuloCorrelatividadController@destroy')->name('modulos_correlatividades.get_destroy');
                    Route::delete('modulos_correlatividades/eliminar/{id}', 'ModuloCorrelatividadController@destroy')->name('modulos_correlatividades.destroy');
            //Clases
                Route::get('clases_maestrias/{curso}', 'ClaseMaestriaController@index')->name('clases_maestrias.index');
                Route::get('clases_maestrias/ver/{id}', 'ClaseMaestriaController@show')->name('clases_maestrias.show');
                Route::get('clases_maestrias/agregar/{curso}/{modulo}', 'ClaseMaestriaController@create')->name('clases_maestrias.create');
                Route::post('clases_maestrias/guardar/{curso}/{modulo}/{docente}', 'ClaseMaestriaController@store')->name('clases_maestrias.store');
                Route::get('clases_maestrias/eliminar/{id}', 'ClaseMaestriaController@destroy')->name('clases_maestrias.get_destroy');
                Route::delete('clases_maestrias/eliminar/{id}', 'ClaseMaestriaController@destroy')->name('clases_maestrias.destroy');
        //Extensiones Universitarias
            //Lista de Extensiones Universtiarias
                Route::get('extensiones_universitarias_ubs', 'ExtensionUniversitariaUbsController@index')->name('extensiones_universitarias_ubs.index');
                Route::get('extensiones_universitarias_ubs/ver/{id}', 'ExtensionUniversitariaUbsController@show')->name('extensiones_universitarias_ubs.show');
                Route::get('extensiones_universitarias_ubs/agregar', 'ExtensionUniversitariaUbsController@create')->name('extensiones_universitarias_ubs.create');
                Route::post('extensiones_universitarias_ubs/agregar', 'ExtensionUniversitariaUbsController@store')->name('extensiones_universitarias_ubs.store');
                Route::get('extensiones_universitarias_ubs/editar/{id}', 'ExtensionUniversitariaUbsController@edit')->name('extensiones_universitarias_ubs.edit');
                Route::post('extensiones_universitarias_ubs/actualizar/{id}', 'ExtensionUniversitariaUbsController@update')->name('extensiones_universitarias_ubs.update');
                Route::get('extensiones_universitarias_ubs/guardar_horas/{id}', 'ExtensionUniversitariaUbsController@update_hours')->name('extensiones_universitarias_ubs.update_hours');
                Route::post('extensiones_universitarias_ubs/guardar_horas/{id}', 'ExtensionUniversitariaUbsController@update_hours');
                Route::post('extensiones_universitarias_ubs/aprobar/{id}', 'ExtensionUniversitariaUbsController@approve')->name('extensiones_universitarias_ubs.approve');
                Route::post('extensiones_universitarias_ubs/rechazar/{id}', 'ExtensionUniversitariaUbsController@reject')->name('extensiones_universitarias_ubs.reject');
                Route::post('extensiones_universitarias_ubs/anular_rechazo/{id}', 'ExtensionUniversitariaUbsController@unreject')->name('extensiones_universitarias_ubs.unreject');
                Route::post('extensiones_universitarias_ubs/desaprobar/{id}', 'ExtensionUniversitariaUbsController@unapprove')->name('extensiones_universitarias_ubs.unapprove');
                Route::post('extensiones_universitarias_ubs/finalizar/{id}', 'ExtensionUniversitariaUbsController@finish')->name('extensiones_universitarias_ubs.finish');
                Route::get('extensiones_universitarias_ubs/cargar_informe/{id}', 'ExtensionUniversitariaUbsController@cargar_informe')->name('extensiones_universitarias_ubs.cargar_informe');
                Route::post('extensiones_universitarias_ubs/cargar_informe/{id}', 'ExtensionUniversitariaUbsController@cargar_informe');
                Route::get('extensiones_universitarias_ubs/cambiar_adjuntos/{id}', 'ExtensionUniversitariaUbsController@change_adjuntos')->name('extensiones_universitarias_ubs.change_adjuntos');
                Route::post('extensiones_universitarias_ubs/cambiar_adjuntos/{id}', 'ExtensionUniversitariaUbsController@change_adjuntos');
                Route::get('extensiones_universitarias_ubs/eliminar/{id}', 'ExtensionUniversitariaUbsController@destroy')->name('extensiones_universitarias_ubs.get_destroy');
                Route::delete('extensiones_universitarias_ubs/eliminar/{id}', 'ExtensionUniversitariaUbsController@destroy')->name('extensiones_universitarias_ubs.destroy');
            //Tipos de Actividades - Extension Universitaria
                Route::get('tipos_extensiones_universitarias_ubs', 'TipoExtensionUniversitariaUbsController@index')->name('tipos_extensiones_universitarias_ubs.index');
                Route::get('tipos_extensiones_universitarias_ubs/ver/{id}', 'TipoExtensionUniversitariaUbsController@show')->name('tipos_extensiones_universitarias_ubs.show');
                Route::get('tipos_extensiones_universitarias_ubs/agregar', 'TipoExtensionUniversitariaUbsController@create')->name('tipos_extensiones_universitarias_ubs.create');
                Route::post('tipos_extensiones_universitarias_ubs/agregar', 'TipoExtensionUniversitariaUbsController@store')->name('tipos_extensiones_universitarias_ubs.store');
                Route::get('tipos_extensiones_universitarias_ubs/editar/{id}', 'TipoExtensionUniversitariaUbsController@edit')->name('tipos_extensiones_universitarias_ubs.edit');
                Route::post('tipos_extensiones_universitarias_ubs/actualizar/{id}', 'TipoExtensionUniversitariaUbsController@update')->name('tipos_extensiones_universitarias_ubs.update');
                Route::post('tipos_extensiones_universitarias_ubs/inactivar/{id}', 'TipoExtensionUniversitariaUbsController@unactivate')->name('tipos_extensiones_universitarias_ubs.unactivate');
                Route::post('tipos_extensiones_universitarias_ubs/activar/{id}', 'TipoExtensionUniversitariaUbsController@activate')->name('tipos_extensiones_universitarias_ubs.activate');
                Route::get('tipos_extensiones_universitarias_ubs/eliminar/{id}', 'TipoExtensionUniversitariaUbsController@destroy')->name('tipos_extensiones_universitarias_ubs.get_destroy');
                Route::delete('tipos_extensiones_universitarias_ubs/eliminar/{id}', 'TipoExtensionUniversitariaUbsController@destroy')->name('tipos_extensiones_universitarias_ubs.destroy');
            //Requerimientos - Extension Universitaria
            Route::get('requerimientos_extensiones_universitarias_ubs', 'RequerimientoExtensionUniversitariaUbsController@show')->name('requerimientos_extensiones_universitarias_ubs.show');
            Route::get('requerimientos_extensiones_universitarias_ubs/agregar', 'RequerimientoExtensionUniversitariaUbsController@create')->name('requerimientos_extensiones_universitarias_ubs.create');
            Route::post('requerimientos_extensiones_universitarias_ubs/agregar', 'RequerimientoExtensionUniversitariaUbsController@store')->name('requerimientos_extensiones_universitarias_ubs.store');
            Route::get('requerimientos_extensiones_universitarias_ubs/editar/{id}', 'RequerimientoExtensionUniversitariaUbsController@edit')->name('requerimientos_extensiones_universitarias_ubs.edit');
            Route::post('requerimientos_extensiones_universitarias_ubs/actualizar/{id}', 'RequerimientoExtensionUniversitariaUbsController@update')->name('requerimientos_extensiones_universitarias_ubs.update');
    //Tesis
        //Lista de Tesis
            Route::get('tesis_ubs', 'TesisUbsController@index')->name('tesis_ubs.index');
            Route::get('tesis_ubs/ver/{id}', 'TesisUbsController@show')->name('tesis_ubs.show');
            Route::get('tesis_ubs/agregar', 'TesisUbsController@create')->name('tesis_ubs.create');
            Route::post('tesis_ubs/agregar', 'TesisUbsController@store')->name('tesis_ubs.store');
            Route::get('tesis_ubs/get_maestrias/{alumno}', 'TesisUbsController@get_maestrias')->name('tesis_ubs.get_maestrias');
            Route::get('tesis_ubs/editar/{id}', 'TesisUbsController@edit')->name('tesis_ubs.edit');
            Route::post('tesis_ubs/actualizar/{id}', 'TesisUbsController@update')->name('tesis_ubs.update');
            Route::get('tesis_ubs/generar_acta/{id}', 'TesisUbsController@generate_acta')->name('tesis_ubs.generate_acta');
            Route::post('tesis_ubs/asignar_fecha_defensa/{id}', 'TesisUbsController@asignar_fecha_defensa')->name('tesis_ubs.asignar_fecha_defensa');
            Route::post('tesis_ubs/puntuar_defensa/{id}', 'TesisUbsController@puntuar_defensa')->name('tesis_ubs.puntuar_defensa');
            //Inscripciones Temas Tesis
                Route::post('tesis_ubs/aprobar_calidad/{id}', 'InscripcionTemaTesisUbsController@aprobar_calidad')->name('tesis_ubs.aprobar_calidad');
                Route::post('tesis_ubs/aprobar_tutor/{id}', 'InscripcionTemaTesisUbsController@aprobar_tutor')->name('tesis_ubs.aprobar_tutor');
                Route::post('tesis_ubs/anular_aprobacion_calidad/{id}', 'InscripcionTemaTesisUbsController@anular_aprobacion_calidad')->name('tesis_ubs.anular_aprobacion_calidad');
                Route::post('tesis_ubs/anular_aprobacion_tutor/{id}', 'InscripcionTemaTesisUbsController@anular_aprobacion_tutor')->name('tesis_ubs.anular_aprobacion_tutor');
                Route::post('tesis_ubs/rechazar/{id}', 'InscripcionTemaTesisUbsController@rechazar')->name('tesis_ubs.rechazar');
                Route::post('tesis_ubs/anular_rechazo/{id}', 'InscripcionTemaTesisUbsController@anular_rechazo')->name('tesis_ubs.anular_rechazo');
                //Anteproyectos
                    Route::get('tesis_ubs/anteproyecto/ver/{id}', 'AnteproyectoTesisUbsController@show')->name('tesis_ubs.show_anteproyecto');
                    Route::get('tesis_ubs/anteproyecto/generar/{id}', 'AnteproyectoTesisUbsController@create')->name('tesis_ubs.create_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/guardar/{id}', 'AnteproyectoTesisUbsController@store')->name('tesis_ubs.store_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/aprobar_tutor/{id}', 'AnteproyectoTesisUbsController@aprobar_tutor')->name('tesis_ubs.aprobar_tutor_anteproyecto');
                    Route::get('tesis_ubs/anteproyecto/aprobar_calidad/{id}', 'AnteproyectoTesisUbsController@aprobar_calidad')->name('tesis_ubs.aprobar_calidad_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/aprobar_calidad/{id}', 'AnteproyectoTesisUbsController@aprobar_calidad');
                    Route::post('tesis_ubs/anteproyecto/anular_aprobacion_tutor/{id}', 'AnteproyectoTesisUbsController@anular_aprobacion_tutor')->name('tesis_ubs.anular_aprobacion_tutor_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/anular_aprobacion_calidad/{id}', 'AnteproyectoTesisUbsController@anular_aprobacion_calidad')->name('tesis_ubs.anular_aprobacion_calidad_anteproyecto');
                    Route::get('tesis_ubs/anteproyecto/ver_entregas/{id}', 'AnteproyectoTesisUbsController@show_entregas')->name('tesis_ubs.show_entregas_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/save_entrega/{id}', 'AnteproyectoTesisUbsController@save_entrega')->name('tesis_ubs.save_entrega_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/save_correccion/{id}', 'AnteproyectoTesisUbsController@save_correccion')->name('tesis_ubs.save_correccion_anteproyecto');
                    Route::post('tesis_ubs/anteproyecto/delete_entrega/{id}', 'AnteproyectoTesisUbsController@delete_entrega')->name('tesis_ubs.delete_entrega_anteproyecto');
                //Borradores
                    Route::get('tesis_ubs/borrador/ver/{id}', 'BorradorTesisUbsController@show')->name('tesis_ubs.show_borrador');
                    Route::get('tesis_ubs/borrador/generar/{id}', 'BorradorTesisUbsController@create')->name('tesis_ubs.create_borrador');
                    Route::post('tesis_ubs/borrador/guardar/{id}', 'BorradorTesisUbsController@store')->name('tesis_ubs.store_borrador');
                    Route::post('tesis_ubs/borrador/aprobar_tutor/{id}', 'BorradorTesisUbsController@aprobar_tutor')->name('tesis_ubs.aprobar_tutor_borrador');
                    Route::post('tesis_ubs/borrador/aprobar_calidad/{id}', 'BorradorTesisUbsController@aprobar_calidad')->name('tesis_ubs.aprobar_calidad_borrador');
                    Route::post('tesis_ubs/borrador/anular_aprobacion_tutor/{id}', 'BorradorTesisUbsController@anular_aprobacion_tutor')->name('tesis_ubs.anular_aprobacion_tutor_borrador');
                    Route::post('tesis_ubs/borrador/anular_aprobacion_calidad/{id}', 'BorradorTesisUbsController@anular_aprobacion_calidad')->name('tesis_ubs.anular_aprobacion_calidad_borrador');
                    Route::get('tesis_ubs/borrador/ver_entregas/{id}', 'BorradorTesisUbsController@show_entregas')->name('tesis_ubs.show_entregas_borrador');
                    Route::post('tesis_ubs/borrador/save_entrega/{id}', 'BorradorTesisUbsController@save_entrega')->name('tesis_ubs.save_entrega_borrador');
                    Route::post('tesis_ubs/borrador/save_correccion/{id}', 'BorradorTesisUbsController@save_correccion')->name('tesis_ubs.save_correccion_borrador');
                    Route::post('tesis_ubs/borrador/delete_entrega/{id}', 'BorradorTesisUbsController@delete_entrega')->name('tesis_ubs.delete_entrega_borrador');
        //Parametros
            Route::get('tesis_ubs/parametros', 'TesisUbsController@parametros_index')->name('tesis_parametros_ubs.index');
            //Fechas de Defensa
                Route::get('tesis_ubs/parametros/fechas_defensas', 'FechaDefensaTesisUbsController@index')->name('tesis_parametros_ubs.fechas_defensas_index');
                Route::get('tesis_ubs/parametros/fechas_defensas/ver/{id}', 'FechaDefensaTesisUbsController@show')->name('tesis_parametros_ubs.fechas_defensas_show');
                Route::get('tesis_ubs/parametros/fechas_defensas/agregar', 'FechaDefensaTesisUbsController@create')->name('tesis_parametros_ubs.fechas_defensas_create');
                Route::post('tesis_ubs/parametros/fechas_defensas/agregar', 'FechaDefensaTesisUbsController@store')->name('tesis_parametros_ubs.fechas_defensas_store');
                Route::get('tesis_ubs/parametros/fechas_defensas/editar/{id}', 'FechaDefensaTesisUbsController@edit')->name('tesis_parametros_ubs.fechas_defensas_edit');
                Route::post('tesis_ubs/parametros/fechas_defensas/actualizar/{id}', 'FechaDefensaTesisUbsController@update')->name('tesis_parametros_ubs.fechas_defensas_update');
                Route::get('tesis_ubs/parametros/fechas_defensas/eliminar/{id}', 'FechaDefensaTesisUbsController@destroy')->name('tesis_parametros_ubs.fechas_defensas_get_destroy');
                Route::delete('tesis_ubs/parametros/fechas_defensas/eliminar/{id}', 'FechaDefensaTesisUbsController@destroy')->name('tesis_parametros_ubs.fechas_defensas_destroy');
            //Lineas de Tesis
                Route::get('tesis_ubs/parametros/lineas', 'LineaTesisUbsController@index')->name('tesis_parametros_ubs.lineas_index');
                Route::get('tesis_ubs/parametros/lineas/all', 'LineaTesisUbsController@index_ajax')->name('tesis_parametros_ubs.lineas_index_ajax');
                Route::get('tesis_ubs/parametros/lineas/show/{id}', 'LineaTesisUbsController@show')->name('tesis_parametros_ubs.lineas_show');
                Route::post('tesis_ubs/parametros/lineas', 'LineaTesisUbsController@store')->name('tesis_parametros_ubs.lineas_store');
                Route::get('tesis_ubs/parametros/lineas/{id}', 'LineaTesisUbsController@edit')->name('tesis_parametros_ubs.lineas_edit');
                Route::post('tesis_ubs/parametros/lineas/{id}', 'LineaTesisUbsController@update')->name('tesis_parametros_ubs.lineas_update');
                Route::get('tesis_ubs/parametros/lineas/inactivar/{id}', 'LineaTesisUbsController@get_unactivate')->name('tesis_parametros_ubs.lineas_get_unactivate');
                Route::post('tesis_ubs/parametros/lineas/inactivar/{id}', 'LineaTesisUbsController@unactivate')->name('tesis_parametros_ubs.lineas_unactivate');
                Route::get('tesis_ubs/parametros/lineas/activar/{id}', 'LineaTesisUbsController@get_activate')->name('tesis_parametros_ubs.lineas_get_activate');
                Route::post('tesis_ubs/parametros/lineas/activar/{id}', 'LineaTesisUbsController@activate')->name('tesis_parametros_ubs.lineas_activate');
                Route::get('tesis_ubs/parametros/lineas/eliminar/{id}', 'LineaTesisUbsController@get_destroy')->name('tesis_parametros_ubs.lineas_get_destroy');
                Route::delete('tesis_ubs/parametros/lineas/eliminar/{id}', 'LineaTesisUbsController@destroy')->name('tesis_parametros_ubs.lineas_destroy');
            //Bloques Anteproyectos
                Route::get('tesis_ubs/parametros/bloques_anteproyectos', 'BloqueAnteproyectoTesisUbsController@index')->name('tesis_parametros_ubs.bloques_anteproyectos_index');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/all', 'BloqueAnteproyectoTesisUbsController@index_ajax')->name('tesis_parametros_ubs.bloques_anteproyectos_index_ajax');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/show/{id}', 'BloqueAnteproyectoTesisUbsController@show')->name('tesis_parametros_ubs.bloques_anteproyectos_show');
                Route::post('tesis_ubs/parametros/bloques_anteproyectos', 'BloqueAnteproyectoTesisUbsController@store')->name('tesis_parametros_ubs.bloques_anteproyectos_store');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/{id}', 'BloqueAnteproyectoTesisUbsController@edit')->name('tesis_parametros_ubs.bloques_anteproyectos_edit');
                Route::post('tesis_ubs/parametros/bloques_anteproyectos/{id}', 'BloqueAnteproyectoTesisUbsController@update')->name('tesis_parametros_ubs.bloques_anteproyectos_update');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/inactivar/{id}', 'BloqueAnteproyectoTesisUbsController@get_unactivate')->name('tesis_parametros_ubs.bloques_anteproyectos_get_unactivate');
                Route::post('tesis_ubs/parametros/bloques_anteproyectos/inactivar/{id}', 'BloqueAnteproyectoTesisUbsController@unactivate')->name('tesis_parametros_ubs.bloques_anteproyectos_unactivate');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/activar/{id}', 'BloqueAnteproyectoTesisUbsController@get_activate')->name('tesis_parametros_ubs.bloques_anteproyectos_get_activate');
                Route::post('tesis_ubs/parametros/bloques_anteproyectos/activar/{id}', 'BloqueAnteproyectoTesisUbsController@activate')->name('tesis_parametros_ubs.bloques_anteproyectos_activate');
                Route::get('tesis_ubs/parametros/bloques_anteproyectos/eliminar/{id}', 'BloqueAnteproyectoTesisUbsController@get_destroy')->name('tesis_parametros_ubs.bloques_anteproyectos_get_destroy');
                Route::delete('tesis_ubs/parametros/bloques_anteproyectos/eliminar/{id}', 'BloqueAnteproyectoTesisUbsController@destroy')->name('tesis_parametros_ubs.bloques_anteproyectos_destroy');
            //Bloques Borradores
                Route::get('tesis_ubs/parametros/bloques_borradores', 'BloqueBorradorTesisUbsController@index')->name('tesis_parametros_ubs.bloques_borradores_index');
                Route::get('tesis_ubs/parametros/bloques_borradores/all', 'BloqueBorradorTesisUbsController@index_ajax')->name('tesis_parametros_ubs.bloques_borradores_index_ajax');
                Route::get('tesis_ubs/parametros/bloques_borradores/show/{id}', 'BloqueBorradorTesisUbsController@show')->name('tesis_parametros_ubs.bloques_borradores_show');
                Route::post('tesis_ubs/parametros/bloques_borradores', 'BloqueBorradorTesisUbsController@store')->name('tesis_parametros_ubs.bloques_borradores_store');
                Route::get('tesis_ubs/parametros/bloques_borradores/{id}', 'BloqueBorradorTesisUbsController@edit')->name('tesis_parametros_ubs.bloques_borradores_edit');
                Route::post('tesis_ubs/parametros/bloques_borradores/{id}', 'BloqueBorradorTesisUbsController@update')->name('tesis_parametros_ubs.bloques_borradores_update');
                Route::get('tesis_ubs/parametros/bloques_borradores/inactivar/{id}', 'BloqueBorradorTesisUbsController@get_unactivate')->name('tesis_parametros_ubs.bloques_borradores_get_unactivate');
                Route::post('tesis_ubs/parametros/bloques_borradores/inactivar/{id}', 'BloqueBorradorTesisUbsController@unactivate')->name('tesis_parametros_ubs.bloques_borradores_unactivate');
                Route::get('tesis_ubs/parametros/bloques_borradores/activar/{id}', 'BloqueBorradorTesisUbsController@get_activate')->name('tesis_parametros_ubs.bloques_borradores_get_activate');
                Route::post('tesis_ubs/parametros/bloques_borradores/activar/{id}', 'BloqueBorradorTesisUbsController@activate')->name('tesis_parametros_ubs.bloques_borradores_activate');
                Route::get('tesis_ubs/parametros/bloques_borradores/eliminar/{id}', 'BloqueBorradorTesisUbsController@get_destroy')->name('tesis_parametros_ubs.bloques_borradores_get_destroy');
                Route::delete('tesis_ubs/parametros/bloques_borradores/eliminar/{id}', 'BloqueBorradorTesisUbsController@destroy')->name('tesis_parametros_ubs.bloques_borradores_destroy');
    //Docentes
        Route::get('docentes_ubs', 'DocenteUbsController@index')->name('docentes_ubs.index');
        Route::post('docentes_ubs', 'DocenteUbsController@index');
        Route::get('docentes_ubs/ver/{id}', 'DocenteUbsController@show')->name('docentes_ubs.show');
        Route::get('docentes_ubs/agregar', 'DocenteUbsController@create')->name('docentes_ubs.create');
        Route::post('docentes_ubs/agregar', 'DocenteUbsController@store')->name('docentes_ubs.store');
        Route::get('docentes_ubs/editar/{id}', 'DocenteUbsController@edit')->name('docentes_ubs.edit');
        Route::post('docentes_ubs/actualizar/{id}', 'DocenteUbsController@update')->name('docentes_ubs.update');
        Route::post('docentes_ubs/inactivar/{id}', 'DocenteUbsController@unactivate')->name('docentes_ubs.unactivate');
        Route::post('docentes_ubs/activar/{id}', 'DocenteUbsController@activate')->name('docentes_ubs.activate');
        Route::get('docentes_ubs/eliminar/{id}', 'DocenteUbsController@destroy')->name('docentes_ubs.get_destroy');
        Route::delete('docentes_ubs/eliminar/{id}', 'DocenteUbsController@destroy')->name('docentes_ubs.destroy');
        Route::get('docentes_ubs/ver_legajo/{id}', 'DocenteUbsController@ver_legajo')->name('docentes_ubs.ver_legajo');
        Route::post('docentes_ubs/subir_legajo/{id}', 'DocenteUbsController@subir_legajo')->name('docentes_ubs.subir_legajo');
        Route::get('docentes_ubs/eliminar_legajo/{id}', 'DocenteUbsController@eliminar_legajo')->name('docentes_ubs.get_eliminar_legajo');
        Route::delete('docentes_ubs/eliminar_legajo/{id}', 'DocenteUbsController@eliminar_legajo')->name('docentes_ubs.eliminar_legajo');

//Ventas
    //Lista de Ventas
        Route::get('ventas', 'VentaController@index')->name('ventas.index');
        Route::get('ventas/ver/{id}', 'VentaController@show')->name('ventas.show');
        Route::get('ventas/agregar', 'VentaController@create')->name('ventas.create');
        Route::post('ventas/agregar', 'VentaController@store')->name('ventas.store');
        Route::post('ventas/inactivar/{id}', 'VentaController@unactivate')->name('ventas.unactivate');
        Route::post('ventas/activar/{id}', 'VentaController@activate')->name('ventas.activate');
        Route::get('ventas/eliminar/{id}', 'VentaController@destroy')->name('ventas.get_destroy');
        Route::delete('ventas/eliminar/{id}', 'VentaController@destroy')->name('ventas.destroy');
        Route::get('ventas/get_subunidades_negocios/{id}', 'VentaController@get_subunidades_negocios')->name('ventas.get_subunidades_negocios');
        Route::get('ventas/get_subcentros_costos/{id}', 'VentaController@get_subcentros_costos')->name('ventas.get_subcentros_costos');
        Route::get('ventas/get_notas_creditos/{id}', 'VentaController@get_notas_creditos')->name('ventas.get_notas_creditos');
    //Recibos
        Route::get('recibos', 'ReciboController@index')->name('recibos.index');
        Route::get('recibos/ver/{id}', 'ReciboController@show')->name('recibos.show');
        Route::get('recibos/agregar', 'ReciboController@create')->name('recibos.create');
        Route::get('recibos/agregar_unico/{id}', 'ReciboController@create_unique')->name('recibos.create_unique');
        Route::post('recibos/agregar', 'ReciboController@store')->name('recibos.store');
        Route::post('recibos/inactivar/{id}', 'ReciboController@unactivate')->name('recibos.unactivate');
        Route::post('recibos/activar/{id}', 'ReciboController@activate')->name('recibos.activate');
        Route::get('recibos/eliminar/{id}', 'ReciboController@destroy')->name('recibos.get_destroy');
        Route::delete('recibos/eliminar/{id}', 'ReciboController@destroy')->name('recibos.destroy');
        Route::get('recibos/get_ventas/{id}', 'ReciboController@get_ventas')->name('recibos.get_ventas');
    //Cobros
        Route::get('cobros', 'CobroController@index')->name('cobros.index');
    //Notas de Creditos
        Route::get('notas_creditos', 'NotaCreditoController@index')->name('notas_creditos.index');
        Route::get('notas_creditos/ver/{id}', 'NotaCreditoController@show')->name('notas_creditos.show');
        Route::get('notas_creditos/agregar', 'NotaCreditoController@create')->name('notas_creditos.create');
        Route::get('notas_creditos/agregar_unico/{id}', 'NotaCreditoController@create_unique')->name('notas_creditos.create_unique');
        Route::post('notas_creditos/agregar', 'NotaCreditoController@store')->name('notas_creditos.store');
        Route::post('notas_creditos/inactivar/{id}', 'NotaCreditoController@unactivate')->name('notas_creditos.unactivate');
        Route::post('notas_creditos/activar/{id}', 'NotaCreditoController@activate')->name('notas_creditos.activate');
        Route::get('notas_creditos/eliminar/{id}', 'NotaCreditoController@destroy')->name('notas_creditos.get_destroy');
        Route::delete('notas_creditos/eliminar/{id}', 'NotaCreditoController@destroy')->name('notas_creditos.destroy');
        Route::get('notas_creditos/get_ventas/{id}', 'NotaCreditoController@get_ventas')->name('notas_creditos.get_ventas');
    //Arqueos Generados
        Route::get('arqueos_cajas', 'ArqueoCajaController@index')->name('arqueos_cajas.index');

//Compras
    //Ordenes de Compras
        Route::get('ordenes_compras', 'OrdenCompraController@index')->name('ordenes_compras.index');
        Route::get('ordenes_compras/ver/{id}', 'OrdenCompraController@show')->name('ordenes_compras.show');
        Route::get('ordenes_compras/agregar', 'OrdenCompraController@create')->name('ordenes_compras.create');
        Route::post('ordenes_compras/agregar', 'OrdenCompraController@store')->name('ordenes_compras.store');
        Route::get('ordenes_compras/editar/{id}', 'OrdenCompraController@edit')->name('ordenes_compras.edit');
        Route::post('ordenes_compras/actualizar/{id}', 'OrdenCompraController@update')->name('ordenes_compras.update');
        Route::post('ordenes_compras/inactivar/{id}', 'OrdenCompraController@unactivate')->name('ordenes_compras.unactivate');
        Route::post('ordenes_compras/activar/{id}', 'OrdenCompraController@activate')->name('ordenes_compras.activate');
        Route::post('ordenes_compras/desaprobar/{id}', 'OrdenCompraController@unapprove')->name('ordenes_compras.unapprove');
        Route::post('ordenes_compras/aprobar/{id}', 'OrdenCompraController@approve')->name('ordenes_compras.approve');
        Route::get('ordenes_compras/eliminar/{id}', 'OrdenCompraController@destroy')->name('ordenes_compras.destroy');
        Route::delete('ordenes_compras/eliminar/{id}', 'OrdenCompraController@destroy');
        Route::get('ordenes_compras/imprimir/{id}', 'OrdenCompraController@imprimir')->name('ordenes_compras.imprimir');
    //Compras
        Route::get('compras', 'CompraController@index')->name('compras.index');
        Route::get('compras/ver/{id}', 'CompraController@show')->name('compras.show');
        Route::get('compras/agregar', 'CompraController@create')->name('compras.create');
        Route::post('compras/agregar', 'CompraController@store')->name('compras.store');
        Route::get('compras/editar/{id}', 'CompraController@edit')->name('compras.edit');
        Route::post('compras/actualizar/{id}', 'CompraController@update')->name('compras.update');
        Route::post('compras/inactivar/{id}', 'CompraController@unactivate')->name('compras.unactivate');
        Route::get('compras/eliminar/{id}', 'CompraController@destroy')->name('compras.destroy');
        Route::delete('compras/eliminar/{id}', 'CompraController@destroy');
        Route::get('compras/get_timbrados_proveedores/{id}', 'CompraController@get_timbrados_proveedores')->name('compras.get_timbrados_proveedores');
        Route::get('compras/get_subunidades_negocios/{id}', 'CompraController@get_subunidades_negocios')->name('compras.get_subunidades_negocios');
        Route::get('compras/get_subcentros_costos/{id}', 'CompraController@get_subcentros_costos')->name('compras.get_subcentros_costos');
        Route::get('compras/get_ordenes_compras/{id}', 'CompraController@get_ordenes_compras')->name('compras.get_ordenes_compras');
        Route::get('compras/get_orden_compra/{id}', 'CompraController@get_orden_compra')->name('compras.get_orden_compra');
        Route::get('compras/agregar_desde_orden/{id}', 'CompraController@create_from_orden')->name('compras.create_from_orden');
    //Ordenes de Pagos
        Route::get('ordenes_pagos', 'OrdenPagoController@index')->name('ordenes_pagos.index');
        Route::get('ordenes_pagos/ver/{id}', 'OrdenPagoController@show')->name('ordenes_pagos.show');
        Route::get('ordenes_pagos/agregar/{tipo}', 'OrdenPagoController@create')->name('ordenes_pagos.create');
        Route::post('ordenes_pagos/agregar_proveedor', 'OrdenPagoController@store_proveedor')->name('ordenes_pagos.store_proveedor');
        Route::post('ordenes_pagos/agregar_anticipo', 'OrdenPagoController@store_anticipo')->name('ordenes_pagos.store_anticipo');
        Route::post('ordenes_pagos/agregar_gerencia', 'OrdenPagoController@store_gerencia')->name('ordenes_pagos.store_gerencia');
        Route::post('ordenes_pagos/inactivar/{id}', 'OrdenPagoController@unactivate')->name('ordenes_pagos.unactivate');
        Route::post('ordenes_pagos/activar/{id}', 'OrdenPagoController@activate')->name('ordenes_pagos.activate');
        Route::post('ordenes_pagos/desaprobar/{id}', 'OrdenPagoController@unapprove')->name('ordenes_pagos.unapprove');
        Route::post('ordenes_pagos/aprobar/{id}', 'OrdenPagoController@approve')->name('ordenes_pagos.approve');
        Route::get('ordenes_pagos/eliminar/{id}', 'OrdenPagoController@destroy')->name('ordenes_pagos.destroy');
        Route::delete('ordenes_pagos/eliminar/{id}', 'OrdenPagoController@destroy');
        Route::get('ordenes_pagos/imprimir/{id}/{tipo}', 'OrdenPagoController@imprimir')->name('ordenes_pagos.imprimir');
        Route::get('ordenes_pagos/agregar_desde_compra/{id}', 'OrdenPagoController@create_from_compra')->name('compras.create_from_compra');
        Route::get('ordenes_pagos/get_compra/{id}', 'OrdenPagoController@get_compra')->name('ordenes_pagos.get_compra');
        Route::get('ordenes_pagos/get_subunidades_negocios/{id}', 'OrdenPagoController@get_subunidades_negocios')->name('ordenes_pagos.get_subunidades_negocios');
    //Pagos
        Route::get('pagos', 'PagoController@index')->name('pagos.index');
        Route::post('pagos/agregar/{id}', 'PagoController@store')->name('pagos.store');
        Route::post('pagos/anular/{id}', 'PagoController@unactivate')->name('pagos.unactivate');
    //Proveedores
        Route::get('proveedores', 'ProveedorController@index')->name('proveedores.index');
        Route::get('proveedores/ver/{id}', 'ProveedorController@show')->name('proveedores.show');
        Route::get('proveedores/agregar', 'ProveedorController@create')->name('proveedores.create');
        Route::post('proveedores/agregar', 'ProveedorController@store')->name('proveedores.store');
        Route::get('proveedores/editar/{id}', 'ProveedorController@edit')->name('proveedores.edit');
        Route::post('proveedores/actualizar/{id}', 'ProveedorController@update')->name('proveedores.update');
        Route::post('proveedores/inactivar/{id}', 'ProveedorController@unactivate')->name('proveedores.unactivate');
        Route::post('proveedores/activar/{id}', 'ProveedorController@activate')->name('proveedores.activate');
        Route::get('proveedores/eliminar/{id}', 'ProveedorController@destroy')->name('proveedores.get_destroy');
        Route::delete('proveedores/eliminar/{id}', 'ProveedorController@destroy')->name('proveedores.destroy');
    //Timbrados de Proveedores
        Route::get('timbrados_proveedores', 'TimbradoProveedorController@index')->name('timbrados_proveedores.index');
        Route::get('timbrados_proveedores/all', 'TimbradoProveedorController@index_ajax')->name('timbrados_proveedores.index_ajax');
        Route::get('timbrados_proveedores/show/{id}', 'TimbradoProveedorController@show')->name('timbrados_proveedores.show');
        Route::post('timbrados_proveedores', 'TimbradoProveedorController@store')->name('timbrados_proveedores.store');
        Route::get('timbrados_proveedores/{id}', 'TimbradoProveedorController@edit')->name('timbrados_proveedores.edit');
        Route::post('timbrados_proveedores/{id}', 'TimbradoProveedorController@update')->name('timbrados_proveedores.update');
        Route::get('timbrados_proveedores/inactivar/{id}', 'TimbradoProveedorController@get_unactivate')->name('timbrados_proveedores.get_unactivate');
        Route::post('timbrados_proveedores/inactivar/{id}', 'TimbradoProveedorController@unactivate')->name('timbrados_proveedores.unactivate');
        Route::get('timbrados_proveedores/activar/{id}', 'TimbradoProveedorController@get_activate')->name('timbrados_proveedores.get_activate');
        Route::post('timbrados_proveedores/activar/{id}', 'TimbradoProveedorController@activate')->name('timbrados_proveedores.activate');
        Route::get('timbrados_proveedores/eliminar/{id}', 'TimbradoProveedorController@get_destroy')->name('timbrados_proveedores.get_destroy');
        Route::delete('timbrados_proveedores/eliminar/{id}', 'TimbradoProveedorController@destroy')->name('timbrados_proveedores.destroy');
    //Categorias de Proveedores
        Route::get('categorias_proveedores', 'CategoriaProveedorController@index')->name('categorias_proveedores.index');
        Route::get('categorias_proveedores/all', 'CategoriaProveedorController@index_ajax')->name('categorias_proveedores.index_ajax');
        Route::get('categorias_proveedores/show/{id}', 'CategoriaProveedorController@show')->name('categorias_proveedores.show');
        Route::post('categorias_proveedores', 'CategoriaProveedorController@store')->name('categorias_proveedores.store');
        Route::get('categorias_proveedores/{id}', 'CategoriaProveedorController@edit')->name('categorias_proveedores.edit');
        Route::post('categorias_proveedores/{id}', 'CategoriaProveedorController@update')->name('categorias_proveedores.update');
        Route::get('categorias_proveedores/inactivar/{id}', 'CategoriaProveedorController@get_unactivate')->name('categorias_proveedores.get_unactivate');
        Route::post('categorias_proveedores/inactivar/{id}', 'CategoriaProveedorController@unactivate')->name('categorias_proveedores.unactivate');
        Route::get('categorias_proveedores/activar/{id}', 'CategoriaProveedorController@get_activate')->name('categorias_proveedores.get_activate');
        Route::post('categorias_proveedores/activar/{id}', 'CategoriaProveedorController@activate')->name('categorias_proveedores.activate');
        Route::get('categorias_proveedores/eliminar/{id}', 'CategoriaProveedorController@get_destroy')->name('categorias_proveedores.get_destroy');
        Route::delete('categorias_proveedores/eliminar/{id}', 'CategoriaProveedorController@destroy')->name('categorias_proveedores.destroy');
//Contabilidad
    //Asientos
        Route::get('asientos_contables', 'AsientoContableController@index')->name('asientos_contables.index');
        Route::get('asientos_contables/ver/{id}', 'AsientoContableController@show')->name('asientos_contables.show');
        Route::get('asientos_contables/agregar', 'AsientoContableController@create')->name('asientos_contables.create');
        Route::post('asientos_contables/agregar', 'AsientoContableController@store')->name('asientos_contables.store');
        Route::get('asientos_contables/editar/{id}', 'AsientoContableController@edit')->name('asientos_contables.edit');
        Route::post('asientos_contables/actualizar/{id}', 'AsientoContableController@update')->name('asientos_contables.update');
        Route::get('asientos_contables/eliminar/{id}', 'AsientoContableController@destroy')->name('asientos_contables.get_destroy');
        Route::delete('asientos_contables/eliminar/{id}', 'AsientoContableController@destroy')->name('asientos_contables.destroy');
        Route::get('asientos_contables/get_subunidades_negocios/{id}', 'AsientoContableController@get_subunidades_negocios')->name('asientos_contables.get_subunidades_negocios');
        Route::get('asientos_contables/get_subcentros_costos/{id}', 'AsientoContableController@get_subcentros_costos')->name('asientos_contables.get_subcentros_costos');
        Route::post('asientos_contables/renumerar', 'AsientoContableController@renumerar')->name('asientos_contables.renumerar');
    //Articulos
        Route::get('articulos', 'ArticuloController@index')->name('articulos.index');
        Route::get('articulos/ver/{id}', 'ArticuloController@show')->name('articulos.show');
        Route::get('articulos/agregar', 'ArticuloController@create')->name('articulos.create');
        Route::post('articulos/agregar', 'ArticuloController@store')->name('articulos.store');
        Route::get('articulos/editar/{id}', 'ArticuloController@edit')->name('articulos.edit');
        Route::post('articulos/actualizar/{id}', 'ArticuloController@update')->name('articulos.update');
        Route::post('articulos/inactivar/{id}', 'ArticuloController@unactivate')->name('articulos.unactivate');
        Route::post('articulos/activar/{id}', 'ArticuloController@activate')->name('articulos.activate');
        Route::get('articulos/eliminar/{id}', 'ArticuloController@destroy')->name('articulos.get_destroy');
        Route::delete('articulos/eliminar/{id}', 'ArticuloController@destroy')->name('articulos.destroy');
        Route::get('articulos/get_subunidades_negocios/{id}', 'ArticuloController@get_subunidades_negocios')->name('articulos.get_subunidades_negocios');
        Route::get('articulos/get_subcentros_costos/{id}', 'ArticuloController@get_subcentros_costos')->name('articulos.get_subcentros_costos');
    //Saldo de Cuentas Contables
        Route::get('cuentas_contables_saldos', 'CuentaContableSaldoController@index')->name('cuentas_contables_saldos.index');
        Route::post('cuentas_contables_saldos', 'CuentaContableSaldoController@index');
    //Cuentas Contables
        Route::get('cuentas_contables', 'CuentaContableController@index')->name('cuentas_contables.index');
        Route::get('cuentas_contables/ver/{id}', 'CuentaContableController@show')->name('cuentas_contables.show');
        Route::get('cuentas_contables/agregar', 'CuentaContableController@create')->name('cuentas_contables.create');
        Route::post('cuentas_contables/agregar', 'CuentaContableController@store')->name('cuentas_contables.store');
        Route::get('cuentas_contables/editar/{id}', 'CuentaContableController@edit')->name('cuentas_contables.edit');
        Route::post('cuentas_contables/actualizar/{id}', 'CuentaContableController@update')->name('cuentas_contables.update');
        Route::post('cuentas_contables/inactivar/{id}', 'CuentaContableController@unactivate')->name('cuentas_contables.unactivate');
        Route::post('cuentas_contables/activar/{id}', 'CuentaContableController@activate')->name('cuentas_contables.activate');
        Route::get('cuentas_contables/eliminar/{id}', 'CuentaContableController@destroy')->name('cuentas_contables.get_destroy');
        Route::delete('cuentas_contables/eliminar/{id}', 'CuentaContableController@destroy')->name('cuentas_contables.destroy');
    //Centros de Costos
        Route::get('centros_costos', 'CentroCostoContableController@index')->name('centros_costos_contables.index');
        Route::get('centros_costos/ver/{id}', 'CentroCostoContableController@show')->name('centros_costos_contables.show');
        Route::get('centros_costos/agregar', 'CentroCostoContableController@create')->name('centros_costos_contables.create');
        Route::post('centros_costos/agregar', 'CentroCostoContableController@store')->name('centros_costos_contables.store');
        Route::get('centros_costos/editar/{id}', 'CentroCostoContableController@edit')->name('centros_costos_contables.edit');
        Route::post('centros_costos/actualizar/{id}', 'CentroCostoContableController@update')->name('centros_costos_contables.update');
        Route::post('centros_costos/inactivar/{id}', 'CentroCostoContableController@unactivate')->name('centros_costos_contables.unactivate');
        Route::post('centros_costos/activar/{id}', 'CentroCostoContableController@activate')->name('centros_costos_contables.activate');
        Route::delete('centros_costos/eliminar/{id}', 'CentroCostoContableController@destroy')->name('centros_costos_contables.destroy');
        Route::post('subcentros_costos/cambiar_estado/{id}', 'CentroCostoContableController@change_estado_subcentro')->name('subcentros_costos_contables.change_estado');
    //Unidades de Negocio
        Route::get('unidades_negocios', 'UnidadNegocioContableController@index')->name('unidades_negocios_contables.index');
        Route::get('unidades_negocios/ver/{id}', 'UnidadNegocioContableController@show')->name('unidades_negocios_contables.show');
        Route::get('unidades_negocios/agregar', 'UnidadNegocioContableController@create')->name('unidades_negocios_contables.create');
        Route::post('unidades_negocios/agregar', 'UnidadNegocioContableController@store')->name('unidades_negocios_contables.store');
        Route::get('unidades_negocios/editar/{id}', 'UnidadNegocioContableController@edit')->name('unidades_negocios_contables.edit');
        Route::post('unidades_negocios/actualizar/{id}', 'UnidadNegocioContableController@update')->name('unidades_negocios_contables.update');
        Route::post('unidades_negocios/inactivar/{id}', 'UnidadNegocioContableController@unactivate')->name('unidades_negocios_contables.unactivate');
        Route::post('unidades_negocios/activar/{id}', 'UnidadNegocioContableController@activate')->name('unidades_negocios_contables.activate');
        Route::delete('unidades_negocios/eliminar/{id}', 'UnidadNegocioContableController@destroy')->name('unidades_negocios_contables.destroy');
        Route::post('subunidades_negocios/cambiar_estado/{id}', 'UnidadNegocioContableController@change_estado_subunidad')->name('subunidades_negocios_contables.change_estado');
    //Tipos de Documentos Contables
        Route::get('tipos_documentos_contables', 'TipoDocumentoContableController@index')->name('tipos_documentos_contables.index');
        Route::get('tipos_documentos_contables/all', 'TipoDocumentoContableController@index_ajax')->name('tipos_documentos_contables.index_ajax');
        Route::get('tipos_documentos_contables/ver/{id}', 'TipoDocumentoContableController@show')->name('tipos_documentos_contables.show');
        Route::post('tipos_documentos_contables', 'TipoDocumentoContableController@store')->name('tipos_documentos_contables.store');
        Route::get('tipos_documentos_contables/{id}', 'TipoDocumentoContableController@edit')->name('tipos_documentos_contables.edit');
        Route::post('tipos_documentos_contables/{id}', 'TipoDocumentoContableController@update')->name('tipos_documentos_contables.update');
        Route::get('tipos_documentos_contables/inactivar/{id}', 'TipoDocumentoContableController@get_unactivate')->name('tipos_documentos_contables.get_unactivate');
        Route::post('tipos_documentos_contables/inactivar/{id}', 'TipoDocumentoContableController@unactivate')->name('tipos_documentos_contables.unactivate');
        Route::get('tipos_documentos_contables/activar/{id}', 'TipoDocumentoContableController@get_activate')->name('tipos_documentos_contables.get_activate');
        Route::post('tipos_documentos_contables/activar/{id}', 'TipoDocumentoContableController@activate')->name('tipos_documentos_contables.activate');
        Route::get('tipos_documentos_contables/eliminar/{id}', 'TipoDocumentoContableController@get_destroy')->name('tipos_documentos_contables.get_destroy');
        Route::delete('tipos_documentos_contables/eliminar/{id}', 'TipoDocumentoContableController@destroy')->name('tipos_documentos_contables.destroy');

//Tesoreria
    //Cajas
        Route::get('cajas', 'CajaController@index')->name('cajas.index');
        Route::get('cajas/all', 'CajaController@index_ajax')->name('cajas.index_ajax');
        Route::get('cajas/ver/{id}', 'CajaController@show')->name('cajas.show');
        Route::post('cajas', 'CajaController@store')->name('cajas.store');
        Route::get('cajas/{id}', 'CajaController@edit')->name('cajas.edit');
        Route::post('cajas/{id}', 'CajaController@update')->name('cajas.update');
        Route::get('cajas/inactivar/{id}', 'CajaController@get_unactivate')->name('cajas.get_unactivate');
        Route::post('cajas/inactivar/{id}', 'CajaController@unactivate')->name('cajas.unactivate');
        Route::get('cajas/activar/{id}', 'CajaController@get_activate')->name('cajas.get_activate');
        Route::post('cajas/activar/{id}', 'CajaController@activate')->name('cajas.activate');
        Route::get('cajas/eliminar/{id}', 'CajaController@get_destroy')->name('cajas.get_destroy');
        Route::delete('cajas/eliminar/{id}', 'CajaController@destroy')->name('cajas.destroy');
    //Cuentas Bancarias
        Route::get('cuentas_bancarias', 'CuentaBancariaController@index')->name('cuentas_bancarias.index');
        Route::get('cuentas_bancarias/all', 'CuentaBancariaController@index_ajax')->name('cuentas_bancarias.index_ajax');
        Route::get('cuentas_bancarias/ver/{id}', 'CuentaBancariaController@show')->name('cuentas_bancarias.show');
        Route::post('cuentas_bancarias', 'CuentaBancariaController@store')->name('cuentas_bancarias.store');
        Route::get('cuentas_bancarias/{id}', 'CuentaBancariaController@edit')->name('cuentas_bancarias.edit');
        Route::post('cuentas_bancarias/{id}', 'CuentaBancariaController@update')->name('cuentas_bancarias.update');
        Route::get('cuentas_bancarias/inactivar/{id}', 'CuentaBancariaController@get_unactivate')->name('cuentas_bancarias.get_unactivate');
        Route::post('cuentas_bancarias/inactivar/{id}', 'CuentaBancariaController@unactivate')->name('cuentas_bancarias.unactivate');
        Route::get('cuentas_bancarias/activar/{id}', 'CuentaBancariaController@get_activate')->name('cuentas_bancarias.get_activate');
        Route::post('cuentas_bancarias/activar/{id}', 'CuentaBancariaController@activate')->name('cuentas_bancarias.activate');
        Route::get('cuentas_bancarias/eliminar/{id}', 'CuentaBancariaController@get_destroy')->name('cuentas_bancarias.get_destroy');
        Route::delete('cuentas_bancarias/eliminar/{id}', 'CuentaBancariaController@destroy')->name('cuentas_bancarias.destroy');
    //Movimientos
        //Movimientos de Cajas
            Route::get('movimientos_cajas', 'MovimientoCajaController@index')->name('movimientos_cajas.index');
            Route::get('movimientos_cajas/ver/{id}', 'MovimientoCajaController@show')->name('movimientos_cajas.show');
            Route::get('movimientos_cajas/agregar', 'MovimientoCajaController@create')->name('movimientos_cajas.create');
            Route::post('movimientos_cajas/agregar', 'MovimientoCajaController@store')->name('movimientos_cajas.store');
            Route::get('movimientos_cajas/editar/{id}', 'MovimientoCajaController@edit')->name('movimientos_cajas.edit');
            Route::post('movimientos_cajas/actualizar/{id}', 'MovimientoCajaController@update')->name('movimientos_cajas.update');
            Route::post('movimientos_cajas/aprobar/{id}', 'MovimientoCajaController@approve')->name('movimientos_cajas.approve');
            Route::post('movimientos_cajas/rechazar/{id}', 'MovimientoCajaController@reject')->name('movimientos_cajas.reject');
            Route::post('movimientos_cajas/anular_rechazo/{id}', 'MovimientoCajaController@unreject')->name('movimientos_cajas.unreject');
            Route::post('movimientos_cajas/desaprobar/{id}', 'MovimientoCajaController@unapprove')->name('movimientos_cajas.unapprove');
            Route::get('movimientos_cajas/eliminar/{id}', 'MovimientoCajaController@destroy')->name('movimientos_cajas.get_destroy');
            Route::delete('movimientos_cajas/eliminar/{id}', 'MovimientoCajaController@destroy')->name('movimientos_cajas.destroy');
        //Movimientos de Bancos
            Route::get('movimientos_bancos', 'MovimientoBancoController@index')->name('movimientos_bancos.index');
            Route::get('movimientos_bancos/ver/{id}', 'MovimientoBancoController@show')->name('movimientos_bancos.show');
            Route::get('movimientos_bancos/agregar', 'MovimientoBancoController@create')->name('movimientos_bancos.create');
            Route::post('movimientos_bancos/agregar', 'MovimientoBancoController@store')->name('movimientos_bancos.store');
            Route::get('movimientos_bancos/editar/{id}', 'MovimientoBancoController@edit')->name('movimientos_bancos.edit');
            Route::post('movimientos_bancos/actualizar/{id}', 'MovimientoBancoController@update')->name('movimientos_bancos.update');
            Route::post('movimientos_bancos/aprobar/{id}', 'MovimientoBancoController@approve')->name('movimientos_bancos.approve');
            Route::post('movimientos_bancos/rechazar/{id}', 'MovimientoBancoController@reject')->name('movimientos_bancos.reject');
            Route::post('movimientos_bancos/anular_rechazo/{id}', 'MovimientoBancoController@unreject')->name('movimientos_bancos.unreject');
            Route::post('movimientos_bancos/desaprobar/{id}', 'MovimientoBancoController@unapprove')->name('movimientos_bancos.unapprove');
            Route::get('movimientos_bancos/eliminar/{id}', 'MovimientoBancoController@destroy')->name('movimientos_bancos.get_destroy');
            Route::delete('movimientos_bancos/eliminar/{id}', 'MovimientoBancoController@destroy')->name('movimientos_bancos.destroy');
        //Movimientos entre Cajas y Bancos
            Route::get('movimientos_cajas_bancos', 'MovimientoCajaBancoController@index')->name('movimientos_cajas_bancos.index');
            Route::get('movimientos_cajas_bancos/ver/{id}', 'MovimientoCajaBancoController@show')->name('movimientos_cajas_bancos.show');
            Route::get('movimientos_cajas_bancos/agregar', 'MovimientoCajaBancoController@create')->name('movimientos_cajas_bancos.create');
            Route::post('movimientos_cajas_bancos/agregar', 'MovimientoCajaBancoController@store')->name('movimientos_cajas_bancos.store');
            Route::get('movimientos_cajas_bancos/editar/{id}', 'MovimientoCajaBancoController@edit')->name('movimientos_cajas_bancos.edit');
            Route::post('movimientos_cajas_bancos/actualizar/{id}', 'MovimientoCajaBancoController@update')->name('movimientos_cajas_bancos.update');
            Route::post('movimientos_cajas_bancos/aprobar/{id}', 'MovimientoCajaBancoController@approve')->name('movimientos_cajas_bancos.approve');
            Route::post('movimientos_cajas_bancos/rechazar/{id}', 'MovimientoCajaBancoController@reject')->name('movimientos_cajas_bancos.reject');
            Route::post('movimientos_cajas_bancos/anular_rechazo/{id}', 'MovimientoCajaBancoController@unreject')->name('movimientos_cajas_bancos.unreject');
            Route::post('movimientos_cajas_bancos/desaprobar/{id}', 'MovimientoCajaBancoController@unapprove')->name('movimientos_cajas_bancos.unapprove');
            Route::get('movimientos_cajas_bancos/eliminar/{id}', 'MovimientoCajaBancoController@destroy')->name('movimientos_cajas_bancos.get_destroy');
            Route::delete('movimientos_cajas_bancos/eliminar/{id}', 'MovimientoCajaBancoController@destroy')->name('movimientos_cajas_bancos.destroy');
    //Bancos
        Route::get('bancos', 'BancoController@index')->name('bancos.index');
        Route::get('bancos/all', 'BancoController@index_ajax')->name('bancos.index_ajax');
        Route::get('bancos/show/{id}', 'BancoController@show')->name('bancos.show');
        Route::post('bancos', 'BancoController@store')->name('bancos.store');
        Route::get('bancos/{id}', 'BancoController@edit')->name('bancos.edit');
        Route::post('bancos/{id}', 'BancoController@update')->name('bancos.update');
        Route::get('bancos/inactivar/{id}', 'BancoController@get_unactivate')->name('bancos.get_unactivate');
        Route::post('bancos/inactivar/{id}', 'BancoController@unactivate')->name('bancos.unactivate');
        Route::get('bancos/activar/{id}', 'BancoController@get_activate')->name('bancos.get_activate');
        Route::post('bancos/activar/{id}', 'BancoController@activate')->name('bancos.activate');
        Route::get('bancos/eliminar/{id}', 'BancoController@get_destroy')->name('bancos.get_destroy');
        Route::delete('bancos/eliminar/{id}', 'BancoController@destroy')->name('bancos.destroy');
    //Cotizaciones
        Route::get('cotizaciones', 'CotizacionController@index')->name('cotizaciones.index');
        Route::get('cotizaciones/all', 'CotizacionController@index_ajax')->name('cotizaciones.index_ajax');
        Route::get('cotizaciones/show/{id}', 'CotizacionController@show')->name('cotizaciones.show');
        Route::post('cotizaciones', 'CotizacionController@store')->name('cotizaciones.store');
        Route::get('cotizaciones/{id}', 'CotizacionController@edit')->name('cotizaciones.edit');
        Route::post('cotizaciones/{id}', 'CotizacionController@update')->name('cotizaciones.update');
        Route::get('cotizaciones/inactivar/{id}', 'CotizacionController@get_unactivate')->name('cotizaciones.get_unactivate');
        Route::post('cotizaciones/inactivar/{id}', 'CotizacionController@unactivate')->name('cotizaciones.unactivate');
        Route::get('cotizaciones/activar/{id}', 'CotizacionController@get_activate')->name('cotizaciones.get_activate');
        Route::post('cotizaciones/activar/{id}', 'CotizacionController@activate')->name('cotizaciones.activate');
        Route::get('cotizaciones/eliminar/{id}', 'CotizacionController@get_destroy')->name('cotizaciones.get_destroy');
        Route::delete('cotizaciones/eliminar/{id}', 'CotizacionController@destroy')->name('cotizaciones.destroy');
    //Convenios
        Route::get('convenios', 'ConvenioController@index')->name('convenios.index');
        Route::get('convenios/ver/{ID}', 'ConvenioController@show')->name('convenios.show');
        Route::get('convenios/agregar', 'ConvenioController@create')->name('convenios.create');
        Route::post('convenios/agregar', 'ConvenioController@store')->name('convenios.store');
        Route::get('convenios/editar/{id}', 'ConvenioController@edit')->name('convenios.edit');
        Route::post('convenios/actualizar/{id}', 'ConvenioController@update')->name('convenios.update');
        Route::post('convenios/inactivar/{id}', 'ConvenioController@unactivate')->name('convenios.unactivate');
        Route::post('convenios/activar/{id}', 'ConvenioController@activate')->name('convenios.activate');
        Route::get('convenios/eliminar/{id}', 'ConvenioController@destroy')->name('convenios.get_destroy');
        Route::delete('convenios/eliminar/{id}', 'ConvenioController@destroy')->name('convenios.destroy');
    //Formas de Pagos
        Route::get('formas_pagos', 'FormaPagoController@index')->name('formas_pagos.index');
        Route::get('formas_pagos/all', 'FormaPagoController@index_ajax')->name('formas_pagos.index_ajax');
        Route::get('formas_pagos/show/{id}', 'FormaPagoController@show')->name('formas_pagos.show');
        Route::post('formas_pagos', 'FormaPagoController@store')->name('formas_pagos.store');
        Route::get('formas_pagos/{id}', 'FormaPagoController@edit')->name('formas_pagos.edit');
        Route::post('formas_pagos/{id}', 'FormaPagoController@update')->name('formas_pagos.update');
        Route::get('formas_pagos/inactivar/{id}', 'FormaPagoController@get_unactivate')->name('formas_pagos.get_unactivate');
        Route::post('formas_pagos/inactivar/{id}', 'FormaPagoController@unactivate')->name('formas_pagos.unactivate');
        Route::get('formas_pagos/activar/{id}', 'FormaPagoController@get_activate')->name('formas_pagos.get_activate');
        Route::post('formas_pagos/activar/{id}', 'FormaPagoController@activate')->name('formas_pagos.activate');
        Route::get('formas_pagos/eliminar/{id}', 'FormaPagoController@get_destroy')->name('formas_pagos.get_destroy');
        Route::delete('formas_pagos/eliminar/{id}', 'FormaPagoController@destroy')->name('formas_pagos.destroy');
    //Monedas
        Route::get('monedas', 'MonedaController@index')->name('monedas.index');
        Route::get('monedas/all', 'MonedaController@index_ajax')->name('monedas.index_ajax');
        Route::get('monedas/show/{id}', 'MonedaController@show')->name('monedas.show');
        Route::post('monedas', 'MonedaController@store')->name('monedas.store');
        Route::get('monedas/{id}', 'MonedaController@edit')->name('monedas.edit');
        Route::post('monedas/{id}', 'MonedaController@update')->name('monedas.update');
        Route::get('monedas/inactivar/{id}', 'MonedaController@get_unactivate')->name('monedas.get_unactivate');
        Route::post('monedas/inactivar/{id}', 'MonedaController@unactivate')->name('monedas.unactivate');
        Route::get('monedas/activar/{id}', 'MonedaController@get_activate')->name('monedas.get_activate');
        Route::post('monedas/activar/{id}', 'MonedaController@activate')->name('monedas.activate');
        Route::get('monedas/eliminar/{id}', 'MonedaController@get_destroy')->name('monedas.get_destroy');
        Route::delete('monedas/eliminar/{id}', 'MonedaController@destroy')->name('monedas.destroy');

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

//Parametros
    //Empresa
        //Datos de la Empresa
            Route::get('empresa', 'EmpresaController@index')->name('empresas.index');
            Route::get('empresa/agregar', 'EmpresaController@index')->name('empresas.create');
            Route::post('empresa/agregar', 'EmpresaController@store')->name('empresas.store');
            Route::get('empresa/actualizar', 'EmpresaController@index')->name('empresas.edit');
            Route::post('empresa/actualizar', 'EmpresaController@update')->name('empresas.update');
        //Puntos de Impresion
            Route::get('puntos_impresiones', 'PuntoImpresionController@index')->name('puntos_impresiones.index');
            Route::get('puntos_impresiones/all', 'PuntoImpresionController@index_ajax')->name('puntos_impresiones.index_ajax');
            Route::get('puntos_impresiones/show/{id}', 'PuntoImpresionController@show')->name('puntos_impresiones.show');
            Route::post('puntos_impresiones', 'PuntoImpresionController@store')->name('puntos_impresiones.store');
            Route::get('puntos_impresiones/{id}', 'PuntoImpresionController@edit')->name('puntos_impresiones.edit');
            Route::post('puntos_impresiones/{id}', 'PuntoImpresionController@update')->name('puntos_impresiones.update');
            Route::get('puntos_impresiones/inactivar/{id}', 'PuntoImpresionController@get_unactivate')->name('puntos_impresiones.get_unactivate');
            Route::post('puntos_impresiones/inactivar/{id}', 'PuntoImpresionController@unactivate')->name('puntos_impresiones.unactivate');
            Route::get('puntos_impresiones/activar/{id}', 'PuntoImpresionController@get_activate')->name('puntos_impresiones.get_activate');
            Route::post('puntos_impresiones/activar/{id}', 'PuntoImpresionController@activate')->name('puntos_impresiones.activate');
            Route::get('puntos_impresiones/eliminar/{id}', 'PuntoImpresionController@get_destroy')->name('puntos_impresiones.get_destroy');
            Route::delete('puntos_impresiones/eliminar/{id}', 'PuntoImpresionController@destroy')->name('puntos_impresiones.destroy');
        //Timbrados
            Route::get('timbrados', 'TimbradoController@index')->name('timbrados.index');
            Route::get('timbrados/all', 'TimbradoController@index_ajax')->name('timbrados.index_ajax');
            Route::get('timbrados/show/{id}', 'TimbradoController@show')->name('timbrados.show');
            Route::post('timbrados', 'TimbradoController@store')->name('timbrados.store');
            Route::get('timbrados/{id}', 'TimbradoController@edit')->name('timbrados.edit');
            Route::post('timbrados/{id}', 'TimbradoController@update')->name('timbrados.update');
            Route::get('timbrados/inactivar/{id}', 'TimbradoController@get_unactivate')->name('timbrados.get_unactivate');
            Route::post('timbrados/inactivar/{id}', 'TimbradoController@unactivate')->name('timbrados.unactivate');
            Route::get('timbrados/activar/{id}', 'TimbradoController@get_activate')->name('timbrados.get_activate');
            Route::post('timbrados/activar/{id}', 'TimbradoController@activate')->name('timbrados.activate');
            Route::get('timbrados/eliminar/{id}', 'TimbradoController@get_destroy')->name('timbrados.get_destroy');
            Route::delete('timbrados/eliminar/{id}', 'TimbradoController@destroy')->name('timbrados.destroy');
    //Nacionalidades
        Route::get('nacionalidades', 'NacionalidadController@index')->name('nacionalidades.index');
        Route::get('nacionalidades/all', 'NacionalidadController@index_ajax')->name('nacionalidades.index_ajax');
        Route::get('nacionalidades/show/{id}', 'NacionalidadController@show')->name('nacionalidades.show');
        Route::post('nacionalidades', 'NacionalidadController@store')->name('nacionalidades.store');
        Route::get('nacionalidades/{id}', 'NacionalidadController@edit')->name('nacionalidades.edit');
        Route::post('nacionalidades/{id}', 'NacionalidadController@update')->name('nacionalidades.update');
        Route::get('nacionalidades/eliminar/{id}', 'NacionalidadController@get_destroy')->name('nacionalidades.get_destroy');
        Route::delete('nacionalidades/eliminar/{id}', 'NacionalidadController@destroy')->name('nacionalidades.destroy');
    //Paises
        Route::get('paises', 'PaisController@index')->name('paises.index');
        Route::get('paises/all', 'PaisController@index_ajax')->name('paises.index_ajax');
        Route::get('paises/show/{id}', 'PaisController@show')->name('paises.show');
        Route::post('paises', 'PaisController@store')->name('paises.store');
        Route::get('paises/{id}', 'PaisController@edit')->name('paises.edit');
        Route::post('paises/{id}', 'PaisController@update')->name('paises.update');
        Route::get('paises/eliminar/{id}', 'PaisController@get_destroy')->name('paises.get_destroy');
        Route::delete('paises/eliminar/{id}', 'PaisController@destroy')->name('paises.destroy');
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
    //Tipos de Movimientos
        Route::get('tipos_movimientos', 'TipoMovimientoController@index')->name('tipos_movimientos.index');
        Route::get('tipos_movimientos/all', 'TipoMovimientoController@index_ajax')->name('tipos_movimientos.index_ajax');
        Route::get('tipos_movimientos/ver/{id}', 'TipoMovimientoController@show')->name('tipos_movimientos.show');
        Route::post('tipos_movimientos', 'TipoMovimientoController@store')->name('tipos_movimientos.store');
        Route::get('tipos_movimientos/{id}', 'TipoMovimientoController@edit')->name('tipos_movimientos.edit');
        Route::post('tipos_movimientos/{id}', 'TipoMovimientoController@update')->name('tipos_movimientos.update');
        Route::get('tipos_movimientos/inactivar/{id}', 'TipoMovimientoController@get_unactivate')->name('tipos_movimientos.get_unactivate');
        Route::post('tipos_movimientos/inactivar/{id}', 'TipoMovimientoController@unactivate')->name('tipos_movimientos.unactivate');
        Route::get('tipos_movimientos/activar/{id}', 'TipoMovimientoController@get_activate')->name('tipos_movimientos.get_activate');
        Route::post('tipos_movimientos/activar/{id}', 'TipoMovimientoController@activate')->name('tipos_movimientos.activate');
        Route::get('tipos_movimientos/eliminar/{id}', 'TipoMovimientoController@get_destroy')->name('tipos_movimientos.get_destroy');
        Route::delete('tipos_movimientos/eliminar/{id}', 'TipoMovimientoController@destroy')->name('tipos_movimientos.destroy');
    //Formas de Conocer USIL
        Route::get('formas_conocimientos', 'FormaConocimientoController@index')->name('formas_conocimientos.index');
        Route::get('formas_conocimientos/all', 'FormaConocimientoController@index_ajax')->name('formas_conocimientos.index_ajax');
        Route::get('formas_conocimientos/ver/{id}', 'FormaConocimientoController@show')->name('formas_conocimientos.show');
        Route::post('formas_conocimientos', 'FormaConocimientoController@store')->name('formas_conocimientos.store');
        Route::get('formas_conocimientos/{id}', 'FormaConocimientoController@edit')->name('formas_conocimientos.edit');
        Route::post('formas_conocimientos/{id}', 'FormaConocimientoController@update')->name('formas_conocimientos.update');
        Route::get('formas_conocimientos/inactivar/{id}', 'FormaConocimientoController@get_unactivate')->name('formas_conocimientos.get_unactivate');
        Route::post('formas_conocimientos/inactivar/{id}', 'FormaConocimientoController@unactivate')->name('formas_conocimientos.unactivate');
        Route::get('formas_conocimientos/activar/{id}', 'FormaConocimientoController@get_activate')->name('formas_conocimientos.get_activate');
        Route::post('formas_conocimientos/activar/{id}', 'FormaConocimientoController@activate')->name('formas_conocimientos.activate');
        Route::get('formas_conocimientos/eliminar/{id}', 'FormaConocimientoController@get_destroy')->name('formas_conocimientos.get_destroy');
        Route::delete('formas_conocimientos/eliminar/{id}', 'FormaConocimientoController@destroy')->name('formas_conocimientos.destroy');

//Noticias y Avisos
    Route::get('noticias_avisos', 'NoticiaAvisoController@index')->name('noticias_avisos.index');
    Route::get('noticias_avisos/ver/{id}', 'NoticiaAvisoController@show')->name('noticias_avisos.show');
    Route::get('noticias_avisos/agregar', 'NoticiaAvisoController@create')->name('noticias_avisos.create');
    Route::post('noticias_avisos/agregar', 'NoticiaAvisoController@store')->name('noticias_avisos.store');
    Route::get('noticias_avisos/editar/{id}', 'NoticiaAvisoController@edit')->name('noticias_avisos.edit');
    Route::post('noticias_avisos/actualizar/{id}', 'NoticiaAvisoController@update')->name('noticias_avisos.update');
    Route::post('noticias_avisos/inactivar/{id}', 'NoticiaAvisoController@unactivate')->name('noticias_avisos.unactivate');
    Route::post('noticias_avisos/activar/{id}', 'NoticiaAvisoController@activate')->name('noticias_avisos.activate');
    Route::get('noticias_avisos/eliminar/{id}', 'NoticiaAvisoController@destroy')->name('noticias_avisos.get_destroy');
    Route::delete('noticias_avisos/eliminar/{id}', 'NoticiaAvisoController@destroy')->name('noticias_avisos.destroy');
    Route::post('noticias_avisos/eliminar_portada/{id}', 'NoticiaAvisoController@destroy_portada')->name('noticias_avisos.destroy_portada');

//Perfiles
    Route::get('perfil/{id}', 'PerfilController@show')->name('perfiles.show');
    Route::post('perfil/{id}', 'PerfilController@update')->name('perfiles.update');
//Configuraciones de diseño
    Route::post('/modo_claro/{id}', 'ConfiguracionController@light_mode')->name('configuraciones.light_mode');
    Route::post('/modo_oscuro/{id}', 'ConfiguracionController@dark_mode')->name('configuraciones.dark_mode');

//Pantallas Alumnos
    Route::get('inicio_alumnos/{id}', 'PantallaAlumnoController@index')->name('pantallas_alumnos.index');
    Route::get('mis_materias/{id}', 'PantallaAlumnoController@materias')->name('pantallas_alumnos.materias');
    Route::get('fechas_de_examenes/{id}', 'PantallaAlumnoController@fechas_examenes')->name('pantallas_alumnos.fechas_examenes');
    Route::get('evaluacion_continua/{id}', 'PantallaAlumnoController@puntajes')->name('pantallas_alumnos.puntajes');
    Route::get('examenes_finales/{id}', 'PantallaAlumnoController@calificaciones')->name('pantallas_alumnos.calificaciones');
    Route::get('informe_academico/{id}/{tipo}', 'CertificadoEstudioController@generar_informe_academico')->name('pantallas_alumnos.informes_academicos');
    Route::get('extension_universitaria/{id}', 'PantallaAlumnoController@extensiones_universitarias')->name('pantallas_alumnos.extensiones_universitarias');
    Route::post('extension_universitaria/adjuntar_certificado/{id}', 'PantallaAlumnoController@adjuntar_certificado_extensiones_universitarias')->name('pantallas_alumnos.adjuntar_certificado_extensiones_universitarias');
    Route::get('extension_universitaria/eliminar_certificado/{id}', 'PantallaAlumnoController@eliminar_certificado_extensiones_universitarias')->name('pantallas_alumnos.get_eliminar_certificado_extensiones_universitarias');
    Route::delete('extension_universitaria/eliminar_certificado/{id}', 'PantallaAlumnoController@eliminar_certificado_extensiones_universitarias')->name('pantallas_alumnos.eliminar_certificado_extensiones_universitarias');
    Route::get('extension_universitaria/catalogo/{id}', 'PantallaAlumnoController@catalogo_extensiones_universitarias')->name('pantallas_alumnos.catalogo_extensiones_universitarias');
    Route::post('extension_universitaria/postular/{id}/{idExtension}', 'PantallaAlumnoController@postular_extension_universitaria')->name('pantallas_alumnos.postular_extension_universitaria');
    Route::delete('extension_universitaria/cancelar_postulacion/{id}/{idDetalle}', 'PantallaAlumnoController@cancelar_postulacion_extension_universitaria')->name('pantallas_alumnos.cancelar_postulacion_extension_universitaria');
    Route::get('inscripcion_tfg/agregar/{id}', 'PantallaAlumnoController@create_inscripciones_tesis')->name('pantallas_alumnos.create_inscripciones_tesis');
    Route::get('inscripciones_tfg/{id}', 'PantallaAlumnoController@inscripciones_tesis')->name('pantallas_alumnos.inscripciones_tesis');
    Route::get('inscripcion_tfg/ver/{id}', 'PantallaAlumnoController@show_inscripciones_tesis')->name('pantallas_alumnos.show_inscripciones_tesis');
    Route::get('anteproyectos_tfg/{id}', 'PantallaAlumnoController@anteproyectos_tesis')->name('pantallas_alumnos.anteproyectos_tesis');
    Route::get('anteproyectos_tfg/ver/{id}', 'PantallaAlumnoController@show_anteproyectos_tesis')->name('pantallas_alumnos.show_anteproyectos_tesis');
    Route::get('anteproyectos_tfg/ver_entregas/{id}', 'PantallaAlumnoController@show_entregas_anteproyectos_tesis')->name('pantallas_alumnos.show_entregas_anteproyectos_tesis');
    Route::get('proyectos_tfg/{id}', 'PantallaAlumnoController@proyectos_tesis')->name('pantallas_alumnos.proyectos_tesis');
    Route::get('proyectos_tfg/ver/{id}', 'PantallaAlumnoController@show_proyectos_tesis')->name('pantallas_alumnos.show_proyectos_tesis');
    Route::get('proyectos_tfg/ver_entregas/{id}', 'PantallaAlumnoController@show_entregas_proyectos_tesis')->name('pantallas_alumnos.show_entregas_proyectos_tesis');
    Route::get('borradores_tfg/{id}', 'PantallaAlumnoController@borradores_tesis')->name('pantallas_alumnos.borradores_tesis');
    Route::get('borradores_tfg/ver/{id}', 'PantallaAlumnoController@show_borradores_tesis')->name('pantallas_alumnos.show_borradores_tesis');
    Route::get('borradores_tfg/ver_entregas/{id}', 'PantallaAlumnoController@show_entregas_borradores_tesis')->name('pantallas_alumnos.show_entregas_borradores_tesis');
    Route::get('noticias/{id}', 'PantallaAlumnoController@noticias')->name('pantallas_alumnos.noticias');
    Route::get('noticia/{id}', 'PantallaAlumnoController@show_noticia')->name('pantallas_alumnos.show_noticia');
    Route::get('avisos/{id}', 'PantallaAlumnoController@avisos')->name('pantallas_alumnos.avisos');
    Route::get('aviso/{id}', 'PantallaAlumnoController@show_aviso')->name('pantallas_alumnos.show_aviso');
    Route::get('solicitudes/{id}', 'PantallaAlumnoController@solicitudes')->name('pantallas_alumnos.solicitudes');
    Route::get('nueva_solicitud/{id}', 'PantallaAlumnoController@create_solicitudes')->name('pantallas_alumnos.create_solicitudes');
    Route::get('encuestas_alumnos/{id}', 'PantallaAlumnoController@encuestas')->name('pantallas_alumnos.encuestas');
    Route::get('estado_cuenta/{id}', 'PantallaAlumnoController@estado_cuenta')->name('pantallas_alumnos.estado_cuenta');

//Pantallas Docentes
    Route::get('inicio_docentes/{id}', 'PantallaDocenteController@index')->name('pantallas_docentes.index');
    Route::get('nueva_clase/{usuario}/{materia}/{semestre}', 'PantallaDocenteController@generate_clases')->name('pantallas_docentes.generate_clases');
    Route::get('cargar_asistencias/{usuario}/{materia}/{semestre}', 'PantallaDocenteController@charge_asistencias')->name('pantallas_docentes.charge_asistencias');
    Route::get('cargar_evaluaciones/{usuario}/{materia}/{carrera}/{semestre}/{tipo}', 'PantallaDocenteController@charge_evaluaciones')->name('pantallas_docentes.charge_evaluaciones');
    Route::get('obtener_evaluaciones/{usuario}/{materia}/{carrera}/{semestre}', 'PantallaDocenteController@obtener_evaluaciones')->name('pantallas_docentes.obtener_evaluaciones');
    Route::get('clases_generadas/{id}', 'PantallaDocenteController@clases')->name('pantallas_docentes.clases');
    Route::get('ver_clase_generada/{id}', 'PantallaDocenteController@show_clases')->name('pantallas_docentes.show_clases');
    Route::get('fechas_examenes/{id}', 'PantallaDocenteController@fechas_examenes')->name('pantallas_docentes.fechas_examenes');
    Route::get('planes_programas_clases_docentes/{id}', 'PlanProgramaClaseDocenteController@index')->name('planes_programas_clases_docentes.index');
    Route::post('planes_programas_clases_docentes/guardar_planes/{id}', 'PlanProgramaClaseDocenteController@store_planes_clases')->name('planes_programas_clases_docentes.store_planes_clases');
    Route::get('planes_programas_clases_docentes/eliminar_planes/{id}', 'PlanProgramaClaseDocenteController@destroy_planes_clases')->name('planes_programas_clases_docentes.destroy_planes_clases');
    Route::post('planes_programas_clases_docentes/copiar_planes/{id}', 'PlanProgramaClaseDocenteController@copy_planes_clases')->name('planes_programas_clases_docentes.copy_planes_clases');
    Route::post('planes_programas_clases_docentes/guardar_programas/{id}', 'PlanProgramaClaseDocenteController@store_programas_clases')->name('planes_programas_clases_docentes.store_programas_clases');
    Route::get('planes_programas_clases_docentes/eliminar_programas/{id}', 'PlanProgramaClaseDocenteController@destroy_programas_clases')->name('planes_programas_clases_docentes.destroy_programas_clases');
    Route::post('planes_programas_clases_docentes/copiar_programas/{id}', 'PlanProgramaClaseDocenteController@copy_programas_clases')->name('planes_programas_clases_docentes.copy_programas_clases');
    Route::get('tutorias_docentes/{id}', 'TutoriaDocenteController@index')->name('tutorias_docentes.index');
    Route::get('tutorias_docentes/ver/{id}', 'TutoriaDocenteController@show')->name('tutorias_docentes.show');
    Route::get('tutorias_docentes/ver_clases/{id}', 'TutoriaDocenteController@index_clases')->name('tutorias_docentes.index_clases');
    Route::get('tutorias_docentes/ver_clase/{id}', 'TutoriaDocenteController@show_clases')->name('tutorias_docentes.show_clases');
    Route::get('tutorias_docentes/nueva_clase/{id}', 'TutoriaDocenteController@create_clases')->name('tutorias_docentes.create_clases');
    Route::get('tutorias_docentes/cargar_evaluacion/{id}', 'TutoriaDocenteController@puntajes')->name('tutorias_docentes.puntajes');
    Route::get('extensiones_universitarias/{id}', 'PantallaDocenteController@extensiones_universitarias')->name('pantallas_docentes.extensiones_universitarias');
    Route::get('ver_extension_universitaria/{id}', 'PantallaDocenteController@show_extensiones_universitarias')->name('pantallas_docentes.show_extensiones_universitarias');
    Route::get('crear_extensiones_universitarias/{id}', 'PantallaDocenteController@create_extensiones_universitarias')->name('pantallas_docentes.create_extensiones_universitarias');
    Route::get('encuestas_docentes/{id}', 'PantallaDocenteController@encuestas')->name('pantallas_docentes.encuestas');
    Route::get('cobros/{id}', 'PantallaDocenteController@cobros')->name('pantallas_docentes.cobros');
