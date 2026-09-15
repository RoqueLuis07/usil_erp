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
        Schema::create('actas_evaluaciones_ubs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta');
            $table->char('tipo', 1);
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->foreignId('docente_id')->constrained('docentes');
            $table->date('fecha_evaluacion');
            $table->char('seccion', 1);
            $table->string('ubicacion_acta')->nullable();
            $table->string('ubicacion_adjunto')->nullable();
            $table->foreignId('generado_por_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas_evaluaciones_ubs');
    }
};
