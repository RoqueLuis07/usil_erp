<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;



class HomeController extends Controller
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
    public function root()
    {
        if (Auth::user()->hasRole('ALUMNO')) { //cambiar el rol por ALUMNO al terminar los roles y permisos
            return redirect()->route('pantallas_alumnos.index', Auth::id());
        } elseif (Auth::user()->hasAnyRole(['DOCENTE', 'ENCARGADO_DOCENTE'])) { //cambiar el rol por DOCENTE al terminar los roles y permisos
            return redirect()->route('pantallas_docentes.index', Auth::id());
        } else {
            // Superadministrador y administradores de extensión: el sistema trabaja solo con Extensión Universitaria.
            return redirect()->route('extensiones_universitarias.index');
        }
    }
}
