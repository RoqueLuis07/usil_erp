<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convierte `extensiones_universitarias_detalles` (hasta ahora una simple
     * lista de alumnos ya asignados por un docente/admin) en un registro real
     * de postulación: PE (pendiente de revisión), AC (aceptada), RE (rechazada).
     */
    public function up(): void
    {
        Schema::table('extensiones_universitarias_detalles', function (Blueprint $table) {
            $table->char('estado', 2)->default('PE')->after('semestre_id');
            $table->timestamp('fecha_postulacion')->nullable()->after('estado');
            $table->foreignId('revisado_por_id')->nullable()->after('fecha_postulacion')->constrained('usuarios', 'id', 'ext_univ_detalles_revisado_por_id_foreign');
            $table->timestamp('fecha_revision')->nullable()->after('revisado_por_id');
            $table->string('motivo_rechazo')->nullable()->after('fecha_revision');
        });

        // Las filas que ya existían antes de este cambio fueron cargadas
        // directamente por un docente/admin (no pasaron por postulación), así
        // que se consideran ya aceptadas.
        DB::table('extensiones_universitarias_detalles')->update(['estado' => 'AC']);
    }

    public function down(): void
    {
        Schema::table('extensiones_universitarias_detalles', function (Blueprint $table) {
            $table->dropForeign('ext_univ_detalles_revisado_por_id_foreign');
            $table->dropColumn(['estado', 'fecha_postulacion', 'revisado_por_id', 'fecha_revision', 'motivo_rechazo']);
        });
    }
};
