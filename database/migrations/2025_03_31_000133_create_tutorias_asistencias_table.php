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
        Schema::create('tutorias_asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clase_id')->constrained('tutorias_clases');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->timestamp('fecha');
            $table->char('estado', 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorias_asistencias');
    }
};
