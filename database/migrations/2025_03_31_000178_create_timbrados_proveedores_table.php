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
        Schema::create('timbrados_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->integer('numero');
            $table->date('valido_desde');
            $table->date('valido_hasta');
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
        Schema::dropIfExists('timbrados_proveedores');
    }
};
