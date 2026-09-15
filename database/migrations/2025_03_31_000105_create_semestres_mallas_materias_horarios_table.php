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
        Schema::create('semestres_mallas_materias_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_malla_materia_id')->nullable()->constrained('semestres_mallas_materias', 'id', 'sem_malla_mat_horarios_smm_id_foreign');
            $table->foreignId('dia_semana_id')->nullable()->constrained('dias_semanas');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semestres_mallas_materias_horarios');
    }
};
