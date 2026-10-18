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

            // Actividades de extensión que tiene a su cargo.
            $actividades = \App\Models\ExtensionUniversitaria::with('tipoExtension')
                ->where('docente_id', $docente->id)
                ->orderByDesc('fecha_inicio')
                ->get();

            return view('docentes/show')->with(compact('docente', 'nombre_docente', 'actividades'));
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
            'nombres_docente' => 'required',
            'apellidos_docente' => 'required',
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
            'barrio' => ['nullable', 'numeric'],
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
            $docente->primer_nombre = removeAccents(Str::upper(trim($request->nombres_docente)));
            $docente->segundo_nombre = null;
            $docente->tercer_nombre = null;
            $docente->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_docente)));
            $docente->segundo_apellido = null;
            $docente->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $docente->sexo_id = $request->sexo;
            $docente->fecha_nacimiento = $request->fecha_nacimiento;
            $docente->telefono = $request->telefono;
            $docente->celular = $request->celular;
            $docente->email_personal = removeAccents(Str::lower($request->email_personal));
            $docente->direccion = removeAccents(Str::upper($request->direccion));
            $docente->departamento_id = $request->departamento;
            $docente->ciudad_id = $request->ciudad;
            $docente->barrio_id = $request->barrio ?: null;
            $docente->nivel_academico_id = $request->nivel_academico;
            $docente->capacitacion_didactica = filter_var($request->capacitacion_didactica, FILTER_VALIDATE_BOOLEAN);
            $docente->area_conocimiento_id = $request->area_conocimiento;

            if ($request->usuario != null) {
                $docente->usuario_id = $request->usuario;
                $docente->email_institucional = User::findOrFail($request->usuario)->email;
            } else {
                $usuario = new User();
                $usuario->name = removeAccents(Str::upper(trim($request->nombres_docente))) . ' ' . removeAccents(Str::upper(trim($request->apellidos_docente)));
                $usuario->email = removeAccents(Str::lower($request->email_personal));
                $usuario->role_id = 3; //asignar el id de rol docente
                $usuario->assignRole(3); //asignar el id de rol docente
                $password = $request->numero_documento . '-' . Str::substr(removeAccents(Str::upper(trim($request->nombres_docente))), 0, 1) . Str::substr(removeAccents(Str::lower(trim($request->apellidos_docente))), 0, 1);
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

            $docente->tutor_tesis = filter_var($request->tutor_tesis, FILTER_VALIDATE_BOOLEAN);
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
            'nombres_docente' => 'required',
            'apellidos_docente' => 'required',
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
            'barrio' => ['nullable', 'numeric'],
            'usuario' => ['required', 'numeric'],
            'nivel_academico' => ['required', 'numeric'],
            'capacitacion_didactica' => 'required',
            'area_conocimiento' => ['required', 'numeric'],
            'carreras' => ['nullable', 'array'],
            'carreras.*' => ['numeric'],
            'tutor_tesis' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $docente = Docente::findOrFail($id);
            $docente->primer_nombre = removeAccents(Str::upper(trim($request->nombres_docente)));
            $docente->segundo_nombre = null;
            $docente->tercer_nombre = null;
            $docente->primer_apellido = removeAccents(Str::upper(trim($request->apellidos_docente)));
            $docente->segundo_apellido = null;
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
            $docente->barrio_id = $request->barrio ?: null;
            $docente->nivel_academico_id = $request->nivel_academico;
            $docente->capacitacion_didactica = filter_var($request->capacitacion_didactica, FILTER_VALIDATE_BOOLEAN);
            $docente->area_conocimiento_id = $request->area_conocimiento;
            $docente->usuario_id = $request->usuario;
            $docente->actualizado_por_id = Auth::id();
            $docente->tutor_tesis = filter_var($request->tutor_tesis, FILTER_VALIDATE_BOOLEAN);
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

}
