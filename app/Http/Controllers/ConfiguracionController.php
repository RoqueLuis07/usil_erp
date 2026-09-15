<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Configuracion;
use App\Models\User;


class ConfiguracionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function light_mode(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $configuracion = Configuracion::where('user_id', $id)->first();
            $configuracion->data_bs_theme = 'light';
            $configuracion->save();

            DB::commit();

            return response()->json([
                'message' => 'Modo claro activado.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function dark_mode(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $configuracion = Configuracion::where('user_id', $id)->first();
            $configuracion->data_bs_theme = 'dark';
            $configuracion->save();

            DB::commit();

            return response()->json([
                'message' => 'Modo oscuro activado.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
