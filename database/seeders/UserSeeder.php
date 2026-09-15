<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.test'),
            'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
            'role_id' => 1,
            'avatar' => 'masculino.jpg',
            'portada' => 'no_portada.jpg',
            'state' => 'AC',
        ]);

        // role_id enlaza a la tabla `roles` propia del sistema, pero los checks de
        // permisos (Auth::user()->can(...)) usan las tablas pivote de Spatie, que
        // requieren asignar el rol explícitamente vía HasRoles.
        $user->assignRole('SUPERADMIN');

        DB::table('configuraciones')->insert([
            'user_id' => $user->id,
            'lang' => 'sp',
            'data_layout' => 'vertical',
            'data_sidebar' => 'dark',
            'data_sidebar_size' => 'lg',
            'card_layout' => null,
            'data_bs_theme' => 'light',
            'data_layout_width' => 'fluid',
            'data_sidebar_image' => 'none',
            'data_layout_position' => 'fixed',
            'data_layout_style' => 'default',
            'data_topbar' => 'warning',
            'data_preloader' => 'disable'
        ]);
    }
}
