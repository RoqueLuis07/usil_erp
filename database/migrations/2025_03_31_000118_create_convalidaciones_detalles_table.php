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
        Schema::create('convalidaciones_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convalidacion_id')->constrained('convalidaciones');
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->string('materia_origen');
            $table->integer('carga_horaria_materia_origen')->nullable();
            $table->string('calificacion_origen');
            $table->decimal('porcentaje_coincidencia_bruta', 11, 2)->nullable();
            $table->string('numero_dictamen')->nullable();
            $table->date('fecha_dictamen')->nullable();
            $table->string('ubicacion_dictamen')->nullable();
            $table->string('extension_dictamen')->nullable();
            $table->string('numero_resolucion')->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->string('ubicacion_resolucion')->nullable();
            $table->string('extension_resolucion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convalidaciones_detalles');
    }
};
