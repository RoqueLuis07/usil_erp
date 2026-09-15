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
        Schema::create('inscripciones_temas_tesis', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('tipo_id')->constrained('tipos_tesis');
            $table->foreignId('area_id')->constrained('areas_tesis');
            $table->foreignId('linea_id')->constrained('lineas_tesis');
            $table->foreignId('tutor_id')->constrained('docentes');
            $table->string('lugar_investigacion')->nullable();
            $table->string('tema');
            $table->text('justificacion');
            $table->timestamp('fecha_defensa')->nullable();
            $table->string('calificacion')->nullable();
            $table->boolean('acta_generado')->default(false);
            $table->foreignId('aprobado_coordinacion_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_coordinacion')->nullable();
            $table->foreignId('aprobado_tutor_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_tutor')->nullable();
            $table->foreignId('rechazado_por_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_rechazo')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones_temas_tesis');
    }
};
