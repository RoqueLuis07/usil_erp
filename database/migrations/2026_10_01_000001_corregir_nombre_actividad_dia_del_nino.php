<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige un único nombre de actividad que la importación de la planilla
 * (2026_09_30_000001) no pudo reparar automáticamente: el Excel original ya
 * traía el texto con una pérdida de bytes irreversible (no un simple
 * doble-encoding), así que el valor correcto se escribe a mano acá.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('extensiones_universitarias')
            ->where('nombre', 'like', 'DONACIONES POR EL D%A DEL NI%O/A 2026')
            ->update(['nombre' => 'DONACIONES POR EL DÍA DEL NIÑO/A 2026']);
    }

    public function down(): void
    {
        // No se revierte: es una corrección de texto, no hay valor anterior útil.
    }
};
