<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Mallas espejo y certificados de estudio SIU (programa de doble titulación de la universidad anterior)
 * se quitaron del sistema: se eliminan sus permisos. Las tablas de datos no se tocan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Permission::all()
            ->filter(fn ($permiso) => str_contains($permiso->name, 'mallas_espejo') || str_contains($permiso->name, 'estudios_siu'))
            ->each->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
    }
};
