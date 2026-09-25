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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
use App\Models\MallaEspejo;
use App\Models\MallaEspejoDetalle;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\SemestreMalla;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\Materia;
use App\Models\Programa;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\AlumnoNota;
use App\Models\Cliente;
use App\Models\AlumnoCliente;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\RequerimientoExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;
use App\Models\Empresa;


class AlumnoController extends Controller
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
        $this->authorize('ver_alumnos');

        try {

			$buscar = Str::upper($request->buscar);
            $filtro_ingreso = $request->filtro_ingreso;
            $filtro_edad = $request->filtro_edad;
            $filtro_programa = $request->filtro_programa;
            $filtro_periodo = $request->filtro_periodo;
            $filtro_correo = $request->filtro_correo;
            $filtro_ubs = $request->filtro_ubs;

            $query = Alumno::with('matriculaciones');

            if (!(blank($filtro_ingreso))) {
                $query->whereHas('matriculaciones', function($query) use ($filtro_ingreso) {
                    $query->where('semestre_id', $filtro_ingreso)
                        ->whereIn('id', function($query) {
                            $query->selectRaw('min(id)')
                                    ->from('matriculaciones')
                                    ->groupBy('alumno_id');
                        });
                });
            }

            if (!(blank($filtro_edad))) {
                $hoy = Carbon::now();
                $edad_min = 0;
                $edad_max = 0;

                switch ($filtro_edad) {
                    case '15-20':
                        $edad_min = 15;
                        $edad_max = 20;
                        break;
                    case '21-25':
                        $edad_min = 21;
                        $edad_max = 25;
                        break;
                    case '26-30':
                        $edad_min = 26;
                        $edad_max = 30;
                        break;
                    case '31':
                        $edad_min = 31;
                        $edad_max = 1000;
                        break;
                    default:
                        break;
                }

                if ($edad_min && $edad_max) {
                    $fecha_min = $hoy->copy()->subYears($edad_max)->startOfYear();
                    $fecha_max = $hoy->copy()->subYears($edad_min)->endOfYear();

                    $query->whereBetween('fecha_nacimiento', [$fecha_min, $fecha_max]);
                }
            }

            if (!(blank($filtro_programa))) {
                $query->whereHas('matriculaciones', function($query) use ($filtro_programa) {
                    $query->where('programa_id', $filtro_programa)
                            ->where('estado', 'AC');
                });
            }

            if (!(blank($filtro_periodo))) {
                $query->whereHas('matriculaciones', function($query) use ($filtro_periodo) {
                    $query->where('semestre_id', $filtro_periodo)
                        ->where('estado', 'AC');
                });
            }

            if (!(blank($filtro_correo))) {
                if ($filtro_correo == 'SI') {
                    $query->where('email_institucional', '!=', null);
                } else if ($filtro_correo == 'NO') {
                    $query->where('email_institucional', null);
                }
            }

            if (!(blank($filtro_ubs))) {
                if ($filtro_ubs == 'UBS') {
                    $query->where('ubs', true);
                } else if ($filtro_ubs == 'GRADO') {
                    $query->where('ubs', false);
                }
            }

			if (!(blank($buscar))) {
                $query->orWhere('id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('primer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('segundo_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('tercer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('primer_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('segundo_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('numero_documento', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
			}

            $alumnos = $query->orderBy('id', 'desc')->paginate(50);

            foreach ($alumnos as $alumno) {
                $primera_matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'asc')->first();
                if ($primera_matriculacion) {
					$alumno->ingreso = $primera_matriculacion->semestre->nombre;
                } else {
                    $alumno->ingreso = $alumno->ingreso_texto;
                }

                $ultima_matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('estado', 'AC')->orderBy('id', 'desc')->first();
                if ($ultima_matriculacion) {
                    $alumno->periodo = $ultima_matriculacion->semestre->nombre;
                    $alumno->programa = $ultima_matriculacion->programa->nombre;
                } else {
                    // Sin matriculación: se muestran los datos académicos cargados en el propio alumno.
                    $alumno->periodo = $alumno->semestre_actual ? $alumno->semestre_actual . ".º semestre" : null;
                    $alumno->programa = optional($alumno->Carrera)->nombre_fantasia;
                }
            }

            $semestres = Semestre::orderBy('id', 'desc')->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->get();

            return view('alumnos/index')->with(compact('alumnos', 'semestres', 'programas', 'buscar', 'filtro_ingreso', 'filtro_edad', 'filtro_programa', 'filtro_periodo', 'filtro_correo', 'filtro_ubs'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_alumnos');

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
            //buscar todos los legajos del alumno
            $alumnos_legajos = AlumnoLegajo::where('alumno_id', $id)->get();
            $legajos_encontrados = []; //crear un array para luego comparar
            foreach ($alumnos_legajos as $alumno_legajo) {
                array_push($legajos_encontrados, $alumno_legajo->tipo_legajo_id); //recorrer lo encontrado y agregar solo el id al nuevo array
            }
            $tipos_legajos = TipoLegajo::whereNotIn('id', $legajos_encontrados) //comparar todos del modelo con lo encontrado
                                ->where('tipo', 'AL')
                                ->where('estado', 'AC')
                                ->get();
            return view('alumnos/show')->with(compact('alumno', 'nombre_alumno', 'nombre_familiar_uno', 'nombre_familiar_dos', 'tipos_legajos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_alumnos');

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
            $carreras = Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('alumnos/create')->with(compact('carreras', 'sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios', 'clientes'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_alumnos');

        $request->validate([
            'primer_nombre_alumno' => 'required',
            'segundo_nombre_alumno' => 'nullable',
            'tercer_nombre_alumno' => 'nullable',
            'primer_apellido_alumno' => 'required',
            'segundo_apellido_alumno' => 'nullable',
            'numero_documento' => ['required', Rule::unique('alumnos')],
            'sexo' => ['required', 'numeric'],
            'fecha_nacimiento' => ['required', 'date'],
            'telefono' => 'nullable',
            'celular' => 'required',
            'nacionalidad' => ['required', 'array'],
            'email_personal' => ['required', 'email'],
            'direccion' => 'required',
            'departamento' => ['required', 'numeric'],
            'ciudad' => ['required', 'numeric'],
            'barrio' => ['nullable', 'numeric'],
            'usuario' => ['nullable', 'numeric'],

            'carrera' => ['nullable', 'numeric'],
            'anho_ingreso' => ['nullable', 'integer', 'between:1990,2100'],
            'semestre_ingreso' => ['nullable', 'in:1,2'],

            'formacion' => ['nullable', 'numeric'],
            'institucion_educativa' => ['nullable', 'numeric'],

            'primer_nombre_familiar1' => 'nullable',
            'segundo_nombre_familiar1' => 'nullable',
            'tercer_nombre_familiar1' => 'nullable',
            'primer_apellido_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'segundo_apellido_familiar1' => 'nullable',
            'relacion_familiar1' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'email_familiar1' => ['nullable', 'email', 'required_with:primer_nombre_familiar1'],

            'primer_nombre_familiar2' => 'nullable',
            'segundo_nombre_familiar2' => 'nullable',
            'tercer_nombre_familiar2' => 'nullable',
            'primer_apellido_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'segundo_apellido_familiar2' => 'nullable',
            'relacion_familiar2' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'email_familiar2' => ['nullable', 'email', 'required_with:primer_nombre_familiar2'],

            'empresa' => 'nullable',
            'cargo' => ['nullable', 'required_with:empresa'],
            'email_laboral' => ['nullable', 'email', 'required_with:empresa'],
            'telefono_laboral' => ['nullable', 'required_with:empresa'],
            'celular_laboral' => 'nullable',

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
            $alumno->barrio_id = $request->barrio ?: null;

            $alumno->carrera_id = $request->carrera ?: null;
            $alumno->anho_ingreso = $request->anho_ingreso ?: null;
            $alumno->semestre_ingreso = $request->semestre_ingreso ?: null;

            $alumno->formacion_id = $request->formacion ?: null;
            $alumno->institucion_educativa_id = $request->institucion_educativa ?: null;

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

            if ($request->empresa != null) {
                $laboral = new AlumnoDatoLaboral();
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
                $laboral->telefono = $request->telefono_laboral;
                $laboral->email = $request->email_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->save();
                $alumno->dato_laboral_id = $laboral->id;
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

            $nulo = true;
            $clientes_request = $request->clientes ?? [];

            foreach ($clientes_request as $array) {
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

                foreach ($clientes_request as $array) {
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
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_alumnos');

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
            $carreras = Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('alumnos/edit')->with(compact('carreras', 'alumno', 'sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios', 'clientes'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_alumnos');

        $request->validate([
            'primer_nombre_alumno' => 'required',
            'segundo_nombre_alumno' => 'nullable',
            'tercer_nombre_alumno' => 'nullable',
            'primer_apellido_alumno' => 'required',
            'segundo_apellido_alumno' => 'nullable',
            'numero_documento' => ['required', Rule::unique('alumnos')->ignore($id)],
            'sexo' => ['required', 'numeric'],
            'fecha_nacimiento' => ['required', 'date'],
            'telefono' => 'nullable',
            'celular' => 'required',
            'nacionalidad' => ['required', 'array'],
            'email_personal' => 'required',
            'email_institucional' => ['nullable', 'email'],
            'direccion' => 'required',
            'departamento' => ['required', 'numeric'],
            'ciudad' => ['required', 'numeric'],
            'barrio' => ['nullable', 'numeric'],
            'usuario' => ['nullable', 'numeric'],
            'ubs' => 'required',

            'carrera' => ['nullable', 'numeric'],
            'anho_ingreso' => ['nullable', 'integer', 'between:1990,2100'],
            'semestre_ingreso' => ['nullable', 'in:1,2'],

            'formacion' => ['nullable', 'numeric'],
            'institucion_educativa' => ['nullable', 'numeric'],

            'primer_nombre_familiar1' => 'nullable',
            'segundo_nombre_familiar1' => 'nullable',
            'tercer_nombre_familiar1' => 'nullable',
            'primer_apellido_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'segundo_apellido_familiar1' => 'nullable',
            'relacion_familiar1' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:primer_nombre_familiar1'],
            'email_familiar1' => 'nullable',

            'primer_nombre_familiar2' => 'nullable',
            'segundo_nombre_familiar2' => 'nullable',
            'tercer_nombre_familiar2' => 'nullable',
            'primer_apellido_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'segundo_apellido_familiar2' => 'nullable',
            'relacion_familiar2' => ['nullable', 'numeric', 'required_with:primer_nombre_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:primer_nombre_familiar2'],
            'email_familiar2' => 'nullable',

            'empresa' => 'nullable',
            'cargo' => ['nullable', 'required_with:empresa'],
            'email_laboral' => 'nullable',
            'telefono_laboral' => ['nullable', 'required_with:empresa'],
            'celular_laboral' => 'nullable',
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
            $alumno->barrio_id = $request->barrio ?: null;
            if ($request->usuario) {
                $alumno->usuario_id = $request->usuario;
            }

            $alumno->carrera_id = $request->carrera ?: null;
            $alumno->anho_ingreso = $request->anho_ingreso ?: null;
            $alumno->semestre_ingreso = $request->semestre_ingreso ?: null;

            $alumno->formacion_id = $request->formacion ?: null;
            $alumno->institucion_educativa_id = $request->institucion_educativa ?: null;

            if ($request->primer_nombre_familiar1 != null) {
                $familiar1 = $alumno->familiar_uno_id ? AlumnoFamiliar::findOrFail($alumno->familiar_uno_id) : new AlumnoFamiliar();
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

            if ($request->empresa != null) {
                if ($alumno->dato_laboral_id != null) {
                    $laboral = AlumnoDatoLaboral::findOrFail($alumno->dato_laboral_id);
                } else {
                    $laboral = new AlumnoDatoLaboral();
                }
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
                $laboral->telefono = $request->telefono_laboral;
                $laboral->email = $request->email_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->save();
                $alumno->dato_laboral_id = $laboral->id;
            }

            if ($alumno->usuario_id) {
                $usuario = User::findOrFail($alumno->usuario_id);
                if ($alumno->email_institucional != null && $usuario->email != $alumno->email_institucional) {
                    $usuario->email = $alumno->email_institucional;
                    $usuario->save();
                }
            }

            $alumno->actualizado_por_id = Auth::id();
            $alumno->ubs = filter_var($request->ubs, FILTER_VALIDATE_BOOLEAN);
            $alumno->save();

			if ($cliente && $cliente->numero_documento != $alumno->numero_documento) {
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
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_alumnos');

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->actualizado_por_id = Auth::id();
            $alumno->estado = 'IN';
            $alumno->save();

            DB::commit();

            return redirect()->route('alumnos.index')->with('error-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_alumnos');

        DB::beginTransaction();

        try {
            $alumno = Alumno::findOrFail($id);
            $alumno->actualizado_por_id = Auth::id();
            $alumno->estado = 'AC';
            $alumno->save();

            DB::commit();

            return redirect()->route('alumnos.index')->with('success-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_alumnos');

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

            return redirect()->route('alumnos.index')->with('success-message','El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('alumnos.index')->with('error-message', 'El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function ver_legajo($id)
    {
        $this->authorize('ver_legajos_alumnos');

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
            return view('alumnos/show_legajo')->with(compact('alumno', 'legajos', 'tipos_legajos'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function subir_legajo(Request $request, $id)
    {
        $this->authorize('subir_legajos_alumnos');

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
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_legajo($id)
    {
        $this->authorize('eliminar_legajos_alumnos');

        DB::beginTransaction();

        try {
            $legajo = AlumnoLegajo::findOrFail($id);
            $nombre_legajo = $legajo->tipoLegajo->nombre;
            $ubicacion_archivo = $legajo->url_ubicacion;

            $alumno = Alumno::findOrFail($legajo->alumno_id);

            Storage::delete($ubicacion_archivo);
            $legajo->delete();

            DB::commit();

            return redirect()->route('alumnos.ver_legajo', $alumno->id)->with('success-message','El legajo ' . $nombre_legajo . ' del alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_notas($id)
    {
        $this->authorize('ver_notas_alumnos');

        try {
            $alumno = Alumno::with('alumnoNotas')->findOrFail($id);
            foreach ($alumno->alumnoNotas as $nota) {
                $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) {
                    $query->where('materia_id', $nota->materia_id);
                }])->where('carrera_id', $nota->carrera_id)
                    ->first();

                    $malla_detalle = $detalle->mallaDetalles->first();

                    if ($malla_detalle) {
                        $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre;
                    } else {
                        $nota->semestre_materia = null;
                    }
            }
            $alumno->alumnoNotas = $alumno->alumnoNotas->sortBy('semestre_materia');

            return view('alumnos/show_notas')->with(compact('alumno'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_notas_espejo($id)
    {
        $this->authorize('ver_notas_alumnos');

        try {
            $alumno = Alumno::with('alumnoNotas')->findOrFail($id);
            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            if (!$matriculacion->carrera_siu_id) {
                return back()->with('error-message', 'El alumno no se encuentra inscripto en SIU.');
            }

            $alumno->carrera_paraguay = $matriculacion->carrera->nombre_fantasia;
            $alumno->carrera_siu = $matriculacion->carreraSiu->nombre_fantasia;

            $malla_paraguay = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre', 'asc')
                    ->with(['materia' => function ($q) {
                        $q->orderBy('nombre_fantasia', 'asc');
                    }]);
            }])->where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();
            $malla_siu = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre', 'asc')
                    ->with(['materia' => function ($q) {
                        $q->orderBy('nombre_fantasia', 'asc');
                    }]);
            }])->where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();

            $cantidad_materias_paraguay = $malla_paraguay->mallaDetalles->count();
            $cantidad_materias_siu = $malla_siu->mallaDetalles->count();

            $espejos_siu_ids = [];
            foreach ($malla_paraguay->mallaDetalles as $malla_paraguay_detalle) {
                $espejo = MallaEspejoDetalle::whereHas('mallaEspejo', function ($query) use ($malla_paraguay, $malla_siu) {
                    $query->where('malla_paraguay_id', $malla_paraguay->id)
                        ->where('malla_siu_id', $malla_siu->id)
                        ->where('estado', 'AC');
                })->where('materia_paraguay_id', $malla_paraguay_detalle->materia_id)->first();

                if ($espejo) {
                    $malla_paraguay_detalle->semestre_materia_siu = $espejo->mallaEspejo->mallaSiu->mallaDetalles()->where('materia_id', $espejo->materia_siu_id)->first()->semestre;
                    $malla_paraguay_detalle->materia_siu_id = $espejo->materia_siu_id;
                    $malla_paraguay_detalle->materia_siu = $espejo->materiaSiu->nombre_fantasia;
                    $malla_paraguay_detalle->espejo = true;
                    $espejos_siu_ids[] = $espejo->materia_siu_id;
                }
            }

            $equivalencias = collect();
            $paraguay_sin_espejo = collect();
            $siu_sin_espejo = collect();

            foreach ($malla_paraguay->mallaDetalles as $malla_detalle) {
                $nota_paraguay = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_id)->first();
                $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_siu_id)->first();

                $periodo = '---';
                if ($nota_paraguay) {
                    $periodo = $nota_paraguay->semestre->nombre;
                }
                if ($nota_siu) {
                    $periodo = $nota_siu->semestre->nombre;
                }

                $semestre_orden = $malla_detalle->semestre ? $malla_detalle->semestre : ($malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : 999);

                $datos = [
                    'semestre_materia_paraguay' => $malla_detalle->semestre ? $malla_detalle->semestre : '---',
                    'materia_paraguay' => $malla_detalle->materia->nombre_real ? $malla_detalle->materia->nombre_real : '---',
                    'nota_paraguay' => $nota_paraguay ? $nota_paraguay->calificacion : '---',
                    'semestre_materia_siu' => $malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : '---',
                    'materia_siu' => $malla_detalle->materia_siu ? $malla_detalle->materia_siu : '---',
                    'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                    'periodo' => $periodo,
                    'estado' => $malla_detalle->espejo,
                    'semestre_orden' => $semestre_orden
                ];

                if ($malla_detalle->espejo) {
                    $equivalencias->push($datos);
                } else {
                    $paraguay_sin_espejo->push($datos);
                }
            }

            foreach ($malla_siu->mallaDetalles as $malla_siu_detalle) {
                if (!in_array($malla_siu_detalle->materia_id, $espejos_siu_ids)) {
                    $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_siu_detalle->materia_id)->first();
                    $periodo = $nota_siu ? $nota_siu->semestre->nombre : '---';
                    $semestre_orden = $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : 999;

                    $datos = [
                        'semestre_materia_paraguay' => '---',
                        'materia_paraguay' => '---',
                        'nota_paraguay' => '---',
                        'semestre_materia_siu' => $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : '---',
                        'materia_siu' => $malla_siu_detalle->materia->nombre_fantasia ? $malla_siu_detalle->materia->nombre_fantasia : '---',
                        'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                        'periodo' => $periodo,
                        'estado' => false,
                        'semestre_orden' => $semestre_orden
                    ];

                    $siu_sin_espejo->push($datos);
                }
            }

            // Combinamos equivalencias primero
            $espejos = $equivalencias;

            // Emparejamos Paraguay sin espejo con SIU sin espejo
            $max_sin_espejo = max($paraguay_sin_espejo->count(), $siu_sin_espejo->count());
            for ($i = 0; $i < $max_sin_espejo; $i++) {
                $p = $paraguay_sin_espejo->get($i, [
                    'semestre_materia_paraguay' => '---',
                    'materia_paraguay' => '---',
                    'nota_paraguay' => '---',
                    'semestre_orden' => 999
                ]);
                $s = $siu_sin_espejo->get($i, [
                    'semestre_materia_siu' => '---',
                    'materia_siu' => '---',
                    'nota_siu' => '---',
                    'periodo' => '---',
                    'semestre_orden' => 999
                ]);

                $periodo_combinado = $p['periodo'] ?? '---';
                if ($s['periodo'] !== '---') {
                    $periodo_combinado = $s['periodo'];
                }

                $datos = [
                    'semestre_materia_paraguay' => $p['semestre_materia_paraguay'],
                    'materia_paraguay' => $p['materia_paraguay'],
                    'nota_paraguay' => $p['nota_paraguay'],
                    'semestre_materia_siu' => $s['semestre_materia_siu'],
                    'materia_siu' => $s['materia_siu'],
                    'nota_siu' => $s['nota_siu'],
                    'periodo' => $periodo_combinado,
                    'estado' => false,
                    'semestre_orden' => min($p['semestre_orden'], $s['semestre_orden'])
                ];

                $espejos->push($datos);
            }

            //Ordenamos por semestre
            $espejos = $espejos->sortBy('semestre_orden')->values();


            return view('alumnos/show_notas_espejo')->with(compact('alumno', 'espejos', 'cantidad_materias_paraguay', 'cantidad_materias_siu'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_asistencias($id)
    {
        $this->authorize('ver_asistencias_alumnos');

        try {
            $alumno = Alumno::findOrFail($id);
            $asistencias = AlumnoAsistencia::where('alumno_id', $alumno->id)->distinct('materia_id', 'semestre_id')->orderBy('semestre_id', 'desc')->get();
            foreach ($asistencias as $asistencia) {
                $asistencia->horas_desarrollo = ClaseMateria::where('materia_id', $asistencia->materia_id)->where('semestre_id', $asistencia->semestre_id)->sum('horas_desarrollo');
                $asistencia->total_clases = ClaseMateria::where('materia_id', $asistencia->materia_id)->where('semestre_id', $asistencia->semestre_id)->count();
                $asistencias_all = AlumnoAsistencia::where('alumno_id', $alumno->id)->get();
                foreach ($asistencias_all as $a) {
                    if ($asistencia->materia_id == $a->materia_id && $asistencia->semestre_id == $a->semestre_id && ($a->estado == 'PR' || $a->estado == 'AJ')) {
                        $asistencia->horas_asistidas = $asistencia->horas_asistidas + $a->horas_desarrollo;
                        $asistencia->total_asistido = $asistencia->total_asistido + 1;
                        $asistencia->porcentaje = number_format(($asistencia->horas_asistidas / $asistencia->horas_desarrollo) * 100, 0, ',', '.');
                    }
                }
            }

            $materias = AlumnoAsistencia::select('materias.nombre_fantasia')
                            ->join('materias', 'alumnos_asistencias.materia_id', '=', 'materias.id')
                            ->where('alumno_id', $alumno->id)
                            ->distinct('materia_id')
                            ->get();

            $semestres = Semestre::orderBy('id', 'desc')->get();

            return view('alumnos/show_asistencias')->with(compact('alumno', 'asistencias', 'materias', 'semestres'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_extensiones($id)
    {
        $this->authorize('ver_extensiones_alumnos');

        try {
            $alumno = Alumno::findOrFail($id);
            $extensiones = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->get();
            $horas_requeridas = RequerimientoExtensionUniversitaria::first()->horas_requeridas;
            $actividades_requeridas = RequerimientoExtensionUniversitaria::first()->actividades_requeridas;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            $cantidad_realizada_1 = 0;
            $cantidad_realizada_2 = 0;
            $cantidad_realizada_3 = 0;
            $cantidad_realizada_4 = 0;
            $horas_realizadas_1 = 0;
            $horas_realizadas_2 = 0;
            $horas_realizadas_3 = 0;
            $horas_realizadas_4 = 0;
            $horas_acreditadas = 0;
            $horas_acreditadas_1 = 0;
            $horas_acreditadas_2 = 0;
            $horas_acreditadas_3 = 0;
            $horas_acreditadas_4 = 0;

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $anho = $fecha->year;
                if ($fecha->month <= 7) {
                    $extension->periodo = $anho . '-1';
                } else {
                    $extension->periodo = $anho . '-2';
                }

                if ($extension->extensionUniversitaria->estado == 'FI') {
                    $tipo_extension_id = $extension->extensionUniversitaria->tipo_extension_id;
                    $maxima_cantidad_horas = $extension->extensionUniversitaria->tipoExtension->maxima_cantidad_horas;

                    // Dependiendo del tipo de extensión, incrementar las horas y cantidades
                    switch ($tipo_extension_id) {
                        case 1:
                            $cantidad_realizada_1++;
                            $horas_realizadas_1 += $extension->cantidad_horas;
                            if ($horas_realizadas_1 > $maxima_cantidad_horas) {
                                $horas_acreditadas_1 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_1 = $horas_realizadas_1;
                            }
                            break;

                        case 2:
                            $cantidad_realizada_2++;
                            $horas_realizadas_2 += $extension->cantidad_horas;
                            if ($horas_realizadas_2 > $maxima_cantidad_horas) {
                                $horas_acreditadas_2 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_2 = $horas_realizadas_2;
                            }
                            break;

                        case 3:
                            $cantidad_realizada_3++;
                            $horas_realizadas_3 += $extension->cantidad_horas;
                            if ($horas_realizadas_3 > $maxima_cantidad_horas) {
                                $horas_acreditadas_3 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_3 = $horas_realizadas_3;
                            }
                            break;

                        case 4:
                            $cantidad_realizada_4++;
                            $horas_realizadas_4 += $extension->cantidad_horas;
                            if ($horas_realizadas_4 > $maxima_cantidad_horas) {
                                $horas_acreditadas_4 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_4 = $horas_realizadas_4;
                            }
                            break;
                    }
                }
            }

            $horas_acreditadas = $horas_acreditadas_1 + $horas_acreditadas_2 + $horas_acreditadas_3 + $horas_acreditadas_4;
            $actividades_realizadas = $cantidad_realizada_1 + $cantidad_realizada_2 + $cantidad_realizada_3 + $cantidad_realizada_4;

            return view('alumnos/show_extensiones')->with(compact('alumno', 'extensiones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'horas_realizadas_1', 'horas_realizadas_2', 'horas_realizadas_3', 'horas_realizadas_4', 'horas_acreditadas_1', 'horas_acreditadas_2', 'horas_acreditadas_3', 'horas_acreditadas_4', 'cantidad_realizada_1', 'cantidad_realizada_2', 'cantidad_realizada_3', 'cantidad_realizada_4', 'actividades_realizadas'));

        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function reporte_extensiones($id)
    {
        // $this->authorize('ver_extensiones_alumnos');

        try {
            $alumno = Alumno::findOrFail($id);
            $extensiones = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->get();
            $horas_requeridas = RequerimientoExtensionUniversitaria::first()->horas_requeridas;
            $actividades_requeridas = RequerimientoExtensionUniversitaria::first()->actividades_requeridas;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer

            $cantidad_realizada_1 = 0;
            $cantidad_realizada_2 = 0;
            $cantidad_realizada_3 = 0;
            $cantidad_realizada_4 = 0;
            $horas_realizadas_1 = 0;
            $horas_realizadas_2 = 0;
            $horas_realizadas_3 = 0;
            $horas_realizadas_4 = 0;
            $horas_acreditadas = 0;
            $horas_acreditadas_1 = 0;
            $horas_acreditadas_2 = 0;
            $horas_acreditadas_3 = 0;
            $horas_acreditadas_4 = 0;

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $anho = $fecha->year;
                if ($fecha->month <= 7) {
                    $extension->periodo = $anho . '-1';
                } else {
                    $extension->periodo = $anho . '-2';
                }

                if ($extension->extensionUniversitaria->estado == 'FI') {
                    $tipo_extension_id = $extension->extensionUniversitaria->tipo_extension_id;
                    $maxima_cantidad_horas = $extension->extensionUniversitaria->tipoExtension->maxima_cantidad_horas;

                    // Dependiendo del tipo de extensión, incrementar las horas y cantidades
                    switch ($tipo_extension_id) {
                        case 1:
                            $cantidad_realizada_1++;
                            $horas_realizadas_1 += $extension->cantidad_horas;
                            if ($horas_realizadas_1 > $maxima_cantidad_horas) {
                                $horas_acreditadas_1 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_1 = $horas_realizadas_1;
                            }
                            break;

                        case 2:
                            $cantidad_realizada_2++;
                            $horas_realizadas_2 += $extension->cantidad_horas;
                            if ($horas_realizadas_2 > $maxima_cantidad_horas) {
                                $horas_acreditadas_2 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_2 = $horas_realizadas_2;
                            }
                            break;

                        case 3:
                            $cantidad_realizada_3++;
                            $horas_realizadas_3 += $extension->cantidad_horas;
                            if ($horas_realizadas_3 > $maxima_cantidad_horas) {
                                $horas_acreditadas_3 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_3 = $horas_realizadas_3;
                            }
                            break;

                        case 4:
                            $cantidad_realizada_4++;
                            $horas_realizadas_4 += $extension->cantidad_horas;
                            if ($horas_realizadas_4 > $maxima_cantidad_horas) {
                                $horas_acreditadas_4 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_4 = $horas_realizadas_4;
                            }
                            break;
                    }
                }
            }

            $horas_acreditadas = $horas_acreditadas_1 + $horas_acreditadas_2 + $horas_acreditadas_3 + $horas_acreditadas_4;
            $actividades_realizadas = $cantidad_realizada_1 + $cantidad_realizada_2 + $cantidad_realizada_3 + $cantidad_realizada_4;

            $pdf = Pdf::loadView('alumnos/pdf_extensiones', compact('empresa', 'fecha_hoy', 'alumno', 'extensiones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'horas_realizadas_1', 'horas_realizadas_2', 'horas_realizadas_3', 'horas_realizadas_4', 'horas_acreditadas_1', 'horas_acreditadas_2', 'horas_acreditadas_3', 'horas_acreditadas_4', 'cantidad_realizada_1', 'cantidad_realizada_2', 'cantidad_realizada_3', 'cantidad_realizada_4', 'actividades_realizadas'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('rpt_extensiones_universitaria_' . $alumno->numero_documento . '_' . Carbon::now()->format('dmY_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }
}
