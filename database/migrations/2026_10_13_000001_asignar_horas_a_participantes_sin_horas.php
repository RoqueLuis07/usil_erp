<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Los alumnos cargados al crear una actividad quedaban con las horas vacías
 * (se veían como "0 horas"). Ahora heredan las de la actividad; esto corrige
 * los que ya existen, solo en actividades todavía abiertas (pendiente,
 * aprobada o informada) y solo donde el valor es nulo: un 0 cargado a mano
 * se respeta.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('extensiones_universitarias_detalles as d')
            ->join('extensiones_universitarias as e', 'e.id', '=', 'd.extension_universitaria_id')
            ->where('d.estado', 'AC')
            ->whereNull('d.cantidad_horas')
            ->whereIn('e.estado', ['PE', 'AP', 'IN'])
            ->update(['d.cantidad_horas' => DB::raw('e.cantidad_horas')]);
    }

    public function down(): void
    {
        // Sin reversa: no se puede distinguir qué filas estaban vacías antes.
    }
};
