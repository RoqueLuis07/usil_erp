<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Permiso para deshacer cargas masivas desde el historial de importaciones.
 * Lo tienen SUPERADMIN y ADMINISTRADOR_EXTENSION (quien importa debe poder deshacer su propia carga).
 */
return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'revertir_importaciones', 'guard_name' => 'web']);

        foreach (['SUPERADMIN', 'ADMINISTRADOR_EXTENSION'] as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->where('guard_name', 'web')->first();
            if ($rol) {
                $rol->givePermissionTo('revertir_importaciones');
            }
        }
    }

    public function down(): void
    {
        Permission::where('name', 'revertir_importaciones')->delete();
    }
};
