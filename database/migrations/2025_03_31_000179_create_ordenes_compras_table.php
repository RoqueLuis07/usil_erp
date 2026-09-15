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
        Schema::create('ordenes_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->char('condicion_compra', 2);
            $table->integer('credito_a')->nullable();
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->decimal('monto_total', 11, 2)->default(0);
            $table->string('url_presupuesto')->nullable();
            $table->text('observaciones')->nullable();
            $table->char('estado', 2)->default('PE');
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
        Schema::dropIfExists('ordenes_compras');
    }
};
