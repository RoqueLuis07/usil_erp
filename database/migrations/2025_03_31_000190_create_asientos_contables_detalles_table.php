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
        Schema::create('asientos_contables_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asiento_id')->constrained('asientos_contables');
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables');
            $table->foreignId('centro_costo_id')->nullable()->constrained('centros_costos_contables');
            $table->foreignId('subcentro_costo_id')->nullable()->constrained('subcentros_costos_contables');
            $table->text('descripcion');
            $table->decimal('debe', 11, 2)->nullable();
            $table->decimal('haber', 11, 2)->nullable();
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_contables_detalles');
    }
};
