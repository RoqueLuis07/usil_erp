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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_fantasia');
            $table->string('nombre_real');
            $table->string('abreviatura');
            $table->foreignId('programa_id')->constrained('programas');
            $table->foreignId('facultad_id')->constrained('facultades');
            $table->foreignId('tipo_carrera_id')->constrained('tipos_carreras');
            $table->boolean('doble_grado');
            $table->integer('cantidad_semestres');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->string('numero_ley')->nullable();
            $table->string('numero_acta')->nullable();
            $table->string('numero_resolucion_cones')->nullable();
            $table->foreignId('articulo_id')->nullable()->constrained('articulos');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->char('estado', 2)->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
