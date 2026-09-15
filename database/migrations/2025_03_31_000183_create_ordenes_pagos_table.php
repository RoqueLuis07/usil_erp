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
        Schema::create('ordenes_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->nullable()->constrained('compras');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->char('tipo', 2);
            $table->foreignId('forma_pago_id')->constrained('formas_pagos');
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->foreignId('cotizacion_id')->nullable()->constrained('cotizaciones');
            $table->foreignId('cuenta_contable_id')->nullable()->constrained('cuentas_contables');
            $table->foreignId('unidad_negocio_id')->nullable()->constrained('unidades_negocios_contables');
            $table->foreignId('subunidad_negocio_id')->nullable()->constrained('subunidades_negocios_contables');
            $table->decimal('monto_total', 11, 2)->default(0);
            $table->text('concepto');
            $table->char('estado', 2)->default('PE');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('aprobado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('anulado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pagos');
    }
};
