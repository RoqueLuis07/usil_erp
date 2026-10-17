<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Permiso para el nuevo panel de Auditoría (lee la tabla `audits` que
 * owen-it/laravel-auditing ya viene llenando). Se lo damos a los roles
 * administrativos: SUPERADMIN y ADMINISTRADOR_EXTENSION.
 */
return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'ver_auditoria', 'guard_name' => 'web']);

        foreach (['SUPERADMIN', 'ADMINISTRADOR_EXTENSION'] as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->where('guard_name', 'web')->first();
            if ($rol) {
                $rol->givePermissionTo('ver_auditoria');
            }
        }
    }

    public function down(): void
    {
        Permission::where('name', 'ver_auditoria')->delete();
    }
};
