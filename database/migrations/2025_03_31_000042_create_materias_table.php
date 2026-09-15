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
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('nombre_fantasia');
            $table->string('nombre_real');
            $table->integer('carga_horaria');
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
        Schema::dropIfExists('materias');
    }
};
