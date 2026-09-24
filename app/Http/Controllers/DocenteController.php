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
use Illuminate\Support\Arr;
use Luecano\NumeroALetras\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Docente;
use App\Models\Sexo;
use App\Models\InstitucionEducativa;
use App\Models\Pais;
use App\Models\Nacionalidad;
use App\Models\DepartamentoParaguay;
use App\Models\Ciudad;
use App\Models\Barrio;
use App\Models\User;
use App\Models\Configuracion;
use App\Models\DocenteNacionalidad;
use App\Models\DocenteNivelAcademico;
use App\Models\TipoLegajo;
use App\Models\DocenteLegajo;
use App\Models\AreaConocimiento;
use App\Models\Empresa;
use App\Models\Semestre;
use App\Models\SemestreMallaMateria;
use App\Models\ClaseMateria;
use App\Models\DocenteSalario;

class DocenteController extends Controller
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
        $this->authorize('ver_docentes');

        try {
            $buscar = Str::upper($request->buscar);
            $filtro_edad = $request->filtro_edad;
            $filtro_area = $request->filtro_area;
            $filtro_didactica = $request->filtro_didactica;

            $query = Docente::query();

            if (!(blank($filtro_edad))) {
                $hoy = Carbon::now();
                $edad_min = 0;
                $edad_max = 0;

                switch ($filtro_edad) {
                    case '25-30':
                        $edad_min = 25;
                        $edad_max = 30;
                        break;
                    case '31-35':
                        $edad_min = 31;
                        $edad_max = 35;
                        break;
                    case '36-40':
                        $edad_min = 36;
                        $edad_max = 40;
                        break;
                    case '41':
                        $edad_min = 41;
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

            if (!(blank($filtro_area))) {
                $query->where('area_conocimiento_id', $filtro_area);
            }

            if (!(blank($filtro_didactica))) {
                if ($filtro_didactica == 'SI') {
                    $query->where('capacitacion_didactica', true);
                } elseif ($filtro_didactica == 'NO') {
                    $query->where('capacitacion_didactica', false);
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

            $docentes = $query->orderBy('id', 'desc')->paginate(50);

            $areas_conocimientos = AreaConocimiento::get();

            return view('docentes/index')->with(compact('docentes', 'areas_conocimientos', 'buscar', 'filtro_edad', 'filtro_area', 'filtro_didactica'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_docentes');

        try {
            $docente = Docente::findOrFail($id);
            $docente->load('nacionalidades');
            $nombre_docente = $docente->primer_nombre;
            if ($docente->segundo_nombre) {
                $nombre_docente = $nombre_docente . ' ' . $docente->segundo_nombre;
            }
            if ($docente->tercer_nombre) {
                $nombre_docente = $nombre_docente . ' ' . $docente->tercer_nombre;
            }
            $nombre_docente = $nombre_docente . ' ' . $docente->primer_apellido;
            if ($docente->segundo_apellido) {
                $nombre_docente = $nombre_docente . ' ' . $docente->segundo_apellido;
            }

            $docente->load('Carreras.Facultad');

            // Períodos/carreras en los que el docente tiene materias asignadas
            // (la única relación real docente -> semestre -> carrera -> facultad).
            try {
                $asignaciones = SemestreMallaMateria::with(['SemestreMalla.Semestre', 'SemestreMalla.Malla', 'Materia'])
                    ->where('docente_id', $docente->id)
                    ->where('estado', 'AC')
                    ->get()
                    ->map(function ($smm) {
                        $carrera = \App\Models\Carrera::with('Facultad')->find(optional(optional($smm->SemestreMalla)->Malla)->carrera_id);
                        return (object) [
                            'periodo' => optional(optional($smm->SemestreMalla)->Semestre)->nombre,
                            'carrera' => optional($carrera)->nombre_fantasia,
                            'facultad' => optional(optional($carrera)->Facultad)->nombre,
                            'materia' => optional($smm->Materia)->nombre,
                        ];
                    });
            } catch (\Exception $e) {
                $asignaciones = collect();
            }

            return view('docentes/show')->with(compact('docente', 'nombre_docente', 'asignaciones'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_docentes');

        try {
            $sexos = Sexo::where('estado', 'AC')->get();
            $nacionalidades = Nacionalidad::get();
            $departamentos_paraguay = DepartamentoParaguay::get();
            $ciudades = Ciudad::get();
            $barrios = Barrio::get();
            $niveles_academicos = DocenteNivelAcademico::where('estado', 'AC')->get();
            $usuarios = User::where('state', 'AC')->get();
            $carreras = \App\Models\Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            $areas_conocimientos = AreaConocimiento::get();
            return view('docentes/create')->with(compact('carreras', 'sexos', 'nacionalidades', 'departamentos_paraguay', 'ciudades', 'barrios', 'niveles_academicos', 'usuarios', 'areas_conocimientos'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_docentes');

        $request->validate([
            'primer_nombre_docente' => 'required',
            'segundo_nombre_docente' => 'nullable',
            'tercer_nombre_docente' => 'nullable',
            'primer_apellido_docente' => 'required',
            'segundo_apellido_docente' => 'nullable',
            'numero_documento' => ['required', Rule::unique('docentes')],
            'sexo' => ['required', 'numeric'],
            'fecha_nacimiento' => ['required', 'date'],
            'telefono' => 'nullable',
            'celular' => 'required',
            'nacionalidad' => ['required', 'array'],
            'email_personal' => ['required', 'email'],
            'direccion' => 'required',
            'departamento' => ['required', 'numeric'],
            'ciudad' => ['required', 'numeric'],
            'barrio' => ['required', 'numeric'],
            'usuario' => ['nullable', 'numeric'],
            'tutor_tesis' => 'required',
            'nivel_academico' => ['required', 'numeric'],
            'capacitacion_didactica' => 'required',
            'area_conocimiento' => ['required', 'numeric'],
            'carreras' => ['nullable', 'array'],
            'carreras.*' => ['numeric'],
        ]);

        DB::beginTransaction();

        try {
            $docente = new Docente();
            $docente->primer_nombre = removeAccents(Str::upper($request->primer_nombre_docente));
            $docente->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_docente));
            $docente->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_docente));
            $docente->primer_apellido = removeAccents(Str::upper($request->primer_apellido_docente));
            $docente->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_docente));
            $docente->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $docente->sexo_id = $request->sexo;
            $docente->fecha_nacimiento = $request->fecha_nacimiento;
            $docente->telefono = $request->telefono;
            $docente->celular = $request->celular;
            $docente->email_personal = removeAccents(Str::lower($request->email_personal));
            $docente->direccion = removeAccents(Str::upper($request->direccion));
            $docente->departamento_id = $request->departamento;
            $docente->ciudad_id = $request->ciudad;
            $docente->barrio_id = $request->barrio;
            $docente->nivel_academico_id = $request->nivel_academico;
            $docente->capacitacion_didactica = $request->capacitacion_didactica;
            $docente->area_conocimiento_id = $request->area_conocimiento;

            if ($request->usuario != null) {
                $docente->usuario_id = $request->usuario;
                $docente->email_institucional = User::findOrFail($request->usuario)->email;
            } else {
                $usuario = new User();
                $usuario->name = removeAccents(Str::upper($request->primer_nombre_docente)) . ' ' . removeAccents(Str::upper($request->primer_apellido_docente));
                $usuario->email = removeAccents(Str::lower($request->email_personal));
                $usuario->role_id = 3; //asignar el id de rol docente
                $usuario->assignRole(3); //asignar el id de rol docente
                $password = $request->numero_documento . '-' . Str::substr(removeAccents(Str::upper($request->primer_nombre_docente)), 0, 1) . Str::substr(removeAccents(Str::lower($request->primer_apellido_docente)), 0, 1);
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

                $docente->usuario_id = $usuario->id;
            }

            $docente->tutor_tesis = $request->tutor_tesis;
            $docente->cargado_por_id = Auth::id();
            $docente->save();

            $docente->nacionalidades()->sync($request->nacionalidad);
            $docente->Carreras()->sync($request->carreras ?? []);


            //crear directorio para guardar su legajo
            $directorio = 'public/legajos/docentes/';
            $nombre_directorio_docente = $docente->id . '_' . Str::lower($docente->primer_nombre) . '_' . Str::lower($docente->primer_apellido);
            Storage::makeDirectory($directorio);

            DB::commit();

            return redirect()->route('docentes.index')->with('success-message', 'El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_docentes');

        try {
            $docente = Docente::findOrFail($id);
            $docente->load(['nacionalidades', 'Carreras']);
            $sexos = Sexo::where('estado', 'AC')->get();
            $nacionalidades = Nacionalidad::get();
            $departamentos_paraguay = DepartamentoParaguay::get();
            $ciudades = Ciudad::get();
            $barrios = Barrio::get();
            $niveles_academicos = DocenteNivelAcademico::where('estado', 'AC')->get();
            $usuarios = User::where('state', 'AC')->get();
            $areas_conocimientos = AreaConocimiento::get();
            $carreras = \App\Models\Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('docentes/edit')->with(compact('carreras', 'docente', 'sexos', 'nacionalidades', 'departamentos_paraguay', 'ciudades', 'barrios', 'niveles_academicos', 'usuarios', 'areas_conocimientos'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_docentes');

        $request->validate([
            'primer_nombre_docente' => 'required',
            'segundo_nombre_docente' => 'nullable',
            'tercer_nombre_docente' => 'nullable',
            'primer_apellido_docente' => 'required',
            'segundo_apellido_docente' => 'nullable',
            'numero_documento' => ['required', Rule::unique('docentes')->ignore($id)],
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
            'barrio' => ['required', 'numeric'],
            'usuario' => ['required', 'numeric'],
            'nivel_academico' => ['required', 'numeric'],
            'capacitacion_didactica' => 'required',
            'area_conocimiento' => ['required', 'numeric'],
            'carreras' => ['nullable', 'array'],
            'carreras.*' => ['numeric'],
            'ubs' => 'required',
            'tutor_tesis' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);
            $docente->primer_nombre = removeAccents(Str::upper($request->primer_nombre_docente));
            $docente->segundo_nombre = removeAccents(Str::upper($request->segundo_nombre_docente));
            $docente->tercer_nombre = removeAccents(Str::upper($request->tercer_nombre_docente));
            $docente->primer_apellido = removeAccents(Str::upper($request->primer_apellido_docente));
            $docente->segundo_apellido = removeAccents(Str::upper($request->segundo_apellido_docente));
            $docente->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $docente->sexo_id = $request->sexo;
            $docente->fecha_nacimiento = $request->fecha_nacimiento;
            $docente->telefono = $request->telefono;
            $docente->celular = $request->celular;
            $docente->nacionalidades()->sync($request->nacionalidad);
            $docente->Carreras()->sync($request->carreras ?? []);
            $docente->email_personal = removeAccents(Str::lower($request->email_personal));
            $docente->email_institucional = removeAccents(Str::lower($request->email_institucional));
            $docente->direccion = removeAccents(Str::upper($request->direccion));
            $docente->departamento_id = $request->departamento;
            $docente->ciudad_id = $request->ciudad;
            $docente->barrio_id = $request->barrio;
            $docente->nivel_academico_id = $request->nivel_academico;
            $docente->capacitacion_didactica = $request->capacitacion_didactica;
            $docente->area_conocimiento_id = $request->area_conocimiento;
            $docente->usuario_id = $request->usuario;
            $docente->actualizado_por_id = Auth::id();
            $docente->ubs = $request->ubs;
            $docente->tutor_tesis = $request->tutor_tesis;
            $docente->save();

            $usuario = User::findOrFail($docente->usuario_id);
            if ($docente->email_institucional != null && $usuario->email != $docente->email_institucional) {
                $usuario->email = $docente->email_institucional;
                $usuario->save();
            }

            DB::commit();

            return redirect()->route('docentes.index')->with('success-message', 'El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_docentes');

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);
            $docente->actualizado_por_id = Auth::id();
            $docente->estado = 'IN';
            $docente->save();

            DB::commit();

            return redirect()->route('docentes.index')->with('error-message','El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_docentes');

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);
            $docente->actualizado_por_id = Auth::id();
            $docente->estado = 'AC';
            $docente->save();

            DB::commit();

            return redirect()->route('docentes.index')->with('success-message','El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_docentes');

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);

            $docente_legajos = DocenteLegajo::where('docente_id', $docente->id)->delete();
            $docente_nacionalidades = DocenteNacionalidad::where('docente_id', $docente->id)->delete();

            $docente->delete();

            DB::commit();

            return redirect()->route('docentes.index')->with('success-message','El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('docentes.index')->with('error-message', 'El docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function ver_legajo($id)
    {
        $this->authorize('ver_legajos_docentes');

        try {
            $docente = Docente::findOrFail($id);
            $legajos = DocenteLegajo::where('docente_id', $docente->id)->get();
            $legajos_encontrados = array(); //crear un array para luego comparar
            foreach ($legajos as $legajo) {
                array_push($legajos_encontrados, $legajo->tipo_legajo_id); //recorrer lo encontrado y agregar solo el id al nuevo array
            }
            $tipos_legajos = TipoLegajo::whereNotIn('id', $legajos_encontrados) //comparar todos del modelo con lo encontrado
                                ->where('tipo', 'DO')
                                ->where('estado', 'AC')
                                ->get();
            $instituciones_educativas = InstitucionEducativa::where('tipo', 'UN')->get();
            $paises = Pais::get();

            return view('docentes/show_legajo')->with(compact('docente', 'legajos', 'tipos_legajos', 'instituciones_educativas', 'paises'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function subir_legajo(Request $request, $id)
    {
        $this->authorize('subir_legajos_docentes');

        $request->validate([
            'tipo_legajo' => ['required', 'numeric'],
            'titulo_obtenido' => ['nullable', 'required_if:tipo_legajo,8,9,10'],
            'institucion_educativa' => ['nullable', 'numeric', 'required_if:tipo_legajo,8,9,10'],
            'pais' => ['nullable', 'numeric', 'required_if:tipo_legajo,8,9,10'],
            'legajo' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);

            $legajo = new DocenteLegajo();
            $legajo->docente_id = $id;
            $legajo->titulo_obtenido = removeAccents(Str::upper($request->titulo_obtenido));
            $legajo->tipo_legajo_id = $request->tipo_legajo;
            $legajo->institucion_educativa_id = $request->institucion_educativa;
            $legajo->pais_id = $request->pais;

            $tipo_legajo = TipoLegajo::findOrFail($request->tipo_legajo);
            //cargar archivo
            $archivo = $request->legajo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/legajos/docentes/' . $docente->id . '_' . Str::lower($docente->primer_nombre) . '_' . Str::lower($docente->primer_apellido);
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = $tipo_legajo->id . '_' . Str::lower($tipo_legajo->nombre);
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/legajos/docentes/' . $docente->id . '_' . Str::lower($docente->primer_nombre) . '_' . Str::lower($docente->primer_apellido);
                $nombre = $tipo_legajo->id . '_' . Str::lower($tipo_legajo->nombre);
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $legajo->url_ubicacion = $directorio . '/' . $nombre_archivo;
            $legajo->extension = $extension;
            $legajo->save();

            DB::commit();

            return response()->json([
                'message' => 'El legajo del docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_legajo($id)
    {
        $this->authorize('eliminar_legajos_docentes');

        DB::beginTransaction();

        try {
            $legajo = DocenteLegajo::findOrFail($id);
            $nombre_legajo = $legajo->tipoLegajo->nombre;
            $ubicacion_archivo = $legajo->url_ubicacion;

            $docente = Docente::findOrFail($legajo->docente_id);

            Storage::delete($ubicacion_archivo);
            $legajo->delete();

            DB::commit();

            return redirect()->route('docentes.ver_legajo', $docente->id)->with('success-message','El legajo ' . $nombre_legajo . ' del docente ' . $docente->primer_nombre . ' ' . $docente->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_horas(Request $request, $id)
    {
        $this->authorize('ver_reportes_horas_docentes');

        try {
            $request->validate([
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
                'monto_virtual' => ['nullable', 'numeric', 'min:1'],
                'monto_teams' => ['nullable', 'numeric', 'min:1'],
                'monto_presencial' => ['nullable', 'numeric', 'min:1'],
            ]);

            $docente = Docente::findOrFail($id);
            $fecha_inicio = Carbon::parse($request->fecha_inicio);
            $fecha_fin = Carbon::parse($request->fecha_fin);
            $monto_virtual = $request->monto_virtual ? str_replace('.', '', $request->monto_virtual) : 0;
            $monto_teams = $request->monto_teams ? str_replace('.', '', $request->monto_teams) : 0;
            $monto_presencial = $request->monto_presencial ? str_replace('.', '', $request->monto_presencial) : 0;

            $fecha_inicio_string = $fecha_inicio->setTime(0, 0, 0)->toDateTimeString();
            $fecha_fin_string = $fecha_fin->setTime(23, 59, 59)->toDateTimeString();

            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();
            $periodo_activo = $semestre ? $semestre->id : null;

            if (!$periodo_activo) {
                return back()->with('error-message', 'No existe un periodo activo para generar el reporte solicitado.');
            }

            $semestre_malla_materias = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($periodo_activo) {
                $query->where('semestre_id', $periodo_activo)
                      ->whereHas('malla', function ($q) {
                        $q->whereHas('carrera', function ($k) {
                            $k->where('programa_id', 1); //solo para GA
                        });
                      });
            })
            ->where('docente_id', $docente->id)
            ->where('estado', 'AC')
            ->get();

            if ($semestre_malla_materias->count() == 0) {
                return back()->with('error-message', 'El docente no cuenta con materias en el periodo actual.');
            }

            $carreras = collect();
            $materias_virtuales = collect();
            $materias_teams = collect();
            $materias_presenciales = collect();

            foreach ($semestre_malla_materias as $smm) {
                $carreras->push(['carrera' => $smm->semestreMalla->malla->carrera->nombre_fantasia,
                                'abreviatura' => $smm->semestreMalla->malla->carrera->abreviatura]);

                $clases_virtuales = ClaseMateria::where('docente_id', $docente->id)
                                        ->where('materia_id', $smm->materia_id)
                                        ->where('semestre_id', $periodo_activo)
                                        ->whereBetween('fecha_hora', [$fecha_inicio_string, $fecha_fin_string])
                                        ->where('modalidad_id', 4)
                                        ->get();

                $clases_teams = ClaseMateria::where('docente_id', $docente->id)
                                        ->where('materia_id', $smm->materia_id)
                                        ->where('semestre_id', $periodo_activo)
                                        ->whereBetween('fecha_hora', [$fecha_inicio_string, $fecha_fin_string])
                                        ->where('modalidad_id', 2)
                                        ->get();

                $clases_presenciales = ClaseMateria::where('docente_id', $docente->id)
                                        ->where('materia_id', $smm->materia_id)
                                        ->where('semestre_id', $periodo_activo)
                                        ->whereBetween('fecha_hora', [$fecha_inicio_string, $fecha_fin_string])
                                        ->where('modalidad_id', 1)
                                        ->get();

                // if ($clases_virtuales->count() == 0 && $clases_teams->count() == 0 && $clases_presenciales->count() == 0) {
                //     return back()->with('error-message', 'El docente no cuenta con clases generadas en el mes actual.');
                // }

                if ($clases_virtuales->count() > 0) {
                    $datos_virtuales = ['materia' => $smm->materia->nombre_real,
                                        'horas' => $clases_virtuales->sum('horas_desarrollo'),
                                        'monto' => $monto_virtual];
                    $materias_virtuales->push($datos_virtuales);
                }

                if ($clases_teams->count() > 0) {
                    $datos_teams = ['materia' => $smm->materia->nombre_real,
                                    'horas' => $clases_teams->sum('horas_desarrollo'),
                                    'monto' => $monto_teams];
                    $materias_teams->push($datos_teams);
                }

                if ($clases_presenciales->count() > 0) {
                    $datos_presenciales = ['materia' => $smm->materia->nombre_real,
                                        'horas' => $clases_presenciales->sum('horas_desarrollo'),
                                        'monto' => $monto_presencial];
                    $materias_presenciales->push($datos_presenciales);
                }
            }

            $materias_virtuales = $materias_virtuales->groupBy('materia')->map(function ($grupo) {
                return $grupo->sortByDesc('horas')->first();
            });

            $materias_teams = $materias_teams->groupBy('materia')->map(function ($grupo) {
                return $grupo->sortByDesc('horas')->first();
            });

            $materias_presenciales = $materias_presenciales->groupBy('materia')->map(function ($grupo) {
                return $grupo->sortByDesc('horas')->first();
            });

            $carreras = $carreras->groupBy('abreviatura')->map(function ($grupo) {
                return $grupo->sortByDesc('nombre_fantasia')->first();
            });


            return view('docentes.show_horas')->with(compact('docente', 'semestre', 'fecha_inicio', 'fecha_fin', 'monto_virtual', 'monto_teams', 'monto_presencial', 'materias_virtuales', 'materias_teams', 'materias_presenciales', 'carreras'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function reporte_horas(Request $request, $id)
    {
        $this->authorize('ver_reportes_horas_docentes');

        try {
            $request->validate([
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'monto_virtual' => 'nullable',
                'monto_teams' => 'nullable',
                'monto_presencial' => 'nullable',
                'mes' => 'required',
                'carrera' => 'required',
                'programa' => 'required',

                'detalles' => ['required', 'array'],
                'detalles.*.materia' => 'required',
                'detalles.*.horas_virtuales' => ['required', 'numeric'],
                'detalles.*.horas_teams' => ['required', 'numeric'],
                'detalles.*.horas_presenciales' => ['required', 'numeric'],
                'detalles.*.observaciones' => 'nullable',

                'total_horas_virtuales' => 'required',
                'total_horas_teams' => 'required',
                'total_horas_presenciales' => 'required',
                'monto_total' => 'required'
            ]);

            $docente = Docente::findOrFail($id);
            $fecha_inicio = Carbon::parse($request->fecha_inicio);
            $fecha_fin = Carbon::parse($request->fecha_fin);
            $monto_virtual = $request->monto_virtual ? $request->monto_virtual : 0;
            $monto_teams = $request->monto_teams ? $request->monto_teams : 0;
            $monto_presencial = $request->monto_presencial ? $request->monto_presencial : 0;
            $mes = $request->mes ? $request->mes : 0;
            $carrera = $request->carrera ? $request->carrera : 0;
            $programa = $request->programa ? $request->programa : 0;

            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer
            $fecha_inicio_string = $fecha_inicio->setTime(0, 0, 0)->toDateTimeString();
            $fecha_fin_string = $fecha_fin->setTime(23, 59, 59)->toDateTimeString();

            $materias = collect();

            foreach ($request->detalles as $detalle) {
                $datos = ['materia' => $detalle['materia'],
                          'horas_virtuales' => $detalle['horas_virtuales'],
                          'horas_teams' => $detalle['horas_teams'],
                          'horas_presenciales' => $detalle['horas_presenciales'],
                          'observaciones' => Str::upper($detalle['observaciones'])];

                $materias->push($datos);
            }

            $total_horas_virtuales = $request->total_horas_virtuales ? $request->total_horas_virtuales : 0;
            $total_horas_teams = $request->total_horas_teams ? $request->total_horas_teams : 0;
            $total_horas_presenciales = $request->total_horas_presenciales ? $request->total_horas_presenciales : 0;
            $monto_total = $request->monto_total ? $request->monto_total : 0;

            $pdf = Pdf::loadView('docentes/pdf_horas', compact('empresa', 'fecha_hoy', 'docente', 'fecha_inicio', 'fecha_fin', 'monto_virtual', 'monto_teams', 'monto_presencial', 'mes', 'carrera', 'programa', 'materias', 'total_horas_virtuales', 'total_horas_teams', 'total_horas_presenciales', 'monto_total'));

            $nombre_archivo = 'salario_docente_id_' . $docente->id . '_' . Carbon::now()->format('dmY_His') . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/salarios/docentes/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);

            $salario = new DocenteSalario();
            $salario->docente_id = $docente->id;
            $salario->fecha_inicio = $fecha_inicio;
            $salario->fecha_fin = $fecha_fin;
            $salario->url_ubicacion = str_replace('public', 'storage', $ubicacion_archivo);
            $salario->cargado_por_id = Auth::id();
            $salario->save();

            return $pdf->stream('rpt_horas_docente_' . $docente->numero_documento . '_' . Carbon::now()->format('dmY_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }
}
