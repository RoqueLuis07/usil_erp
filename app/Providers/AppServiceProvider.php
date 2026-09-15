<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\Configuracion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL::forceScheme('https'); // activar para produccion

        // $usuario = User::where('id', 1)->first();
        // if ($usuario && $usuario->role_id == 1) {
        //     if (!$usuario->hasRole('SUPERADMIN')) {
        //         $permissions = Permission::get();
        //         $role = Role::where('id', 1)->first();
        //         if ($role) {
        //             $role->syncPermissions($permissions);


        //             $usuario->assignRole($role);
        //         }
        //     }
        // }


        View::composer('*', function($view) {
            if (Auth::check()) {
                $configuracion = Configuracion::where('user_id', Auth::id())->first();
            } else {
                $configuracion = [
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
                ];
            }
            View::share('configuracion', $configuracion);
        });
    }
}
