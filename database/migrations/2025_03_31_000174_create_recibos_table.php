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
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero');
            $table->timestamp('fecha');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->text('concepto');
            $table->decimal('monto_total', 11, 2);
            $table->foreignId('forma_pago_id')->constrained('formas_pagos');
            $table->char('estado', 2)->default('AC');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('anulado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
