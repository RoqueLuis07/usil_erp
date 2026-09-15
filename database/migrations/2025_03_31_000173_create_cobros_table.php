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
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            $table->foreignId('forma_pago_id')->nullable()->constrained('formas_pagos');
            $table->decimal('monto', 11, 2);
            $table->foreignId('caja_id')->nullable()->constrained('cajas');
            $table->foreignId('banco_id')->nullable()->constrained('bancos');
            $table->foreignId('nota_credito_id')->nullable()->constrained('notas_creditos');
            $table->string('numero_transaccion')->nullable();
            $table->date('fecha_transaccion')->nullable();
            $table->foreignId('cuenta_bancaria_id')->nullable()->constrained('cuentas_bancarias');
            $table->char('estado', 2)->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};
