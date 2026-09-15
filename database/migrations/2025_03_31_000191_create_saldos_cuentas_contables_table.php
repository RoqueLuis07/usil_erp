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
        Schema::create('saldos_cuentas_contables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables');
            $table->decimal('saldo', 11, 2)->default(0);
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos_cuentas_contables');
    }
};
