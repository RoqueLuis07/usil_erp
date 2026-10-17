<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * La Escuela de Negocios (UBS) pertenecía a la universidad anterior y se quitó del sistema:
 * se eliminan sus permisos (y con ellos las asignaciones a roles). Las tablas de datos no se tocan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Permission::all()->filter(fn ($permiso) => str_contains($permiso->name, '_ubs'))->each->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Los permisos de UBS ya no existen en el sistema.
    }
};
