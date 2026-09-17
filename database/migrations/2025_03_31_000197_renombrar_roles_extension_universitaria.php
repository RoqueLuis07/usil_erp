<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Renombra los roles del primer recorte (ENCARGADO_EXTENSION, DOCENTE) a los
     * nombres reales del modelo de 4 roles: ADMINISTRADOR_EXTENSION y
     * ENCARGADO_DOCENTE. Solo cambia el campo `name` de la fila existente en
     * `roles` — los usuarios ya asignados (por role_id) conservan su rol sin
     * cambios, así que esto es seguro de correr contra una base ya sembrada
     * (como el deploy de Railway) sin perder datos.
     */
    public function up(): void
    {
        DB::table('roles')->where('name', 'ENCARGADO_EXTENSION')->where('guard_name', 'web')->update(['name' => 'ADMINISTRADOR_EXTENSION']);
        DB::table('roles')->where('name', 'DOCENTE')->where('guard_name', 'web')->update(['name' => 'ENCARGADO_DOCENTE']);
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'ADMINISTRADOR_EXTENSION')->where('guard_name', 'web')->update(['name' => 'ENCARGADO_EXTENSION']);
        DB::table('roles')->where('name', 'ENCARGADO_DOCENTE')->where('guard_name', 'web')->update(['name' => 'DOCENTE']);
    }
};
