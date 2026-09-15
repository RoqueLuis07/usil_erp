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
        Schema::create('tutorias_actas_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta');
            $table->char('tipo', 1);
            $table->foreignId('tutoria_id')->constrained('tutorias');
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
        Schema::dropIfExists('tutorias_actas_evaluaciones');
    }
};
