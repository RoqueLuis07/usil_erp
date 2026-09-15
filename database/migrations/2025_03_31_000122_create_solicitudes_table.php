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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_solicitud');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('tipo_solicitud_id')->constrained('tipos_solicitudes');
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('programa_id')->constrained('programas');
            $table->date('fecha_inasistencia')->nullable();
            $table->bigInteger('tutoria_id')->nullable();
            $table->foreignId('modalidad_id')->nullable()->constrained('modalidades');
            $table->text('observaciones')->nullable();
            $table->string('adjunto')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->char('tipo_generacion', 2)->nullable();
            $table->char('estado', 2)->default('PE');
            $table->foreignId('aprobado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('rechazado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('entregado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
