<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/show')->with(compact('rol', 'permisos', 'cant_usuarios', 'cant_permisos',
                                                    'p_alumnos', 'p_docentes', 'p_convalidaciones',
                                                    'p_extensiones_universitarias',
                                                    'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                    'p_materias', 'p_semestres', 'p_modalidades', 'p_formaciones_academicas',
                                                    'p_instituciones_educativas', 'p_areas_conocimientos',
                                                    'p_reportes_academicos',


                                                    'p_usuarios', 'p_roles', 'p_permisos',

                                                    'p_nacionalidades',
                                                    'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                    'p_formas_conocimientos',

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
            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/create')->with(compact('p_alumnos', 'p_docentes', 'p_convalidaciones',
                                                        'p_extensiones_universitarias',
                                                        'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                        'p_materias', 'p_semestres', 'p_modalidades', 'p_formaciones_academicas',
                                                        'p_instituciones_educativas', 'p_areas_conocimientos',
                                                        'p_reportes_academicos',


                                                        'p_usuarios', 'p_roles', 'p_permisos',

                                                        'p_nacionalidades',
                                                        'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                        'p_formas_conocimientos',

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

            $p_alumnos = Permission::where('name', 'like', '%' . 'alumnos')->orderBy('id')->get();
            $p_docentes = Permission::where('name', 'like', '%' . 'docentes')->orderBy('id')->get();
            $p_convalidaciones = Permission::where('name', 'like', '%' . 'convalidaciones' . '%')->orderBy('id')->get();
            $p_extensiones_universitarias = Permission::where('name', 'like', '%' . 'extensiones_universitarias')->orderBy('id')->get();
            $p_reportes_academicos = Permission::where('name', 'like', '%' . 'reportes_academicos' . '%')->orderBy('id')->get();
            $p_parametros_academicos = Permission::where('name', 'like', '%' . 'parametros_academicos')->orderBy('id')->get();
            $p_programas = Permission::where('name', 'like', '%' . 'programas')->orderBy('id')->get();
            $p_facultades = Permission::where('name', 'like', '%' . 'facultades')->orderBy('id')->get();
            $p_carreras = Permission::where('name', 'like', '%' . 'carreras')->orderBy('id')->get();
            $p_materias = Permission::where('name', 'like', '%' . 'materias')->orderBy('id')->get();
            $p_semestres = Permission::where('name', 'like', '%' . 'periodos')->orderBy('id')->get();
            $p_modalidades = Permission::where('name', 'like', '%' . 'modalidades')->orderBy('id')->get();
            $p_formaciones_academicas = Permission::where('name', 'like', '%' . 'formaciones_academicas')->orderBy('id')->get();
            $p_instituciones_educativas = Permission::where('name', 'like', '%' . 'instituciones_educativas')->orderBy('id')->get();
            $p_areas_conocimientos = Permission::where('name', 'like', '%' . 'areas_conocimientos')->orderBy('id')->get();
            $p_usuarios = Permission::where('name', 'like', '%' . 'usuarios')->orderBy('id')->get();
            $p_roles = Permission::where('name', 'like', '%' . 'roles')->orderBy('id')->get();
            $p_permisos = Permission::where('name', 'like', '%' . 'permisos')->orderBy('id')->get();
            $p_nacionalidades = Permission::where('name', 'like', '%' . 'nacionalidades')->orderBy('id')->get();
            $p_departamentos_paraguay = Permission::where('name', 'like', '%' . 'departamentos_paraguay')->orderBy('id')->get();
            $p_ciudades = Permission::where('name', 'like', '%' . 'ciudades')->orderBy('id')->get();
            $p_barrios = Permission::where('name', 'like', '%' . 'barrios')->orderBy('id')->get();
            $p_formas_conocimientos = Permission::where('name', 'like', '%' . 'formas_conocimientos')->orderBy('id')->get();
            $p_alumnos_pantalla = Permission::where('name', 'like', '%' . 'alumnos_pantalla')->orderBy('id')->get();
            $p_docentes_pantalla = Permission::where('name', 'like', '%' . 'docentes_pantalla')->orderBy('id')->get();

            return view ('roles/edit')->with(compact('rol', 'permisos', 'cant_usuarios', 'cant_permisos',
                                                    'p_alumnos', 'p_docentes', 'p_convalidaciones',
                                                    'p_extensiones_universitarias',
                                                    'p_parametros_academicos', 'p_programas', 'p_facultades', 'p_carreras',
                                                    'p_materias', 'p_semestres', 'p_modalidades', 'p_formaciones_academicas',
                                                    'p_instituciones_educativas', 'p_areas_conocimientos',
                                                    'p_reportes_academicos',


                                                    'p_usuarios', 'p_roles', 'p_permisos',

                                                    'p_nacionalidades',
                                                    'p_departamentos_paraguay', 'p_ciudades', 'p_barrios',
                                                    'p_formas_conocimientos',

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
