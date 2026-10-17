<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'gestionar_parametros_extensiones_universitarias', 'guard_name' => 'web']);

        foreach (['SUPERADMIN', 'ADMINISTRADOR_EXTENSION'] as $rol) {
            $role = Role::where('name', $rol)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('gestionar_parametros_extensiones_universitarias');
            }
        }
    }

    public function down(): void
    {
        Permission::where('name', 'gestionar_parametros_extensiones_universitarias')->delete();
    }
};
