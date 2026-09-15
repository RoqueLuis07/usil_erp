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
        Schema::create('alumnos', function (Blueprint $table) {
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
            $table->string('email_personal');
            $table->string('email_institucional')->nullable();
            $table->foreignId('formacion_id')->nullable()->constrained('alumnos_formaciones');
            $table->foreignId('institucion_educativa_id')->nullable()->constrained('instituciones_educativas');
            $table->string('anho_egreso_educativo')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos_paraguay');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades');
            $table->foreignId('barrio_id')->nullable()->constrained('barrios');
            $table->foreignId('familiar_uno_id')->nullable()->constrained('alumnos_familiares');
            $table->foreignId('familiar_dos_id')->nullable()->constrained('alumnos_familiares');
            $table->foreignId('dato_laboral_id')->nullable()->constrained('alumnos_datos_laborales');
            $table->string('url_ubicacion_foto')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('tiktok')->nullable();
            $table->char('estado', 2)->default('AC');
            $table->boolean('ubs')->default(false);
            $table->foreignId('forma_conocimiento_id')->nullable()->constrained('formas_conocimientos');
            $table->text('link_crm')->nullable();
            $table->text('url_certificado_estudio')->nullable();
            $table->text('url_certificado_estudio_siu')->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('alumnos');
    }
};
