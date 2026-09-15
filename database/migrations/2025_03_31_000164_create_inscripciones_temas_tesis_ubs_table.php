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
        Schema::create('inscripciones_temas_tesis_ubs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('linea_id')->constrained('lineas_tesis_ubs');
            $table->foreignId('tutor_id')->constrained('docentes');
            $table->string('tema');
            $table->text('titulo');
            $table->foreignId('fecha_defensa_id')->nullable()->constrained('fechas_defensas_tesis_ubs');
            $table->string('calificacion')->nullable();
            $table->boolean('acta_generado')->default(false);
            $table->foreignId('aprobado_calidad_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_calidad')->nullable();
            $table->foreignId('aprobado_tutor_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_tutor')->nullable();
            $table->foreignId('rechazado_por_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_rechazo')->nullable();
            $table->char('pagado', 2)->default('PE');
            $table->timestamp('fecha_pago')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones_temas_tesis_ubs');
    }
};
