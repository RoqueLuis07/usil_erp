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
        Schema::create('tutorias_clases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoria_id')->constrained('tutorias');
            $table->timestamp('fecha_hora');
            $table->string('tema_desarrollado');
            $table->integer('horas_desarrollo');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->text('observaciones')->nullable();
            $table->char('estado', 2)->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorias_clases');
    }
};
