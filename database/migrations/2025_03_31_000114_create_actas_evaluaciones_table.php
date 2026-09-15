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
        Schema::create('actas_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta');
            $table->char('tipo', 1);
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('docente_id')->constrained('docentes');
            $table->date('fecha_evaluacion');
            $table->integer('materia_semestre');
            $table->char('seccion', 1);
            $table->string('ubicacion_acta')->nullable();
            $table->string('ubicacion_adjunto')->nullable();
            $table->foreignId('generado_por_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas_evaluaciones');
    }
};
