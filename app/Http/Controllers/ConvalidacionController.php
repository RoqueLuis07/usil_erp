<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Convalidacion;


class ConvalidacionController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        if ($this->authorize('ver_convalidaciones_externas') || $this->authorize('ver_convalidaciones_internas')) {
            try {
                $convalidaciones = collect();

                if (Auth::user()->can('ver_convalidaciones_externas')) {
                    $convalidaciones_externas = Convalidacion::where('tipo', 'EX')->orderBy('id', 'asc')->get();
                    foreach ($convalidaciones_externas as $convalidacion) {
                        $convalidaciones->push($convalidacion);
                    }
                }

                if (Auth::user()->can('ver_convalidaciones_internas')) {
                    $convalidaciones_internas = Convalidacion::where('tipo', 'IN')->orderBy('id', 'asc')->get();
                    foreach ($convalidaciones_internas as $convalidacion) {
                        $convalidaciones->push($convalidacion);
                    }
                }

                return view('convalidaciones/index')->with(compact('convalidaciones'));
            } catch (\Exception $e) {
                return redirect()->route('convalidaciones.index')->with('error-message', $e->getMessage());
            }
        } else {
            abort(403);
        }
    }

    public function show($id)
    {
        if ($this->authorize('ver_convalidaciones_externas') || $this->authorize('ver_convalidaciones_internas')) {
            try {
                $convalidacion = Convalidacion::with('convalidacionDetalles')->findOrFail($id);

                return view('convalidaciones/show')->with(compact('convalidacion'));
            } catch (\Exception $e) {
                return redirect()->route('convalidaciones.index')->with('error-message', $e->getMessage());
            }
        } else {
            abort(403);
        }
    }

}
