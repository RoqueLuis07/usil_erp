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
        Schema::create('examenes_suficiencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('docente_id')->nullable()->constrained('docentes');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->date('fecha_examen');
            $table->string('aula_examen')->nullable();
            $table->integer('puntos_obtenidos')->nullable();
            $table->string('calificacion')->nullable();
            $table->string('url_ubicacion')->nullable();
            $table->char('estado', 2)->default('AC');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examenes_suficiencias');
    }
};
