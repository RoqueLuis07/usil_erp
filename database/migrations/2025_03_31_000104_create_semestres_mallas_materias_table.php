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
        Schema::create('semestres_mallas_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_malla_id')->constrained('semestres_mallas');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('docente_id')->nullable()->constrained('docentes');
            $table->string('aula')->nullable();
            $table->date('fecha_examen_parcial')->nullable();
            $table->date('fecha_examen_ordinario')->nullable();
            $table->date('fecha_examen_complementario')->nullable();
            $table->date('fecha_examen_extraordinario')->nullable();
            $table->string('url_plan_clases')->nullable();
            $table->string('url_programa_clases')->nullable();
            $table->text('observaciones')->nullable();
            $table->char('estado', 2)->default('IN');
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
        Schema::dropIfExists('semestres_mallas_materias');
    }
};
