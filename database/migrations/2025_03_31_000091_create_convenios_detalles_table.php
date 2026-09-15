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
        Schema::create('convenios_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convenio_id')->constrained('convenios');
            $table->char('tipo_matricula', 2)->nullable();
            $table->decimal('descuento_matricula', 11, 2)->nullable();
            $table->integer('porcentaje_matricula')->nullable();
            $table->char('tipo_contado', 2)->nullable();
            $table->decimal('descuento_contado', 11, 2)->nullable();
            $table->integer('porcentaje_contado')->nullable();
            $table->char('tipo_cuotas', 2)->nullable();
            $table->char('aplica_a_cuotas', 2)->nullable();
            $table->decimal('descuento_cuota_1', 11, 2)->nullable();
            $table->integer('porcentaje_cuota_1')->nullable();
            $table->decimal('descuento_cuota_2', 11, 2)->nullable();
            $table->integer('porcentaje_cuota_2')->nullable();
            $table->decimal('descuento_cuota_3', 11, 2)->nullable();
            $table->integer('porcentaje_cuota_3')->nullable();
            $table->decimal('descuento_cuota_4', 11, 2)->nullable();
            $table->integer('porcentaje_cuota_4')->nullable();
            $table->decimal('descuento_cuota_5', 11, 2)->nullable();
            $table->integer('porcentaje_cuota_5')->nullable();
            $table->decimal('descuento_cuotas', 11, 2)->nullable();
            $table->integer('porcentaje_cuotas')->nullable();
            $table->char('tipo_descuento', 2)->nullable();
            $table->decimal('precio_descuento', 11, 2)->nullable();
            $table->integer('porcentaje_descuento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios_detalles');
    }
};
