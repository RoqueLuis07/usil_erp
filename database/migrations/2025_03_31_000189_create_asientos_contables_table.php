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
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->timestamp('fecha');
            $table->string('origen');
            $table->foreignId('unidad_negocio_id')->nullable()->constrained('unidades_negocios_contables');
            $table->foreignId('subunidad_negocio_id')->nullable()->constrained('subunidades_negocios_contables');
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            $table->foreignId('nota_credito_id')->nullable()->constrained('notas_creditos');
            $table->foreignId('compra_id')->nullable()->constrained('compras');
            $table->foreignId('orden_pago_id')->nullable()->constrained('ordenes_pagos');
            $table->foreignId('movimiento_caja_id')->nullable()->constrained('movimientos_cajas');
            $table->foreignId('movimiento_banco_id')->nullable()->constrained('movimientos_bancos');
            $table->foreignId('movimiento_caja_banco_id')->nullable()->constrained('movimientos_cajas_bancos');
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->foreignId('cotizacion_id')->nullable()->constrained('cotizaciones');
            $table->char('estado', 2)->default('AC');
            $table->foreignId('cargado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};
