<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Las noticias sin portada guardaban "storage/no_image.png", un archivo que no viaja con el código.
 * Ahora apuntan al placeholder que sí está en public/images.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('noticias_avisos')->where('portada', 'storage/no_image.png')->update(['portada' => 'images/placeholders/no_image.svg']);
    }

    public function down(): void
    {
    }
};
