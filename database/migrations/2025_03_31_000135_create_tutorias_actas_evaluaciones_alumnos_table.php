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
        Schema::create('tutorias_actas_evaluaciones_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acta_evaluacion_id')->constrained('tutorias_actas_evaluaciones');
            $table->foreignId('alumno_id')->constrained('alumnos');
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
        Schema::dropIfExists('tutorias_actas_evaluaciones_alumnos');
    }
};
