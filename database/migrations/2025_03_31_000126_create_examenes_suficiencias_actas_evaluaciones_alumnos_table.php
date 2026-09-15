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
        Schema::create('examenes_suficiencias_actas_evaluaciones_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acta_evaluacion_id')->constrained('examenes_suficiencias_actas_evaluaciones', 'id', 'exam_sufic_actas_eval_alum_acta_eval_id_foreign');
            $table->foreignId('alumno_id')->constrained('alumnos', 'id', 'exam_sufic_actas_eval_alum_alumno_id_foreign');
            $table->integer('puntos_examen')->nullable();
            $table->string('calificacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examenes_suficiencias_actas_evaluaciones_alumnos');
    }
};
