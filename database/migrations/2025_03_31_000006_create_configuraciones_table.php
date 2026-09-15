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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained('usuarios');
             $table->string('lang');
             $table->string('data_layout');
             $table->string('data_sidebar');
             $table->string('data_sidebar_size');
             $table->string('card_layout')->nullable();
             $table->string('data_bs_theme');
             $table->string('data_layout_width');
             $table->string('data_sidebar_image');
             $table->string('data_layout_position');
             $table->string('data_layout_style');
             $table->string('data_topbar');
             $table->string('data_preloader');
             $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
