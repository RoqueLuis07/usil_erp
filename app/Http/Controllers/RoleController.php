<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('ver_roles');

        try {
            $roles = Role::get();
            return view('roles/index')->with(compact('roles'));
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }
    public function show($id)
    {
        $this->authorize('ver_roles');

        try {
            $rol = Role::findOrFail($id);
            $permisos = $rol->permissions->all();
            $cant_usuarios = $rol->user->count();
            $cant_permisos = $rol->permissions->count();

            $p_cajeros = Permission::where('name', 'like', '%' . 'cajero')->orderBy('id')->get();
            $p_clientes = Permission::where('name', 'like', '%' . 'clientes')->orderBy('id')->get();
            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_matriculaciones = Permission::where('name', 'like', '%' . 'matriculaciones')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_examenes_suficiencia = Permission::where('name', 'like', '%' . 'examenes_suficiencia')->orderBy('id')->get();
            $p_materias_semestres = Permission::where('name', 'like', '%' . 'materias_semestres')->orderBy('id')->get();
            $p_actas = Permission::where('name', 'like', '%' . 'actas')->orderBy('id')->get();
            $p_materias_clases = Permission::where('name', 'like', '%' . 'materias_clases')->orderBy('id')->get();
            $p_anulaciones_correlatividades = Permission::where('name', 'like', '%' . 'anulaciones_correlatividades')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_tesis = Permission::where('name', 'like', '%' . 'tesis')->orderBy('id')->get();
            $p_tutorias = Permission::where('name', 'like', '%' . 'tutorias')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_solicitudes = Permission::where('name', 'like', '%' . 'solicitudes')->orderBy('id')->get();
            $p_encuestas = Permission::where('name', 'like', '%' . 'encuestas')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_materias_suficiencia = Permission::where('name', 'like', '%' . 'materias_suficiencias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_mallas = Permission::where('name', 'like', '%' . 'mallas')->orderBy('id')->get();
            $p_mallas_espejo = Permission::where('name', 'like', '%' . 'mallas_espejo')->orderBy('id')->get();
            $p_escalas = Permission::where('name', 'like', '%' . 'escalas')->orderBy('id')->get();
            $p_evaluaciones = Permission::where('name', 'like', '%' . 'evaluaciones')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_fechas_desmatriculaciones = Permission::where('name', 'like', '%' . 'fechas_desmatriculaciones')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_alumnos_ubs = Permission::where('name', 'like', '%' . 'alumnos_ubs')->orderBy('id')->get();
            $p_inscripciones_ubs = Permission::where('name', 'like', '%' . 'inscripciones_ubs')->orderBy('id')->get();
            $p_cursos_ubs = Permission::where('name', 'like', '%' . 'cursos_ubs')->orderBy('id')->get();
            $p_modulos_ubs = Permission::where('name', 'like', '%' . 'modulos_ubs')->orderBy('id')->get();
            $p_tipos_cursos_ubs = Permission::where('name', 'like', '%' . 'tipos_cursos_ubs')->orderBy('id')->get();
            $p_maestrias_ubs = Permission::where('name', 'like', '%' . 'maestrias_ubs')->orderBy('id')->get();
            $p_extensiones_ubs = Permission::where('name', 'like', '%' . 'extensiones_ubs')->orderBy('id')->get();
            $p_tesis_ubs = Permission::where('name', 'like', '%' . 'tesis_ubs')->orderBy('id')->get();
            $p_docentes_ubs = Permission::where('name', 'like', '%' . 'docentes_ubs')->orderBy('id')->get();
            $p_ventas = Permission::where('name', 'like', '%' . 'ventas')->orderBy('id')->get();
            $p_recibos = Permission::where('name', 'like', '%' . 'recibos')->orderBy('id')->get();
            $p_cobros = Permission::where('name', 'like', '%' . 'cobros')->orderBy('id')->get();
            $p_notas_creditos = Permission::where('name', 'like', '%' . 'notas_creditos')->orderBy('id')->get();
            $p_cajas_arqueos = Permission::where('name', 'like', '%' . 'cajas_arqueos')->orderBy('id')->get();
            $p_compras_ordenes = Permission::where('name', 'like', '%' . 'compras_ordenes')->orderBy('id')->get();
            $p_compras = Permission::where('name', 'like', '%' . 'compras')->orderBy('id')->get();
            $p_pagos_ordenes = Permission::where('name', 'like', '%' . 'pagos_ordenes')->orderBy('id')->get();
            $p_pagos = Permission::where('name', 'like', '%' . 'pagos')->orderBy('id')->get();
            $p_proveedores = Permission::where('name', 'like', '%' . 'proveedores')->orderBy('id')->get();
            $p_asientos_contables = Permission::where('name', 'like', '%' . 'asientos_contables')->orderBy('id')->get();
            $p_cuentas_contables_saldos = Permission::where('name', 'like', '%' . 'cuentas_contables_saldos')->orderBy('id')->get();
            $p_articulos = Permission::where('name', 'like', '%' . 'articulos')->orderBy('id')->get();
            $p_cuentas_contables = Permission::where('name', 'like', '%' . 'cuentas_contables')->orderBy('id')->get();
            $p_centros_costos_contables = Permission::where('name', 'like', '%' . 'centros_costos_contables')->orderBy('id')->get();
            $p_unidades_negocios_contables = Permission::where('name', 'like', '%' . 'unidades_negocios_contables')->orderBy('id')->get();
            $p_tipos_documentos_contables = Permission::where('name', 'like', '%' . 'tipos_documentos_contables')->orderBy('id')->get();
            $p_cajas = Permission::where('name', 'like', '%' . 'cajas')->orderBy('id')->get();
            $p_cuentas_bancarias = Permission::where('name', 'like', '%' . 'cuentas_bancarias')->orderBy('id')->get();
            $p_cajas_movimientos = Permission::where('name', 'like', '%' . 'cajas_movimientos')->orderBy('id')->get();
            $p_movimientos_cuentas = Permission::where('name', 'like', '%' . 'movimientos_cuentas')->orderBy('id')->get();
            $p_cajas_cuentas_movimientos = Permission::where('name', 'like', '%' . 'cajas_cuentas_movimientos')->orderBy('id')->get();
            $p_bancos = Permission::where('name', 'like', '%' . 'bancos')->orderBy('id')->get();
            $p_cotizaciones = Permission::where('name', 'like', '%' . 'cotizaciones')->orderBy('id')->get();
            $p_convenios = Permission::where('name', 'like', '%' . 'convenios')->orderBy('id')->get();
            $p_pagos_formas = Permission::where('name', 'like', '%' . 'pagos_formas')->orderBy('id')->get();
            $p_monedas = Permission::where('name', 'like', '%' . 'monedas')->orderBy('id')->get();
            $p_empleados = Permission::where('name', 'like', '%' . 'empleados')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_noticias_avisos = Permission::where('name', 'like', '%' . 'noticias_avisos')->orderBy('id')->get();
            $p_empresas = Permission::where('name', 'like', '%' . 'empresa')->orderBy('id')->get();
            $p_puntos_impresiones = Permission::where('name', 'like', '%' . 'puntos_impresiones')->orderBy('id')->get();
            $p_timbrados = Permission::where('name', 'like', '%' . 'timbrados')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_paises = Permission::where('name', 'like', '%' . 'paises')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_tipos_movimientos = Permission::where('name', 'like', '%' . 'tipos_movimientos')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/show')->with(compact('rol', 'permisos', 'cant_usuarios', 'cant_permisos',
                                                    'p_cajeros', 'p_clientes',

                                                    'p_alumnos', 'p_matriculaciones', 'p_docentes', 'p_examenes_suficiencia',
                                                    'p_materias_semestres', 'p_actas', 'p_materias_clases', 'p_anulaciones_correlatividades',
                                                    'p_convalidaciones',
                                                    'p_tesis',
                                                    'p_tutorias', 'p_extensiones_universitarias',
                                                    'p_solicitudes', 'p_encuestas', 'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                    'p_materias', 'p_materias_suficiencia', 'p_semestres', 'p_mallas',
                                                    'p_mallas_espejo', 'p_escalas', 'p_modalidades', 'p_formaciones_academicas',
                                                    'p_evaluaciones', 'p_instituciones_educativas', 'p_fechas_desmatriculaciones', 'p_areas_conocimientos',
                                                    'p_reportes_academicos',

                                                    'p_alumnos_ubs', 'p_inscripciones_ubs',
                                                    'p_cursos_ubs', 'p_modulos_ubs', 'p_tipos_cursos_ubs',
                                                    'p_maestrias_ubs',
                                                    'p_tesis_ubs',
                                                    'p_extensiones_ubs', 'p_docentes_ubs',

                                                    'p_ventas', 'p_recibos', 'p_cobros', 'p_notas_creditos',
                                                    'p_cajas_arqueos',

                                                    'p_compras_ordenes', 'p_compras',
                                                    'p_proveedores',
                                                    'p_pagos_ordenes', 'p_pagos',

                                                    'p_asientos_contables', 'p_cuentas_contables_saldos', 'p_articulos', 'p_cuentas_contables', 'p_tipos_documentos_contables',
                                                    'p_centros_costos_contables', 'p_unidades_negocios_contables',

                                                    'p_cajas', 'p_cuentas_bancarias', 'p_cajas_movimientos', 'p_movimientos_cuentas',
                                                    'p_cajas_cuentas_movimientos', 'p_bancos', 'p_cotizaciones', 'p_convenios',
                                                    'p_pagos_formas', 'p_monedas',

                                                    'p_empleados',

                                                    'p_usuarios', 'p_roles', 'p_permisos',

                                                    'p_noticias_avisos',

                                                    'p_empresas', 'p_puntos_impresiones', 'p_timbrados', 'p_nacionalidades',
                                                    'p_paises', 'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                    'p_tipos_movimientos', 'p_formas_conocimientos',

                                                    'p_alumnos_pantalla',

                                                    'p_docentes_pantalla'
                                                    ));
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }
    public function create()
    {
        $this->authorize('crear_roles');

        try {
            $p_cajeros = Permission::where('name', 'like', '%' . 'cajero')->orderBy('id')->get();
            $p_clientes = Permission::where('name', 'like', '%' . 'clientes')->orderBy('id')->get();
            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_matriculaciones = Permission::where('name', 'like', '%' . 'matriculaciones')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_examenes_suficiencia = Permission::where('name', 'like', '%' . 'examenes_suficiencia')->orderBy('id')->get();
            $p_materias_semestres = Permission::where('name', 'like', '%' . 'materias_semestres')->orderBy('id')->get();
            $p_actas = Permission::where('name', 'like', '%' . 'actas')->orderBy('id')->get();
            $p_materias_clases = Permission::where('name', 'like', '%' . 'materias_clases')->orderBy('id')->get();
            $p_anulaciones_correlatividades = Permission::where('name', 'like', '%' . 'anulaciones_correlatividades')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_tesis = Permission::where('name', 'like', '%' . 'tesis')->orderBy('id')->get();
            $p_tutorias = Permission::where('name', 'like', '%' . 'tutorias')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_solicitudes = Permission::where('name', 'like', '%' . 'solicitudes')->orderBy('id')->get();
            $p_encuestas = Permission::where('name', 'like', '%' . 'encuestas')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_materias_suficiencia = Permission::where('name', 'like', '%' . 'materias_suficiencias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_mallas = Permission::where('name', 'like', '%' . 'mallas')->orderBy('id')->get();
            $p_mallas_espejo = Permission::where('name', 'like', '%' . 'mallas_espejo')->orderBy('id')->get();
            $p_escalas = Permission::where('name', 'like', '%' . 'escalas')->orderBy('id')->get();
            $p_evaluaciones = Permission::where('name', 'like', '%' . 'evaluaciones')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_fechas_desmatriculaciones = Permission::where('name', 'like', '%' . 'fechas_desmatriculaciones')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_alumnos_ubs = Permission::where('name', 'like', '%' . 'alumnos_ubs')->orderBy('id')->get();
            $p_inscripciones_ubs = Permission::where('name', 'like', '%' . 'inscripciones_ubs')->orderBy('id')->get();
            $p_cursos_ubs = Permission::where('name', 'like', '%' . 'cursos_ubs')->orderBy('id')->get();
            $p_modulos_ubs = Permission::where('name', 'like', '%' . 'modulos_ubs')->orderBy('id')->get();
            $p_tipos_cursos_ubs = Permission::where('name', 'like', '%' . 'tipos_cursos_ubs')->orderBy('id')->get();
            $p_maestrias_ubs = Permission::where('name', 'like', '%' . 'maestrias_ubs')->orderBy('id')->get();
            $p_extensiones_ubs = Permission::where('name', 'like', '%' . 'extensiones_ubs')->orderBy('id')->get();
            $p_tesis_ubs = Permission::where('name', 'like', '%' . 'tesis_ubs')->orderBy('id')->get();
            $p_docentes_ubs = Permission::where('name', 'like', '%' . 'docentes_ubs')->orderBy('id')->get();
            $p_ventas = Permission::where('name', 'like', '%' . 'ventas')->orderBy('id')->get();
            $p_recibos = Permission::where('name', 'like', '%' . 'recibos')->orderBy('id')->get();
            $p_cobros = Permission::where('name', 'like', '%' . 'cobros')->orderBy('id')->get();
            $p_notas_creditos = Permission::where('name', 'like', '%' . 'notas_creditos')->orderBy('id')->get();
            $p_cajas_arqueos = Permission::where('name', 'like', '%' . 'cajas_arqueos')->orderBy('id')->get();
            $p_compras_ordenes = Permission::where('name', 'like', '%' . 'compras_ordenes')->orderBy('id')->get();
            $p_compras = Permission::where('name', 'like', '%' . 'compras')->orderBy('id')->get();
            $p_pagos_ordenes = Permission::where('name', 'like', '%' . 'pagos_ordenes')->orderBy('id')->get();
            $p_pagos = Permission::where('name', 'like', '%' . 'pagos')->orderBy('id')->get();
            $p_proveedores = Permission::where('name', 'like', '%' . 'proveedores')->orderBy('id')->get();
            $p_asientos_contables = Permission::where('name', 'like', '%' . 'asientos_contables')->orderBy('id')->get();
            $p_cuentas_contables_saldos = Permission::where('name', 'like', '%' . 'cuentas_contables_saldos')->orderBy('id')->get();
            $p_articulos = Permission::where('name', 'like', '%' . 'articulos')->orderBy('id')->get();
            $p_cuentas_contables = Permission::where('name', 'like', '%' . 'cuentas_contables')->orderBy('id')->get();
            $p_centros_costos_contables = Permission::where('name', 'like', '%' . 'centros_costos_contables')->orderBy('id')->get();
            $p_unidades_negocios_contables = Permission::where('name', 'like', '%' . 'unidades_negocios_contables')->orderBy('id')->get();
            $p_tipos_documentos_contables = Permission::where('name', 'like', '%' . 'tipos_documentos_contables')->orderBy('id')->get();
            $p_cajas = Permission::where('name', 'like', '%' . 'cajas')->orderBy('id')->get();
            $p_cuentas_bancarias = Permission::where('name', 'like', '%' . 'cuentas_bancarias')->orderBy('id')->get();
            $p_cajas_movimientos = Permission::where('name', 'like', '%' . 'cajas_movimientos')->orderBy('id')->get();
            $p_movimientos_cuentas = Permission::where('name', 'like', '%' . 'movimientos_cuentas')->orderBy('id')->get();
            $p_cajas_cuentas_movimientos = Permission::where('name', 'like', '%' . 'cajas_cuentas_movimientos')->orderBy('id')->get();
            $p_bancos = Permission::where('name', 'like', '%' . 'bancos')->orderBy('id')->get();
            $p_cotizaciones = Permission::where('name', 'like', '%' . 'cotizaciones')->orderBy('id')->get();
            $p_convenios = Permission::where('name', 'like', '%' . 'convenios')->orderBy('id')->get();
            $p_pagos_formas = Permission::where('name', 'like', '%' . 'pagos_formas')->orderBy('id')->get();
            $p_monedas = Permission::where('name', 'like', '%' . 'monedas')->orderBy('id')->get();
            $p_empleados = Permission::where('name', 'like', '%' . 'empleados')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_noticias_avisos = Permission::where('name', 'like', '%' . 'noticias_avisos')->orderBy('id')->get();
            $p_empresas = Permission::where('name', 'like', '%' . 'empresa')->orderBy('id')->get();
            $p_puntos_impresiones = Permission::where('name', 'like', '%' . 'puntos_impresiones')->orderBy('id')->get();
            $p_timbrados = Permission::where('name', 'like', '%' . 'timbrados')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_paises = Permission::where('name', 'like', '%' . 'paises')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_tipos_movimientos = Permission::where('name', 'like', '%' . 'tipos_movimientos')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/create')->with(compact('p_cajeros', 'p_clientes',

                                                        'p_alumnos', 'p_matriculaciones', 'p_docentes', 'p_examenes_suficiencia',
                                                        'p_materias_semestres', 'p_actas', 'p_materias_clases', 'p_anulaciones_correlatividades',
                                                        'p_convalidaciones',
                                                        'p_tesis',
                                                        'p_tutorias', 'p_extensiones_universitarias',
                                                        'p_solicitudes', 'p_encuestas', 'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                        'p_materias', 'p_materias_suficiencia', 'p_semestres', 'p_mallas',
                                                        'p_mallas_espejo', 'p_escalas', 'p_modalidades', 'p_formaciones_academicas',
                                                        'p_evaluaciones', 'p_instituciones_educativas', 'p_fechas_desmatriculaciones', 'p_areas_conocimientos',
                                                        'p_reportes_academicos',

                                                        'p_alumnos_ubs', 'p_inscripciones_ubs',
                                                        'p_cursos_ubs', 'p_modulos_ubs', 'p_tipos_cursos_ubs',
                                                        'p_maestrias_ubs',
                                                        'p_tesis_ubs',
                                                        'p_extensiones_ubs', 'p_docentes_ubs',

                                                        'p_ventas', 'p_recibos', 'p_cobros', 'p_notas_creditos',
                                                        'p_cajas_arqueos',

                                                        'p_compras_ordenes', 'p_compras',
                                                        'p_proveedores',
                                                        'p_pagos_ordenes', 'p_pagos',

                                                        'p_asientos_contables', 'p_cuentas_contables_saldos', 'p_articulos', 'p_cuentas_contables', 'p_tipos_documentos_contables',
                                                        'p_centros_costos_contables', 'p_unidades_negocios_contables',

                                                        'p_cajas', 'p_cuentas_bancarias', 'p_cajas_movimientos', 'p_movimientos_cuentas',
                                                        'p_cajas_cuentas_movimientos', 'p_bancos', 'p_cotizaciones', 'p_convenios',
                                                        'p_pagos_formas', 'p_monedas',

                                                        'p_empleados',

                                                        'p_usuarios', 'p_roles', 'p_permisos',

                                                        'p_noticias_avisos',

                                                        'p_empresas', 'p_puntos_impresiones', 'p_timbrados', 'p_nacionalidades',
                                                        'p_paises', 'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                        'p_tipos_movimientos', 'p_formas_conocimientos',

                                                        'p_alumnos_pantalla',

                                                        'p_docentes_pantalla'
                                                    ));
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        $this->authorize('crear_roles');

        $request->validate([
            'name' => ['required', Rule::unique('roles')],
            'permission' => 'required'
        ]);

        DB::beginTransaction();

        try {

            $rol = new Role();
            $rol->name = removeAccents(Str::upper($request->name));
            $rol->guard_name = 'web';
            $rol->save();

            $permisos = array_keys($request->permission);
            $rol->syncPermissions($permisos);

            DB::commit();

            return redirect()->route('roles.index')->with('success-message','El rol ' . $rol->name . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }
    public function edit($id)
    {
        $this->authorize('editar_roles');

        try {
            $rol = Role::findOrFail($id);
            $permisos = $rol->permissions->all();
            $cant_usuarios = $rol->user->count();
            $cant_permisos = $rol->permissions->count();

            $p_cajeros = Permission::where('name', 'like', '%' . 'cajero')->orderBy('id')->get();
            $p_clientes = Permission::where('name', 'like', '%' . 'clientes')->orderBy('id')->get();
            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_matriculaciones = Permission::where('name', 'like', '%' . 'matriculaciones')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_examenes_suficiencia = Permission::where('name', 'like', '%' . 'examenes_suficiencia')->orderBy('id')->get();
            $p_materias_semestres = Permission::where('name', 'like', '%' . 'materias_semestres')->orderBy('id')->get();
            $p_actas = Permission::where('name', 'like', '%' . 'actas')->orderBy('id')->get();
            $p_materias_clases = Permission::where('name', 'like', '%' . 'materias_clases')->orderBy('id')->get();
            $p_anulaciones_correlatividades = Permission::where('name', 'like', '%' . 'anulaciones_correlatividades')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_tesis = Permission::where('name', 'like', '%' . 'tesis')->orderBy('id')->get();
            $p_tutorias = Permission::where('name', 'like', '%' . 'tutorias')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_solicitudes = Permission::where('name', 'like', '%' . 'solicitudes')->orderBy('id')->get();
            $p_encuestas = Permission::where('name', 'like', '%' . 'encuestas')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_materias_suficiencia = Permission::where('name', 'like', '%' . 'materias_suficiencias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_mallas = Permission::where('name', 'like', '%' . 'mallas')->orderBy('id')->get();
            $p_mallas_espejo = Permission::where('name', 'like', '%' . 'mallas_espejo')->orderBy('id')->get();
            $p_escalas = Permission::where('name', 'like', '%' . 'escalas')->orderBy('id')->get();
            $p_evaluaciones = Permission::where('name', 'like', '%' . 'evaluaciones')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_fechas_desmatriculaciones = Permission::where('name', 'like', '%' . 'fechas_desmatriculaciones')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_alumnos_ubs = Permission::where('name', 'like', '%' . 'alumnos_ubs')->orderBy('id')->get();
            $p_inscripciones_ubs = Permission::where('name', 'like', '%' . 'inscripciones_ubs')->orderBy('id')->get();
            $p_cursos_ubs = Permission::where('name', 'like', '%' . 'cursos_ubs')->orderBy('id')->get();
            $p_modulos_ubs = Permission::where('name', 'like', '%' . 'modulos_ubs')->orderBy('id')->get();
            $p_tipos_cursos_ubs = Permission::where('name', 'like', '%' . 'tipos_cursos_ubs')->orderBy('id')->get();
            $p_maestrias_ubs = Permission::where('name', 'like', '%' . 'maestrias_ubs')->orderBy('id')->get();
            $p_extensiones_ubs = Permission::where('name', 'like', '%' . 'extensiones_ubs')->orderBy('id')->get();
            $p_tesis_ubs = Permission::where('name', 'like', '%' . 'tesis_ubs')->orderBy('id')->get();
            $p_docentes_ubs = Permission::where('name', 'like', '%' . 'docentes_ubs')->orderBy('id')->get();
            $p_ventas = Permission::where('name', 'like', '%' . 'ventas')->orderBy('id')->get();
            $p_recibos = Permission::where('name', 'like', '%' . 'recibos')->orderBy('id')->get();
            $p_cobros = Permission::where('name', 'like', '%' . 'cobros')->orderBy('id')->get();
            $p_notas_creditos = Permission::where('name', 'like', '%' . 'notas_creditos')->orderBy('id')->get();
            $p_cajas_arqueos = Permission::where('name', 'like', '%' . 'cajas_arqueos')->orderBy('id')->get();
            $p_compras_ordenes = Permission::where('name', 'like', '%' . 'compras_ordenes')->orderBy('id')->get();
            $p_compras = Permission::where('name', 'like', '%' . 'compras')->orderBy('id')->get();
            $p_pagos_ordenes = Permission::where('name', 'like', '%' . 'pagos_ordenes')->orderBy('id')->get();
            $p_pagos = Permission::where('name', 'like', '%' . 'pagos')->orderBy('id')->get();
            $p_proveedores = Permission::where('name', 'like', '%' . 'proveedores')->orderBy('id')->get();
            $p_asientos_contables = Permission::where('name', 'like', '%' . 'asientos_contables')->orderBy('id')->get();
            $p_cuentas_contables_saldos = Permission::where('name', 'like', '%' . 'cuentas_contables_saldos')->orderBy('id')->get();
            $p_articulos = Permission::where('name', 'like', '%' . 'articulos')->orderBy('id')->get();
            $p_cuentas_contables = Permission::where('name', 'like', '%' . 'cuentas_contables')->orderBy('id')->get();
            $p_centros_costos_contables = Permission::where('name', 'like', '%' . 'centros_costos_contables')->orderBy('id')->get();
            $p_unidades_negocios_contables = Permission::where('name', 'like', '%' . 'unidades_negocios_contables')->orderBy('id')->get();
            $p_tipos_documentos_contables = Permission::where('name', 'like', '%' . 'tipos_documentos_contables')->orderBy('id')->get();
            $p_cajas = Permission::where('name', 'like', '%' . 'cajas')->orderBy('id')->get();
            $p_cuentas_bancarias = Permission::where('name', 'like', '%' . 'cuentas_bancarias')->orderBy('id')->get();
            $p_cajas_movimientos = Permission::where('name', 'like', '%' . 'cajas_movimientos')->orderBy('id')->get();
            $p_movimientos_cuentas = Permission::where('name', 'like', '%' . 'movimientos_cuentas')->orderBy('id')->get();
            $p_cajas_cuentas_movimientos = Permission::where('name', 'like', '%' . 'cajas_cuentas_movimientos')->orderBy('id')->get();
            $p_bancos = Permission::where('name', 'like', '%' . 'bancos')->orderBy('id')->get();
            $p_cotizaciones = Permission::where('name', 'like', '%' . 'cotizaciones')->orderBy('id')->get();
            $p_convenios = Permission::where('name', 'like', '%' . 'convenios')->orderBy('id')->get();
            $p_pagos_formas = Permission::where('name', 'like', '%' . 'pagos_formas')->orderBy('id')->get();
            $p_monedas = Permission::where('name', 'like', '%' . 'monedas')->orderBy('id')->get();
            $p_empleados = Permission::where('name', 'like', '%' . 'empleados')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_noticias_avisos = Permission::where('name', 'like', '%' . 'noticias_avisos')->orderBy('id')->get();
            $p_empresas = Permission::where('name', 'like', '%' . 'empresa')->orderBy('id')->get();
            $p_puntos_impresiones = Permission::where('name', 'like', '%' . 'puntos_impresiones')->orderBy('id')->get();
            $p_timbrados = Permission::where('name', 'like', '%' . 'timbrados')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_paises = Permission::where('name', 'like', '%' . 'paises')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_tipos_movimientos = Permission::where('name', 'like', '%' . 'tipos_movimientos')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/edit')->with(compact('rol', 'permisos', 'cant_usuarios', 'cant_permisos',
                                                    'p_cajeros', 'p_clientes',

                                                    'p_alumnos', 'p_matriculaciones', 'p_docentes', 'p_examenes_suficiencia',
                                                    'p_materias_semestres', 'p_actas', 'p_materias_clases', 'p_anulaciones_correlatividades',
                                                    'p_convalidaciones',
                                                    'p_tesis',
                                                    'p_tutorias', 'p_extensiones_universitarias',
                                                    'p_solicitudes', 'p_encuestas', 'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                    'p_materias', 'p_materias_suficiencia', 'p_semestres', 'p_mallas',
                                                    'p_mallas_espejo', 'p_escalas', 'p_modalidades', 'p_formaciones_academicas',
                                                    'p_evaluaciones', 'p_instituciones_educativas', 'p_fechas_desmatriculaciones', 'p_areas_conocimientos',
                                                    'p_reportes_academicos',

                                                    'p_alumnos_ubs', 'p_inscripciones_ubs',
                                                    'p_cursos_ubs', 'p_modulos_ubs', 'p_tipos_cursos_ubs',
                                                    'p_maestrias_ubs',
                                                    'p_tesis_ubs',
                                                    'p_extensiones_ubs', 'p_docentes_ubs',

                                                    'p_ventas', 'p_recibos', 'p_cobros', 'p_notas_creditos',
                                                    'p_cajas_arqueos',

                                                    'p_compras_ordenes', 'p_compras',
                                                    'p_proveedores',
                                                    'p_pagos_ordenes', 'p_pagos',

                                                    'p_asientos_contables', 'p_cuentas_contables_saldos', 'p_articulos', 'p_cuentas_contables', 'p_tipos_documentos_contables',
                                                    'p_centros_costos_contables', 'p_unidades_negocios_contables',

                                                    'p_cajas', 'p_cuentas_bancarias', 'p_cajas_movimientos', 'p_movimientos_cuentas',
                                                    'p_cajas_cuentas_movimientos', 'p_bancos', 'p_cotizaciones', 'p_convenios',
                                                    'p_pagos_formas', 'p_monedas',

                                                    'p_empleados',

                                                    'p_usuarios', 'p_roles', 'p_permisos',

                                                    'p_noticias_avisos',

                                                    'p_empresas', 'p_puntos_impresiones', 'p_timbrados', 'p_nacionalidades',
                                                    'p_paises', 'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                    'p_tipos_movimientos', 'p_formas_conocimientos',

                                                    'p_alumnos_pantalla',

                                                    'p_docentes_pantalla'
                                                    ));
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $this->authorize('editar_roles');

        $request->validate([
            'name' => ['required', Rule::unique('roles')->ignore($id)],
            'permission' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $rol = Role::findOrFail($id);
            $rol->name = removeAccents(Str::upper($request->name));
            $rol->save();

            $permisos = array_keys($request->permission);
            $rol->syncPermissions($permisos);

            DB::commit();

            return redirect()->route('roles.index')->with('success-message','El rol ' . $rol->name . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_roles');

        DB::beginTransaction();

        try {
            $rol = Role::findOrFail($id);
            $rol->state = 0;
            $rol->save();

            $rol->permissions()->detach();

            DB::commit();

            return redirect()->route('roles.index')->with('error-message','El rol ' . $rol->name . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_roles');

        DB::beginTransaction();

        try {
            $rol = Role::findOrFail($id);
            $rol->state = 1;
            $rol->save();

            DB::commit();

            return redirect()->route('roles.index')->with('error-message','El rol ' . $rol->name . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_roles');

        DB::beginTransaction();

        try {
            $rol = Role::findOrFail($id);
            $rol->delete();

            DB::commit();

            return redirect()->route('roles.index')->with('error-message','El rol ' . $rol->name . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('roles.index')->with('error-message', 'El rol no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('roles.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
