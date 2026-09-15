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
        Schema::create('cursos_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->integer('cantidad_cuotas')->nullable();
            $table->decimal('precio_contado', 11, 2)->nullable();
            $table->decimal('precio_cuota', 11, 2)->nullable();
            $table->decimal('precio_multa', 11, 2)->nullable();
            $table->integer('dia_vencimiento_cuota')->nullable();
            $table->date('fecha_inicio_vencimiento_cuota')->nullable();
            $table->integer('dias_gracia')->nullable();
            $table->decimal('precio_defensa', 11, 2)->nullable();
            $table->decimal('precio_titulo', 11, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos_precios');
    }
};
