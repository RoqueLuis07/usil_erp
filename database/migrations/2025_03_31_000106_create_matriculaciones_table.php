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
        Schema::create('matriculaciones', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('carrera_siu_id')->nullable()->constrained('carreras');
            $table->char('tipo_pago', 2);
            $table->foreignId('convenio_id')->nullable()->constrained('convenios');
            $table->bigInteger('venta_id')->nullable();
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
        Schema::dropIfExists('matriculaciones');
    }
};
