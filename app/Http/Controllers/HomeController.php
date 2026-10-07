<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

use App\Models\Alumno;
use App\Models\AlumnoNota;
use App\Models\Malla;
use App\Models\SemestreMalla;


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
        } elseif (Auth::user()->hasRole('ADMINISTRADOR_EXTENSION')) {
            // Este rol vive enteramente dentro del apartado de Extensión (diseño
            // nuevo); el dashboard genérico de abajo es compartido con SUPERADMIN
            // y el resto del ERP, así que lo salteamos para que el cambio de
            // diseño se note desde el mismo login.
            return redirect()->route('extensiones_universitarias.index');
        } else {
            return view('index');
        }
    }
}
