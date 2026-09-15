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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('tercer_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('numero_documento');
            $table->foreignId('sexo_id')->nullable()->constrained('sexos');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular');
            $table->string('direccion')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos_paraguay');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades');
            $table->foreignId('barrio_id')->nullable()->constrained('barrios');
            $table->string('email_personal');
            $table->string('email_institucional')->nullable();
            $table->foreignId('nivel_academico_id')->nullable()->constrained('docentes_niveles_academicos');
            $table->boolean('capacitacion_didactica')->nullable();
            $table->foreignId('area_conocimiento_id')->nullable();
            $table->foreignId('dato_laboral_id')->nullable()->constrained('docentes_datos_laborales');
            $table->string('razon_social')->nullable();
            $table->string('ruc')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('tiktok')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->string('url_ubicacion_foto')->nullable();
            $table->boolean('ubs')->nullable()->default(false);
            $table->boolean('tutor_tesis')->nullable()->default(false);
            $table->foreignId('banco_id')->nullable()->constrained('bancos');
            $table->char('tipo_cuenta_bancaria', 2)->nullable();
            $table->string('numero_cuenta_bancaria')->nullable();
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
        Schema::dropIfExists('docentes');
    }
};
