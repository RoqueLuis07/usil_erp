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
        Schema::create('convalidaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_solicitud');
            $table->char('tipo', 2);
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('facultad_id')->constrained('facultades');
            $table->string('facultad_origen');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->string('carrera_origen');
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('universidad_origen_id')->constrained('instituciones_educativas');
            $table->string('ubicacion_certificado_estudios')->nullable();
            $table->string('extension_certificado_estudios')->nullable();
            $table->char('estado', 2);
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
        Schema::dropIfExists('convalidaciones');
    }
};
