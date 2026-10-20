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
use App\Models\Carrera;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\RequerimientoExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;
use App\Support\ResumenExtensionAlumno;

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
            $filtro_programa = $request->filtro_programa; // id de la carrera
            $filtro_correo = $request->filtro_correo;

            $query = Alumno::with('Carrera');

            if (!(blank($filtro_ingreso))) {
                $query->where('anho_ingreso', $filtro_ingreso);
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
                $query->where('carrera_id', $filtro_programa);
            }


            if (!(blank($filtro_correo))) {
                if ($filtro_correo == 'SI') {
                    $query->where('email_institucional', '!=', null);
                } else if ($filtro_correo == 'NO') {
                    $query->where('email_institucional', null);
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
                $alumno->ingreso = $alumno->ingreso_texto ? $alumno->ingreso_texto . ($alumno->anho_ingreso_estimado ? ' (est.)' : '') : null;
                $alumno->periodo = $alumno->semestre_actual ? $alumno->semestre_actual . ".º semestre" : null;
                $alumno->programa = optional($alumno->Carrera)->nombre_fantasia;
            }

            // Horas de extensión acumuladas y cumplimiento del requisito, solo de la página actual.
            $resumenes_extension = ResumenExtensionAlumno::para($alumnos->getCollection());

            // Filtros: años de ingreso cargados y carreras activas (se pasan como {id, nombre}).
            $semestres = Alumno::whereNotNull('anho_ingreso')->distinct()->orderByDesc('anho_ingreso')->pluck('anho_ingreso')
                ->map(fn ($anho) => (object) ['id' => $anho, 'nombre' => $anho]);
            $programas = Carrera::where('estado', 'AC')->orderBy('nombre_fantasia')->get()
                ->map(fn ($carrera) => (object) ['id' => $carrera->id, 'nombre' => $carrera->nombre_fantasia]);

            return view('alumnos/index')->with(compact('alumnos', 'resumenes_extension', 'semestres', 'programas', 'buscar', 'filtro_ingreso', 'filtro_edad', 'filtro_programa', 'filtro_correo'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_alumnos');

        try {
            $alumno = Alumno::findOrFail($id);
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
            $carreras = Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('alumnos/create')->with(compact('carreras', 'sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_alumnos');

        $request->validate([
            'nombres_alumno' => 'required',
            'apellidos_alumno' => 'required',
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

            'nombres_familiar1' => 'nullable',
            'apellidos_familiar1' => ['nullable', 'required_with:nombres_familiar1'],
            'relacion_familiar1' => ['nullable', 'numeric', 'required_with:nombres_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:nombres_familiar1'],
            'email_familiar1' => ['nullable', 'email', 'required_with:nombres_familiar1'],

            'nombres_familiar2' => 'nullable',
            'apellidos_familiar2' => ['nullable', 'required_with:nombres_familiar2'],
            'relacion_familiar2' => ['nullable', 'numeric', 'required_with:nombres_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:nombres_familiar2'],
            'email_familiar2' => ['nullable', 'email', 'required_with:nombres_familiar2'],

            'empresa' => 'nullable',
            'cargo' => ['nullable', 'required_with:empresa'],
            'email_laboral' => ['nullable', 'email', 'required_with:empresa'],
            'telefono_laboral' => ['nullable', 'required_with:empresa'],
            'celular_laboral' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $alumno = new Alumno();
            $alumno->primer_nombre = removeAccents(Str::upper(trim($request->nombres_alumno)));
            $alumno->segundo_nombre = null;
            $alumno->tercer_nombre = null;
            $alumno->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_alumno)));
            $alumno->segundo_apellido = null;
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
            $alumno->anho_ingreso_estimado = $request->boolean('anho_ingreso_estimado') && $alumno->anho_ingreso;

            $alumno->formacion_id = $request->formacion ?: null;
            $alumno->institucion_educativa_id = $request->institucion_educativa ?: null;

            if ($request->nombres_familiar1 != null) {
                $familiar1 = new AlumnoFamiliar();
                $familiar1->primer_nombre = removeAccents(Str::upper(trim($request->nombres_familiar1)));
                $familiar1->segundo_nombre = null;
                $familiar1->tercer_nombre = null;
                $familiar1->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_familiar1)));
                $familiar1->segundo_apellido = null;
                $familiar1->relacion_id = $request->relacion_familiar1;
                $familiar1->email = removeAccents(Str::lower($request->email_familiar1));
                $familiar1->celular = $request->celular_familiar1;
                $familiar1->save();
                $alumno->familiar_uno_id = $familiar1->id;
            }

            if ($request->nombres_familiar2 != null) {
                $familiar2 = new AlumnoFamiliar();
                $familiar2->primer_nombre = removeAccents(Str::upper(trim($request->nombres_familiar2)));
                $familiar2->segundo_nombre = null;
                $familiar2->tercer_nombre = null;
                $familiar2->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_familiar2)));
                $familiar2->segundo_apellido = null;
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
                $usuario->name = removeAccents(Str::upper(trim($request->nombres_alumno))) . ' ' . removeAccents(Str::upper(trim($request->apellidos_alumno)));
                $usuario->email = removeAccents(Str::lower($request->email_personal));
                $usuario->role_id = 2; //asignar el id de rol alumno
                $usuario->assignRole(2); //asignar el id de rol alumno
                $password = $request->numero_documento . '-' . Str::substr(removeAccents(Str::upper(trim($request->nombres_alumno))), 0, 1) . Str::substr(removeAccents(Str::lower(trim($request->apellidos_alumno))), 0, 1);
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
            $alumno = Alumno::findOrFail($id);
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
            $carreras = Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('alumnos/edit')->with(compact('carreras', 'alumno', 'sexos', 'nacionalidades', 'alumnos_formaciones', 'instituciones_educativas', 'departamentos_paraguay', 'ciudades', 'barrios', 'relaciones_familiares', 'usuarios'));
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_alumnos');

        $request->validate([
            'nombres_alumno' => 'required',
            'apellidos_alumno' => 'required',
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

            'carrera' => ['nullable', 'numeric'],
            'anho_ingreso' => ['nullable', 'integer', 'between:1990,2100'],
            'semestre_ingreso' => ['nullable', 'in:1,2'],

            'formacion' => ['nullable', 'numeric'],
            'institucion_educativa' => ['nullable', 'numeric'],

            'nombres_familiar1' => 'nullable',
            'apellidos_familiar1' => ['nullable', 'required_with:nombres_familiar1'],
            'relacion_familiar1' => ['nullable', 'numeric', 'required_with:nombres_familiar1'],
            'celular_familiar1' => ['nullable', 'required_with:nombres_familiar1'],
            'email_familiar1' => 'nullable',

            'nombres_familiar2' => 'nullable',
            'apellidos_familiar2' => ['nullable', 'required_with:nombres_familiar2'],
            'relacion_familiar2' => ['nullable', 'numeric', 'required_with:nombres_familiar2'],
            'celular_familiar2' => ['nullable', 'required_with:nombres_familiar2'],
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

            $alumno->primer_nombre = removeAccents(Str::upper(trim($request->nombres_alumno)));
            $alumno->segundo_nombre = null;
            $alumno->tercer_nombre = null;
            $alumno->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_alumno)));
            $alumno->segundo_apellido = null;
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
            $alumno->anho_ingreso_estimado = $request->boolean('anho_ingreso_estimado') && $alumno->anho_ingreso;

            $alumno->formacion_id = $request->formacion ?: null;
            $alumno->institucion_educativa_id = $request->institucion_educativa ?: null;

            if ($request->nombres_familiar1 != null) {
                $familiar1 = $alumno->familiar_uno_id ? AlumnoFamiliar::findOrFail($alumno->familiar_uno_id) : new AlumnoFamiliar();
                $familiar1->primer_nombre = removeAccents(Str::upper(trim($request->nombres_familiar1)));
                $familiar1->segundo_nombre = null;
                $familiar1->tercer_nombre = null;
                $familiar1->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_familiar1)));
                $familiar1->segundo_apellido = null;
                $familiar1->relacion_id = $request->relacion_familiar1;
                $familiar1->email = removeAccents(Str::lower($request->email_familiar1));
                $familiar1->celular = $request->celular_familiar1;
                $familiar1->save();
                $alumno->familiar_uno_id = $familiar1->id;
            }

            if ($request->nombres_familiar2 != null) {
                if ($alumno->familiar_dos_id != null) {
                    $familiar2 = AlumnoFamiliar::findOrFail($alumno->familiar_dos_id);
                } else {
                    $familiar2 = new AlumnoFamiliar();
                }
                $familiar2->primer_nombre = removeAccents(Str::upper(trim($request->nombres_familiar2)));
                $familiar2->segundo_nombre = null;
                $familiar2->tercer_nombre = null;
                $familiar2->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_familiar2)));
                $familiar2->segundo_apellido = null;
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
            $alumno->save();


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

    public function show_extensiones($id)
    {
        $this->authorize('ver_extensiones_alumnos');

        try {
            $alumno = Alumno::findOrFail($id);
            $extensiones = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->get();
            $horas_requeridas = RequerimientoExtensionUniversitaria::first()->horas_requeridas;
            $actividades_requeridas = RequerimientoExtensionUniversitaria::first()->actividades_requeridas;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $extension->periodo = $fecha->year . '-' . ($fecha->month <= 7 ? 1 : 2);
            }

            // Avance por tipo de actividad (cualquier cantidad de tipos).
            $resumen = ResumenExtensionAlumno::para(collect([$alumno]))[$alumno->id];
            $horas_acreditadas = $resumen['horas_acreditadas'];
            $actividades_realizadas = $resumen['actividades_realizadas'];

            return view('alumnos/show_extensiones')->with(compact('alumno', 'extensiones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'resumen', 'actividades_realizadas'));

        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }

    public function reporte_extensiones($id)
    {
        // Quien administra puede ver el informe de cualquier alumno; un alumno, solo el suyo.
        // Se valida antes del try para que el 403 no lo capture el catch genérico.
        if (!Auth::user()->can('ver_extensiones_alumnos') && Alumno::where('id', $id)->value('usuario_id') != Auth::id()) {
            abort(403);
        }

        try {
            $alumno = Alumno::findOrFail($id);
            $extensiones = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->get();
            $horas_requeridas = RequerimientoExtensionUniversitaria::first()->horas_requeridas;
            $actividades_requeridas = RequerimientoExtensionUniversitaria::first()->actividades_requeridas;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $extension->periodo = $fecha->year . '-' . ($fecha->month <= 7 ? 1 : 2);
            }

            // Avance por tipo de actividad (cualquier cantidad de tipos).
            $resumen = ResumenExtensionAlumno::para(collect([$alumno]))[$alumno->id];
            $horas_acreditadas = $resumen['horas_acreditadas'];
            $actividades_realizadas = $resumen['actividades_realizadas'];

            $pdf = Pdf::loadView('alumnos/pdf_extensiones', compact('fecha_hoy', 'alumno', 'extensiones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'resumen', 'actividades_realizadas'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('rpt_extensiones_universitaria_' . $alumno->numero_documento . '_' . Carbon::now()->format('dmY_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
        }
    }
}
