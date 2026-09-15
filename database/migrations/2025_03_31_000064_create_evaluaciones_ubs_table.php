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
        Schema::create('evaluaciones_ubs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('tipo_evaluacion_id')->constrained('tipos_evaluaciones_ubs');
            $table->integer('puntos');
            $table->integer('valor_porcentual');
            $table->integer('puntaje_minimo_requerido');
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
        Schema::dropIfExists('evaluaciones_ubs');
    }
};
