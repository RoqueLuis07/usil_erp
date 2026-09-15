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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha')->nullable();
            $table->foreignId('orden_pago_id')->nullable()->constrained('ordenes_pagos');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('forma_pago_id')->nullable()->constrained('formas_pagos');
            $table->decimal('monto', 11, 2);
            $table->foreignId('caja_id')->nullable()->constrained('cajas');
            $table->foreignId('banco_id')->nullable()->constrained('bancos');
            $table->foreignId('cuenta_bancaria_id')->nullable()->constrained('cuentas_bancarias');
            $table->string('numero_transaccion')->nullable();
            $table->date('fecha_transaccion')->nullable();
            $table->string('numero_cheque')->nullable();
            $table->string('numero_serie_cheque')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
