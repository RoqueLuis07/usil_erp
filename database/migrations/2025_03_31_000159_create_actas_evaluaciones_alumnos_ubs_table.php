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
        Schema::create('actas_evaluaciones_alumnos_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acta_evaluacion_id')->constrained('actas_evaluaciones_ubs');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->integer('puntos_obtenidos');
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
        Schema::dropIfExists('actas_evaluaciones_alumnos_ubs');
    }
};
