<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('extensiones_universitarias_detalles', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable()->after('alumno_id')->constrained('carreras', 'id', 'ext_univ_detalles_carrera_id_foreign');
            $table->foreignId('semestre_id')->nullable()->after('carrera_id')->constrained('semestres', 'id', 'ext_univ_detalles_semestre_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extensiones_universitarias_detalles', function (Blueprint $table) {
            $table->dropForeign('ext_univ_detalles_carrera_id_foreign');
            $table->dropForeign('ext_univ_detalles_semestre_id_foreign');
            $table->dropColumn(['carrera_id', 'semestre_id']);
        });
    }
};
