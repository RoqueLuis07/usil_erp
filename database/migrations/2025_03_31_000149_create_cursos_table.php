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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_fantasia');
            $table->string('nombre_real');
            $table->string('codigo')->nullable();
            $table->foreignId('programa_id')->nullable()->constrained('programas');
            $table->foreignId('facultad_id')->nullable()->constrained('facultades');
            $table->foreignId('tipo_curso_id')->constrained('tipos_cursos');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->integer('llamado');
            $table->date('fecha_apertura');
            $table->date('fecha_fin');
            $table->integer('cantidad_creditos')->nullable();
            $table->integer('cantidad_horas')->nullable();
            $table->integer('duracion')->nullable();
            $table->boolean('evaluacion')->default(false);
            $table->boolean('es_maestria')->default(false);
            $table->string('numero_ley')->nullable();
            $table->string('numero_acta')->nullable();
            $table->string('numero_resolucion_cones')->nullable();
            $table->string('url_cronograma')->nullable();
            $table->foreignId('articulo_id')->nullable()->constrained('articulos');
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
        Schema::dropIfExists('cursos');
    }
};
