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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->integer('impuesto');
            $table->timestamp('ultima_compra')->nullable();
            $table->timestamp('ultima_venta')->nullable();
            $table->boolean('tiene_stock');
            $table->string('compra_venta');
            $table->string('carrera_curso')->nullable();
            $table->integer('stock')->nullable();
            $table->foreignId('centro_costo_id')->constrained('centros_costos_contables');
            $table->foreignId('subcentro_costo_id')->constrained('subcentros_costos_contables');
            $table->foreignId('unidad_id')->constrained('unidades_negocios_contables');
            $table->foreignId('subunidad_id')->constrained('subunidades_negocios_contables');
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
        Schema::dropIfExists('articulos');
    }
};
