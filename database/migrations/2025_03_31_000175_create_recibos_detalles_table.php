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
        Schema::create('recibos_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recibo_id')->constrained('recibos');
            $table->foreignId('venta_id')->constrained('ventas');
            $table->decimal('monto', 11, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos_detalles');
    }
};
