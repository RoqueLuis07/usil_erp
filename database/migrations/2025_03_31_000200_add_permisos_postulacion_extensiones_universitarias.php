<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Crea los permisos nuevos del flujo de postulación (no existían en
     * PermissionSeeder porque el flujo no existía) y se los asigna a los roles
     * que corresponden. Va como migración —y no solo como cambio al seeder—
     * para que llegue también a bases ya sembradas (Railway), donde el
     * entrypoint solo corre `db:seed` la primera vez que arranca con la tabla
     * `usuarios` vacía.
     */
    public function up(): void
    {
        $nuevos = [
            'ver_catalogo_extensiones_alumnos_pantalla',
            'postular_extensiones_alumnos_pantalla',
            'gestionar_postulaciones_extensiones_universitarias',
        ];

        foreach ($nuevos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $superadmin = Role::where('name', 'SUPERADMIN')->where('guard_name', 'web')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo($nuevos);
        }

        $alumno = Role::where('name', 'ALUMNO')->where('guard_name', 'web')->first();
        if ($alumno) {
            $alumno->givePermissionTo(['ver_catalogo_extensiones_alumnos_pantalla', 'postular_extensiones_alumnos_pantalla']);
        }

        // ENCARGADO_DOCENTE ya venía posteando al form de creación de proyectos
        // sin tener el permiso `crear_extensiones_universitarias` que el
        // controlador exige (bug preexistente, corregido acá de paso).
        $encargadoDocente = Role::where('name', 'ENCARGADO_DOCENTE')->where('guard_name', 'web')->first();
        if ($encargadoDocente) {
            $encargadoDocente->givePermissionTo(['crear_extensiones_universitarias', 'gestionar_postulaciones_extensiones_universitarias']);
        }

        $administrador = Role::where('name', 'ADMINISTRADOR_EXTENSION')->where('guard_name', 'web')->first();
        if ($administrador) {
            $administrador->givePermissionTo(['gestionar_postulaciones_extensiones_universitarias']);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'ver_catalogo_extensiones_alumnos_pantalla',
            'postular_extensiones_alumnos_pantalla',
            'gestionar_postulaciones_extensiones_universitarias',
        ])->delete();
    }
};
