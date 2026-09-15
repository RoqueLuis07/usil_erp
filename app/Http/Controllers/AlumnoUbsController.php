<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Luecano\NumeroALetras\NumeroALetras;

use App\Models\Alumno;
use App\Models\Sexo;
use App\Models\Nacionalidad;
use App\Models\AlumnoFormacion;
use App\Models\InstitucionEducativa;
use App\Models\DepartamentoParaguay;
use App\Models\Ciudad;
use App\Models\Barrio;
use App\Models\RelacionFamiliar;
use App\Models\User;
use App\Models\Configuracion;
use App\Models\AlumnoNacionalidad;
use App\Models\AlumnoFamiliar;
use App\Models\AlumnoDatoLaboral;
use App\Models\TipoLegajo;
use App\Models\AlumnoLegajo;
use App\Models\AlumnoAsistencia;
use App\Models\ClaseMateria;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\SemestreMalla;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\Materia;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\AlumnoNota;
use App\Models\Cliente;
use App\Models\AlumnoCliente;
use App\Models\FormaConocimiento;


class AlumnoUbsController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_alumnos_ubs');

        try {
			$buscar = Str::upper($request->buscar);

            $alumnos = Alumno::where('ubs', true)->orderBy('id', 'desc')->paginate(50);

			if (!(blank($buscar))) {
				$alumnos = Alumno::where('ubs', true)
					->where(function ($query) use ($buscar) {
						$buscarLike = '%' . str_replace(' ', '%', $buscar) . '%';

						$query->where('id', 'LIKE', $buscarLike)
							  ->orWhere('primer_nombre', 'LIKE', $buscarLike)
							  ->orWhere('segundo_nombre', 'LIKE', $buscarLike)
							  ->orWhere('tercer_nombre', 'LIKE', $buscarLike)
							  ->orWhere('primer_apellido', 'LIKE', $buscarLike)
							  ->orWhere('segundo_apellido', 'LIKE', $buscarLike)
							  ->orWhere('numero_documento', 'LIKE', $buscarLike);
					})
					->paginate(50);
			}
            return view('ubs/alumnos/index')->with(compact('alumnos', 'buscar'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_alumnos_ubs');

        try {
            $alumno = Alumno::with(['alumnoClientes' => function ($query) {
                $query->orderBy('es_principal', 'desc');
            }])->findOrFail($id);
            $alumno->load('nacionalidades');
            $nombre_alumno = $alumno->primer_nombre;
            if ($alumno->segundo_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_nombre;
            }
            if ($alumno->tercer_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->tercer_nombre;
            }
            $nombre_alumno = $nombre_alumno . ' ' . $alumno->primer_apellido;
            if ($alumno->segundo_apellido) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_apellido;
            }

            if ($alumno->familiar_uno_id) {
                $nombre_familiar_uno = $alumno->familiarUno->primer_nombre;
                if ($alumno->familiarUno->segundo_nombre) {
                    $nombre_familiar_uno = $nombre_familiar_uno . ' ' . $alumno->familiarUno->segundo_nombre;
                }
                if ($alumno->familiarUno->tercer_nombre) {
                    $nombre_familiar_uno = $nombre_familiar_uno . ' ' . $alumno->familiarUno->tercer_nombre;
                }
                $nombre_familiar_uno = $nombre_familiar_uno . ' ' . $alumno->familiarUno->primer_apellido;
                if ($alumno->familiarUno->segundo_apellido) {
                    $nombre_familiar_uno = $nombre_familiar_uno . ' ' . $alumno->familiarUno->segundo_apellido;
                }
            } else {
                $nombre_familiar_uno = '';
            }

            if ($alumno->familiar_dos_id) {
                $nombre_familiar_dos = $alumno->familiarDos->primer_nombre;
                if ($alumno->familiarDos->segundo_nombre) {
                    $nombre_familiar_dos = $nombre_familiar_dos . ' ' . $alumno->familiarDos->segundo_nombre;
                }
                if ($alumno->familiarDos->tercer_nombre) {
                    $nombre_familiar_dos = $nombre_familiar_dos . ' ' . $alumno->familiarDos->tercer_nombre;
                }
                $nombre_familiar_dos = $nombre_familiar_dos . ' ' . $alumno->familiarDos->primer_apellido;
                if ($alumno->familiarDos->segundo_apellido) {
                    $nombre_familiar_dos = $nombre_familiar_dos . ' ' . $alumno->familiarDos->segundo_apellido;
                }
            } else {
                $nombre_familiar_dos = '';
            }

            return view('ubs/alumnos/show')->with(compact('alumno', 'nombre_alumno', 'nombre_familiar_uno', 'nombre_familiar_dos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_alumnos_ubs');

        try {
            $sexos = Sexo::where('estado', 'AC')->get();
            $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
            $alumnos_formaciones = AlumnoFormacion::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $instituciones_educativas = InstitucionEducativa::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $departamentos_paraguay = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            $ciudades = Ciudad::orderBy('nombre', 'asc')->get();
            $barrios = Barrio::orderBy('nombre', 'asc')->get();
            $relaciones_familiares = RelacionFamiliar::where('estado', 'AC')->get();
            $usuarios = User::where('state', 'AC')->get();
            $clientes = Cliente::where('estado', 'AC')->get();
            $formas_conocimientos = FormaConocimiento::get();
            return view('ubs/alumnos/create')->with(compact('sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios', 'clientes', 'formas_conocimientos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_alumnos_ubs');

        $request->validate([
            'primer_nombre_alumno' => 'required',
            'segundo_nombre_alumno' => 'nullable',
            'tercer_nombre_alumno' => 'nullable',
            'primer_apellido_alumno' => 'required',
            'segundo_apellido_alumno' => 'nullable',
            'numero_documento' => ['required', Rule::unique('alumnos')],
            'sexo' => [/*'required'*/'nullable', 'numeric'],
            'fecha_nacimiento' => [/*'required'*/'nullable', 'date'],
            'telefono' => 'nullable',
            'celular' => 'required',
            'nacionalidad' => [/*'required'*/'nullable', 'array'],
            'email_personal' => ['required', 'email'],
            'direccion' => /*'required',*/'nullable',
            'departamento' => ['nullable', 'numeric'],
            'ciudad' => [/*'required'*/ 'nullable', 'numeric'],
            'barrio' => [/*'required'*/'nullable', 'numeric'],
            'forma_conocimiento' => ['nullable', 'numeric'],
            'link_crm' => ['nullable', 'active_url', Rule::unique('alumnos')],
            'observaciones' => ['nullable'],
            'usuario' => ['nullable', 'numeric'],

            'empresa' => 'required',
            'cargo' => 'required',
            'email_laboral' => ['required', 'email'],
            'telefono_laboral' => 'nullable',
            'celular_laboral' => 'required',

            'formacion' => [/*'required'*/'nullable', 'numeric'],
            'institucion_educativa' => [/*'required'*/'nullable', 'numeric'],
            'anho_egreso_educativo' => ['nullable', 'numeric'],

            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'instagram' => 'nullable',
            'linkedin' => 'nullable',
            'tiktok' => 'nullable',

            'primer_nombre_familiar1' => 'nullable',
            'segundo_nombre_familiar1' => 'nullable',
            'tercer_nombre_familiar1' => 'nullable',
            'primer_apellido_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'segundo_apellido_familiar1' => 'nullable',
            'relacion_familiar1' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'email_familiar1' => ['nullable', 'email', 'rquired_with:primer_nombre_familiar1'],

            'primer_nombre_familiar2' => 'nullable',
            'segundo_nombre_familiar2' => 'nullable',
            'tercer_nombre_familiar2' => 'nullable',
            'primer_apellido_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'segundo_apellido_familiar2' => 'nullable',
            'relacion_familiar2' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'email_familiar2' => ['nullable', 'email', 'required_with:primer_nombre_familiar2'],

            'clientes' => ['nullable', 'array'],
            'clientes.*.cliente' => ['nullable', 'numeric'],
            'clientes.*.es_principal' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $alumno = new Alumno();
            $alumno->primer_nombre = removeAccents(Str::upper($request->primer_nombre_alumno));
            $alumno->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_alumno));
            $alumno->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_alumno));
            $alumno->primer_apellido = removeAccents(Str::upper($request->primer_apellido_alumno));
            $alumno->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_alumno));
            $alumno->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $alumno->sexo_id = $request->sexo;
            $alumno->fecha_nacimiento = $request->fecha_nacimiento;
            $alumno->telefono = $request->telefono;
            $alumno->celular = $request->celular;

            $alumno->email_personal = removeAccents(Str::lower($request->email_personal));
            $alumno->direccion = removeAccents(Str::upper($request->direccion));
            $alumno->departamento_id = $request->departamento;
            $alumno->ciudad_id = $request->ciudad;
            $alumno->barrio_id = $request->barrio;

            $alumno->formacion_id = $request->formacion;
            $alumno->institucion_educativa_id = $request->institucion_educativa;
            $alumno->anho_egreso_educativo = $request->anho_egreso_educativo;

            if ($request->empresa != null) {
                $laboral = new AlumnoDatoLaboral();
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
				$laboral->email = $request->email_laboral;
                $laboral->telefono = $request->telefono_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->save();
                $alumno->dato_laboral_id = $laboral->id;
            }

            if ($request->primer_nombre_familiar1 != null) {
                $familiar1 = new AlumnoFamiliar();
                $familiar1->primer_nombre = removeAccents(Str::upper($request->primer_nombre_familiar1));
                $familiar1->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_familiar1));
                $familiar1->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_familiar1));
                $familiar1->primer_apellido = removeAccents(Str::upper($request->primer_apellido_familiar1));
                $familiar1->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_familiar1));
                $familiar1->relacion_id = $request->relacion_familiar1;
                $familiar1->email = removeAccents(Str::lower($request->email_familiar1));
                $familiar1->celular = $request->celular_familiar1;
                $familiar1->save();
                $alumno->familiar_uno_id = $familiar1->id;
            }

            if ($request->primer_nombre_familiar2 != null) {
                $familiar2 = new AlumnoFamiliar();
                $familiar2->primer_nombre = removeAccents(Str::upper($request->primer_nombre_familiar2));
                $familiar2->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_familiar2));
                $familiar2->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_familiar2));
                $familiar2->primer_apellido = removeAccents(Str::upper($request->primer_apellido_familiar2));
                $familiar2->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_familiar2));
                $familiar2->relacion_id = $request->relacion_familiar2;
                $familiar2->email = removeAccents(Str::lower($request->email_familiar2));
                $familiar2->celular = $request->celular_familiar2;
                $familiar2->save();
                $alumno->familiar_dos_id = $familiar2->id;
            }

            if ($request->usuario != null) {
                $alumno->usuario_id = $request->usuario;
            } else {
                $usuario = new User();
                $usuario->name = removeAccents(Str::upper($request->primer_nombre_alumno)) . ' ' . removeAccents(Str::upper($request->primer_apellido_alumno));
                $usuario->email = removeAccents(Str::lower($request->email_personal));
                $usuario->role_id = 2; //asignar el id de rol alumno
                $usuario->assignRole(2); //asignar el id de rol alumno
                $password = $request->numero_documento . '-' . Str::substr(removeAccents(Str::upper($request->primer_nombre_alumno)), 0, 1) . Str::substr(removeAccents(Str::lower($request->primer_apellido_alumno)), 0, 1);
                $usuario->password = Hash::make($password);
                $usuario->avatar = 'no_image.jpg';
                $usuario->portada = 'no_portada.jpg';
                $usuario->save();

                $configuracion = new Configuracion();
                $configuracion->user_id = $usuario->id;
                $configuracion->lang = 'sp';
                $configuracion->data_layout = 'vertical';
                $configuracion->data_sidebar = 'dark';
                $configuracion->data_sidebar_size = 'lg';
                $configuracion->card_layout = null;
                $configuracion->data_bs_theme = 'light';
                $configuracion->data_layout_width = 'fluid';
                $configuracion->data_sidebar_image = 'none';
                $configuracion->data_layout_position = 'fixed';
                $configuracion->data_layout_style = 'default';
                $configuracion->data_topbar = 'warning';
                $configuracion->data_preloader = 'disable';
                $configuracion->save();

                $alumno->usuario_id = $usuario->id;
            }

            $alumno->ubs = true;
            $alumno->cargado_por_id = Auth::id();
            $alumno->save();

            $alumno->nacionalidades()->sync($request->nacionalidad);

            $nombre_alumno = $alumno->primer_nombre;
            if ($alumno->segundo_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_nombre;
            }
            if ($alumno->tercer_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->tercer_nombre;
            }
            $nombre_alumno = $nombre_alumno . ' ' . $alumno->primer_apellido;
            if ($alumno->segundo_apellido) {
                $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_apellido;
            }

            if (!Cliente::where('numero_documento', $alumno->numero_documento)->exists()) {
                $cliente = new Cliente();
                $cliente->nombre = $nombre_alumno;
                $cliente->razon_social = $nombre_alumno;
                $cliente->numero_documento = $alumno->numero_documento;
                $cliente->sexo_id = $alumno->sexo_id;
                $cliente->fecha_nacimiento = $alumno->fecha_nacimiento;
                $cliente->email = $alumno->email_personal;
                $cliente->observaciones = 'CARGADO AUTOMATICAMENTE DESDE ALUMNOS';
                $cliente->cargado_por_id = Auth::id();
                $cliente->departamento_id = $alumno->departamento_id;
                $cliente->ciudad_id = $alumno->ciudad_id;
                $cliente->barrio_id = $alumno->barrio_id;
                $cliente->celular = $alumno->celular;
                $cliente->telefono = $alumno->telefono;
                $cliente->save();
            } else {
                $cliente = Cliente::where('numero_documento', $alumno->numero_documento)->first();
            }


            if (!Cliente::where('numero_documento', $alumno->numero_documento)->exists()) {
                $cliente = new Cliente();
                $cliente->nombre = $nombre_alumno;
                $cliente->razon_social = $nombre_alumno;
                $cliente->numero_documento = $alumno->numero_documento;
                $cliente->sexo_id = $alumno->sexo_id;
                $cliente->fecha_nacimiento = $alumno->fecha_nacimiento;
                $cliente->email = $alumno->email_personal;
                $cliente->observaciones = 'CARGADO AUTOMATICAMENTE DESDE ALUMNOS';
                $cliente->cargado_por_id = Auth::id();
                $cliente->departamento_id = $alumno->departamento_id;
                $cliente->ciudad_id = $alumno->ciudad_id;
                $cliente->barrio_id = $alumno->barrio_id;
                $cliente->celular = $alumno->celular;
                $cliente->telefono = $alumno->telefono;
                $cliente->save();
            } else {
                $cliente = Cliente::where('numero_documento', $alumno->numero_documento)->first();
            }

            $nulo = true;

            foreach ($request->clientes as $array) {
                if (!is_null($array)) {
                    $nulo = false;
                    break;
                }
            }

            if (!$nulo) {
                $alumno_cliente = new AlumnoCliente ();
                $alumno_cliente->alumno_id = $alumno->id;
                $alumno_cliente->cliente_id = $cliente->id;
                $alumno_cliente->es_principal = true;
                $alumno_cliente->save();
            } else {
                $alumno_cliente = new AlumnoCliente ();
                $alumno_cliente->alumno_id = $alumno->id;
                $alumno_cliente->cliente_id = $cliente->id;
                $alumno_cliente->es_principal = false;
                $alumno_cliente->save();

                foreach ($request->clientes as $array) {
                    $alumno_cliente = new AlumnoCliente();
                    $alumno_cliente->alumno_id = $alumno->id;
                    $alumno_cliente->cliente_id = $array['cliente'];
                    $alumno_cliente->es_principal = $array['es_principal'];
                    $alumno_cliente->save();
                }
            }

            //crear directorio para guardar su legajo
            $directorio = 'public/legajos/alumnos/';
            $nombre_directorio_alumno = $alumno->id . '_' . Str::lower($alumno->primer_nombre) . '_' . Str::lower($alumno->primer_apellido);
            Storage::makeDirectory($directorio);

            DB::commit();

            return response()->json([
                'message' => 'El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue creado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_alumnos_ubs');

        try {
            $alumno = Alumno::with(['alumnoClientes' => function ($query) {
                $query->orderBy('es_principal', 'desc');
            }])->findOrFail($id);
            $alumno->load('nacionalidades');
            $sexos = Sexo::where('estado', 'AC')->get();
            $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
            $alumnos_formaciones = AlumnoFormacion::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $instituciones_educativas = InstitucionEducativa::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $departamentos_paraguay = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            $ciudades = Ciudad::orderBy('nombre', 'asc')->get();
            $barrios = Barrio::orderBy('nombre', 'asc')->get();
            $relaciones_familiares = RelacionFamiliar::where('estado', 'AC')->get();
            $usuarios = User::where('state', 'AC')->get();
            $clientes = Cliente::where('estado', 'AC')->get();
            $formas_conocimientos = FormaConocimiento::get();
            return view('ubs/alumnos/edit')->with(compact('alumno', 'sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios', 'clientes', 'formas_conocimientos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_alumnos_ubs');

        $request->validate([
            'primer_nombre_alumno' => 'required',
            'segundo_nombre_alumno' => 'nullable',
            'tercer_nombre_alumno' => 'nullable',
            'primer_apellido_alumno' => 'required',
            'segundo_apellido_alumno' => 'nullable',
            'numero_documento' => ['required', Rule::unique('alumnos')->ignore($id)],
            'sexo' => [/*'required',*/'nullable', 'numeric'],
            'fecha_nacimiento' => [/*'required'*/'nullable', 'date'],
            'telefono' => 'nullable',
            'celular' => 'required',
            'nacionalidad' => [/*'required'*/'nullable', 'array'],
            'email_personal' => ['required', 'email'],
            'direccion' => /*'required',*/'nullable',
            'departamento' => ['nullable', 'numeric'],
            'ciudad' => [/*'required'*/ 'nullable', 'numeric'],
            'barrio' => [/*'required'*/'nullable', 'numeric'],
            'forma_conocimiento' => ['nullable', 'numeric'],
            'link_crm' => ['nullable', 'active_url', Rule::unique('alumnos')->ignore($id)],
            'observaciones' => ['nullable'],
            'usuario' => ['nullable', 'numeric'],

            'empresa' => 'required',
            'cargo' => 'required',
            'email_laboral' => ['required', 'email'],
            'telefono_laboral' => 'nullable',
            'celular_laboral' => 'required',

            'formacion' => [/*'required'*/'nullable', 'numeric'],
            'institucion_educativa' => [/*'required'*/'nullable', 'numeric'],
            'anho_egreso_educativo' => ['nullable', 'numeric'],

            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'instagram' => 'nullable',
            'linkedin' => 'nullable',
            'tiktok' => 'nullable',

            'primer_nombre_familiar1' => 'nullable',
            'segundo_nombre_familiar1' => 'nullable',
            'tercer_nombre_familiar1' => 'nullable',
            'primer_apellido_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'segundo_apellido_familiar1' => 'nullable',
            'relacion_familiar' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'email_familiar1' => 'nullable',

            'primer_nombre_familiar2' => 'nullable',
            'segundo_nombre_familiar2' => 'nullable',
            'tercer_nombre_familiar2' => 'nullable',
            'primer_apellido_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'segundo_apellido_familiar2' => 'nullable',
            'relacion_familiar' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'email_familiar2' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);

			$cliente = Cliente::where('numero_documento', $alumno->numero_documento)->first();

            $alumno->primer_nombre = removeAccents(Str::upper($request->primer_nombre_alumno));
            $alumno->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_alumno));
            $alumno->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_alumno));
            $alumno->primer_apellido = removeAccents(Str::upper($request->primer_apellido_alumno));
            $alumno->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_alumno));
            $alumno->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $alumno->sexo_id = $request->sexo;
            $alumno->nacionalidades()->sync($request->nacionalidad);
            $alumno->fecha_nacimiento = $request->fecha_nacimiento;
            $alumno->telefono = $request->telefono;
            $alumno->celular = $request->celular;
            $alumno->email_personal = removeAccents(Str::lower($request->email_personal));
            $alumno->email_institucional = removeAccents(Str::lower($request->email_institucional));
            $alumno->direccion = removeAccents(Str::upper($request->direccion));
            $alumno->departamento_id = $request->departamento;
            $alumno->ciudad_id = $request->ciudad;
            $alumno->barrio_id = $request->barrio;
            $alumno->usuario_id = $request->usuario;

            $alumno->formacion_id = $request->formacion;
            $alumno->institucion_educativa_id = $request->institucion_educativa;
            $alumno->anho_egreso_educativo = $request->anho_egreso_educativo;

            if ($request->empresa != null) {
                if ($alumno->dato_laboral_id != null) {
                    $laboral = AlumnoDatoLaboral::findOrFail($alumno->dato_laboral_id);
                } else {
                    $laboral = new AlumnoDatoLaboral();
                }
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
				$laboral->email = $request->email_laboral;
                $laboral->telefono = $request->telefono_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->save();
                $alumno->dato_laboral_id = $laboral->id;
            }

            $alumno->facebook = $request->facebook;
            $alumno->twitter = $request->twitter;
            $alumno->instagram = $request->instagram;
            $alumno->tiktok = $request->tiktok;
            $alumno->linkedin = $request->linkedin;

            if ($request->primer_nombre_familiar1 != null) {
                $familiar1 = AlumnoFamiliar::findOrFail($alumno->familiar_uno_id);
                $familiar1->primer_nombre = removeAccents(Str::upper($request->primer_nombre_familiar1));
                $familiar1->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_familiar1));
                $familiar1->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_familiar1));
                $familiar1->primer_apellido = removeAccents(Str::upper($request->primer_apellido_familiar1));
                $familiar1->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_familiar1));
                $familiar1->relacion_id = $request->relacion_familiar1;
                $familiar1->email = removeAccents(Str::lower($request->email_familiar1));
                $familiar1->celular = $request->celular_familiar1;
                $familiar1->save();
                $alumno->familiar_uno_id = $familiar1->id;
            }

            if ($request->primer_nombre_familiar2 != null) {
                if ($alumno->familiar_dos_id != null) {
                    $familiar2 = AlumnoFamiliar::findOrFail($alumno->familiar_dos_id);
                } else {
                    $familiar2 = new AlumnoFamiliar();
                }
                $familiar2->primer_nombre = removeAccents(Str::upper($request->primer_nombre_familiar2));
                $familiar2->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_familiar2));
                $familiar2->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_familiar2));
                $familiar2->primer_apellido = removeAccents(Str::upper($request->primer_apellido_familiar2));
                $familiar2->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_familiar2));
                $familiar2->relacion_id = $request->relacion_familiar2;
                $familiar2->email = removeAccents(Str::lower($request->email_familiar2));
                $familiar2->celular = $request->celular_familiar2;
                $familiar2->save();
                $alumno->familiar_dos_id = $familiar2->id;
            }

            $usuario = User::findOrFail($alumno->usuario_id);
            if ($alumno->email_institucional != null && $usuario->email != $alumno->email_institucional) {
                $usuario->email = $alumno->email_institucional;
                $usuario->save();
            }

            $alumno->ubs = true;
            $alumno->actualizado_por_id = Auth::id();
            $alumno->save();

			if ($cliente->numero_documento != $alumno->numero_documento) {
				$cliente->numero_documento = $alumno->numero_documento;
				$cliente->save();
			}

            $encontrado = false;
            if ($request->clientes) {
                $ultimo_item = array_key_last($request->clientes);
                AlumnoCliente::where('alumno_id', $alumno->id)->delete();
                foreach ($request->clientes as $key => $array) {
                    $alumno_cliente = new AlumnoCliente();
                    $alumno_cliente->alumno_id = $alumno->id;
                    $alumno_cliente->cliente_id = $array['cliente'];
                    $alumno_cliente->es_principal = $array['es_principal'];
                    $alumno_cliente->save();

					if ($cliente) {
						if ($array['cliente'] == $cliente->id) {
							$encontrado = true;
						}

						if (!$encontrado && $key == $ultimo_item) {
							$alumno_cliente = new AlumnoCliente ();
							$alumno_cliente->alumno_id = $alumno->id;
							$alumno_cliente->cliente_id = $cliente->id;
							$clientes = AlumnoCliente::where('alumno_id', $alumno->id)->get();
							$principal = false;
							foreach ($clientes as $cl) {
								if ($cl->es_principal == true) {
									$principal = true;
								}
							}
							if ($principal) {
								$alumno_cliente->es_principal = false;
							} else {
								$alumno_cliente->es_principal = true;
							}
							$alumno_cliente->save();
						}
					}
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_alumnos_ubs');

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->actualizado_por_id = Auth::id();
            $alumno->estado = 'IN';
            $alumno->save();

            DB::commit();

            return redirect()->route('alumnos_ubs.index')->with('error-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_alumnos_ubs');

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->actualizado_por_id = Auth::id();
            $alumno->estado = 'AC';
            $alumno->save();

            DB::commit();

            return redirect()->route('alumnos_ubs.index')->with('success-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_alumnos_ubs');

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);

            // $alumno_clientes = AlumnoCliente::where('alumno_id', $alumno->id)->delete();
            $alumno_dato_laboral = AlumnoDatoLaboral::where('id', $alumno->dato_laboral_id)->delete();
            $alumno_familiar1 = AlumnoDatoLaboral::where('id', $alumno->familiar_uno)->delete();
            $alumno_familiar2 = AlumnoDatoLaboral::where('id', $alumno->familiar_dos)->delete();
            $alumno_legajos = AlumnoLegajo::where('alumno_id', $alumno->id)->delete();
            $alumno_nacionalidades = AlumnoNacionalidad::where('alumno_id', $alumno->id)->delete();

            $alumno->delete();

            DB::commit();

            return redirect()->route('alumnos_ubs.index')->with('success-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('alumnos_ubs.index')->with('error-message', 'El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function ver_legajo($id)
    {
        $this->authorize('ver_legajos_alumnos_ubs');

        try {
            $alumno = Alumno::findOrFail($id);
            $legajos = AlumnoLegajo::where('alumno_id', $alumno->id)->get();
            $legajos_encontrados = array(); //crear un array para luego comparar
            foreach ($legajos as $legajo) {
                array_push($legajos_encontrados, $legajo->tipo_legajo_id); //recorrer lo encontrado y agregar solo el id al nuevo array
            }
            $tipos_legajos = TipoLegajo::whereNotIn('id', $legajos_encontrados) //comparar todos del modelo con lo encontrado
                                ->where('tipo', 'AL')
                                ->where('estado', 'AC')
                                ->get();

            return view('ubs/alumnos/show_legajo')->with(compact('alumno', 'legajos', 'tipos_legajos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function subir_legajo(Request $request, $id)
    {
        $this->authorize('subir_legajos_alumnos_ubs');

        $request->validate([
            'tipo_legajo' => ['required', 'numeric'],
            'legajo' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);

            $legajo = new AlumnoLegajo();
            $legajo->alumno_id = $id;
            $legajo->tipo_legajo_id = $request->tipo_legajo;

            $tipo_legajo = TipoLegajo::findOrFail($request->tipo_legajo);
            //cargar archivo
            $archivo = $request->legajo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/legajos/alumnos/' . $alumno->id . '_' . Str::lower($alumno->primer_nombre) . '_' . Str::lower($alumno->primer_apellido);
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = $tipo_legajo->id . '_' . Str::lower($tipo_legajo->nombre);
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/legajos/alumnos/' . $alumno->id . '_' . Str::lower($alumno->primer_nombre) . '_' . Str::lower($alumno->primer_apellido);
                $nombre = $tipo_legajo->id . '_' . Str::lower($tipo_legajo->nombre);
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $legajo->url_ubicacion = $directorio . '/' . $nombre_archivo;
            $legajo->extension = $extension;
            $legajo->save();

            DB::commit();

            return response()->json([
                'message' => 'El legajo del alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_legajo($id)
    {
        $this->authorize('eliminar_legajos_alumnos_ubs');

        DB::beginTransaction();

        try {
            $legajo = AlumnoLegajo::findOrFail($id);
            $nombre_legajo = $legajo->tipoLegajo->nombre;
            $ubicacion_archivo = $legajo->url_ubicacion;

            $alumno = Alumno::findOrFail($legajo->alumno_id);

            Storage::delete($ubicacion_archivo);
            $legajo->delete();

            DB::commit();

            return redirect()->route('alumnos_ubs.ver_legajo', $alumno->id)->with('success-message','El legajo ' . $nombre_legajo . ' del alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }

	public function get_cedula($cedula)
    {
        $this->authorize('crear_alumnos_ubs');

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('numero_documento', $cedula)->first();
			$alumno_existe = 'NO';
			$message = '';
            if ($alumno) {
				$alumno_existe = 'SI';
				$message = 'La cédula de identidad ingresada ya se encuentra cargada en el sistema.';
			}

			return response()->json([
				'alumno_existe' => $alumno_existe,
				'message' => $message,
			]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos_ubs.index')->with('error-message', $e->getMessage());
        }
    }
}
