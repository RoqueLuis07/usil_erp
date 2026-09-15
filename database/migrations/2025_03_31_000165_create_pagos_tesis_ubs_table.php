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
        Schema::create('pagos_tesis_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones_temas_tesis_ubs');
            $table->string('descripcion');
            $table->date('vencimiento');
            $table->decimal('monto', 11, 2);
            $table->timestamp('fecha_pago')->nullable();
            $table->decimal('saldo', 11, 2);
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_tesis_ubs');
    }
};
