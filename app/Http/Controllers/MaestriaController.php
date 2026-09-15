<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

use App\Models\Curso;
use App\Models\Programa;
use App\Models\Facultad;
use App\Models\TipoCurso;
use App\Models\Modalidad;
use App\Models\CursoPrecio;


class MaestriaController extends Controller
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
        $this->authorize('ver_maestrias_ubs');

        try {
            $maestrias = Curso::where('es_maestria', true)->orderBy('nombre_fantasia', 'asc')->get();
            return view('ubs/maestrias/index')->with(compact('maestrias'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_maestrias_ubs');

        try {
            $maestria = Curso::with('precios', 'modulos', 'inscripciones')->findOrFail($id);
            return view('ubs/maestrias/show')->with(compact('maestria'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_maestrias_ubs');

        try {
            $programas = Programa::where('id', '>', 4)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_maestrias = TipoCurso::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('ubs/maestrias/create')->with(compact('programas', 'facultades', 'tipos_maestrias', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_maestrias_ubs');

        $request->validate([
            'nombre_fantasia' => 'required',
            'nombre_real' => 'required',
            'codigo' => 'required',
            'programa' => ['required', 'numeric'],
            'facultad' => ['required', 'numeric'],
            'tipo_curso' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'llamado' => ['required', 'numeric'],
            'fecha_apertura' => ['required', 'date', /*'after_or_equal:today',*/ function ($attribute, $value, $fail) {
                if (request('fecha_fin') && $value > request('fecha_fin')) {
                    $fail('La ' . $attribute . ' no puede ser mayor a la fecha de fin.');
                }
            }],
            'fecha_fin' => ['required', 'date', 'after:fecha_apertura'],
            'cantidad_horas' => ['required', 'numeric', 'min:1'],
            'cantidad_creditos' => ['required', 'numeric', 'min:1'],
            'duracion' => ['required', 'numeric', 'min:1'],
            'evaluacion' => 'required',
            'numero_ley' => ['required', Rule::unique('cursos')],
            'numero_acta' => ['required', Rule::unique('cursos')],
            'numero_resolucion_cones' => ['required', Rule::unique('cursos')],
            'cronograma' => ['nullable', 'file', 'extensions:jpg,png'],

            'precio_contado' => ['nullable', 'numeric', 'min:1'],
            'cantidad_cuotas' => ['nullable', 'numeric', 'max:24'],
            'precio_cuota' => ['nullable', 'numeric', 'min:1'],
            'fecha_inicio_vencimiento_cuota' => ['nullable', 'date', 'after_or_equal:fecha_apertura'],
            'dia_vencimiento_cuota' => ['nullable', 'numeric', 'max:10'],
            'precio_defensa' => ['nullable', 'numeric', 'min:1'],
            'precio_titulo' => ['nullable', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $maestria = new Curso();
            $maestria->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $maestria->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $maestria->codigo = removeAccents(Str::upper($request->codigo));
            $maestria->programa_id = $request->programa;
            $maestria->facultad_id = $request->facultad;
            $maestria->tipo_curso_id = $request->tipo_curso;
            $maestria->modalidad_id = $request->modalidad;
            $maestria->llamado = $request->llamado;
            $maestria->fecha_apertura = $request->fecha_apertura;
            $maestria->fecha_fin = $request->fecha_fin;
            $maestria->cantidad_horas = $request->cantidad_horas;
            $maestria->cantidad_creditos = $request->cantidad_creditos;
            $maestria->duracion = $request->duracion;
            $maestria->evaluacion = $request->evaluacion;
            $maestria->es_maestria = true;
            $maestria->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $maestria->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $maestria->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));

            //cargar archivo
            if ($request->cronograma) {
                $archivo = $request->cronograma;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/maestrias/cronogramas';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = 'cronograma_' . removeAccents(Str::lower(str_replace(' ', '_', $request->nombre_real)));
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                }
                $maestria->url_cronograma = $directorio . '/' . $nombre_archivo;
            }

            $maestria->cargado_por_id = Auth::id();
            $maestria->save();

            // if (Auth::user()->can('cargar precios cursos ubs')) {
                $precios = new CursoPrecio();
                $precios->curso_id = $maestria->id;
                $precios->moneda_id = 1;
                $precios->cantidad_cuotas = $request->cantidad_cuotas;
                $precios->precio_contado = $request->precio_contado;
                $precios->precio_cuota = $request->precio_cuota;
                $precios->precio_multa = 0;
                $precios->dia_vencimiento_cuota = $request->dia_vencimiento_cuota;
                $precios->fecha_inicio_vencimiento_cuota = $request->fecha_inicio_vencimiento_cuota;
                $precios->dias_gracia = 0;
                $precios->precio_defensa = $request->precio_defensa;
                $precios->precio_titulo = $request->precio_titulo;
                $precios->save();
            // }

            DB::commit();

            return redirect()->route('maestrias.index')->with('success-message', 'La maestría ' . $maestria->nombre_fantasia . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_maestrias_ubs');

        try {
            $maestria = Curso::findOrFail($id);
            $programas = Programa::where('id', '>', 4)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_maestrias = TipoCurso::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('ubs/maestrias/edit')->with(compact('maestria', 'programas', 'facultades', 'tipos_maestrias', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_maestrias_ubs');

        $maestria = Curso::findOrFail($id);
        $fecha_apertura = $maestria->fecha_apertura;

        $request->validate([
            'nombre_fantasia' => 'required',
            'nombre_real' => 'required',
            'codigo' => 'required',
            'programa' => ['required', 'numeric'],
            'facultad' => ['required', 'numeric'],
            'tipo_curso' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'llamado' => ['required', 'numeric', 'min:1'],
            'fecha_apertura' => ['required', 'date', 'after_or_equal:' . $fecha_apertura, function ($attribute, $value, $fail) {
                if (request('fecha_fin') && $value > request('fecha_fin')) {
                    $fail('La ' . $attribute . ' no puede ser mayor a la fecha de fin.');
                }
            }],
            'fecha_fin' => ['required', 'date', 'after:fecha_apertura'],
            'cantidad_horas' => ['required', 'numeric', 'min:1'],
            'cantidad_creditos' => ['required', 'numeric', 'min:1'],
            'duracion' => ['required', 'numeric', 'min:1'],
            'evaluacion' => 'required',
            'numero_ley' => ['required', Rule::unique('cursos')->ignore($id)],
            'numero_acta' => ['required', Rule::unique('cursos')->ignore($id)],
            'numero_resolucion_cones' => ['required', Rule::unique('cursos')->ignore($id)],
            'cronograma' => ['nullable', 'file', 'extensions:jpg,png'],

            'precio_contado' => ['nullable', 'numeric', 'min:1'],
            'cantidad_cuotas' => ['nullable', 'numeric', 'max:24'],
            'precio_cuota' => ['nullable', 'numeric', 'min:1'],
            'fecha_inicio_vencimiento_cuota' => ['nullable', 'date', 'after_or_equal:fecha_apertura'],
            'dia_vencimiento_cuota' => ['nullable', 'numeric', 'max:10'],
            'precio_defensa' => ['nullable', 'numeric', 'min:1'],
            'precio_titulo' => ['nullable', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $maestria = Curso::findOrFail($id);
            $maestria->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $maestria->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $maestria->codigo = removeAccents(Str::upper($request->codigo));
            $maestria->programa_id = $request->programa;
            $maestria->facultad_id = $request->facultad;
            $maestria->tipo_curso_id = $request->tipo_curso;
            $maestria->modalidad_id = $request->modalidad;
            $maestria->llamado = $request->llamado;
            $maestria->fecha_apertura = $request->fecha_apertura;
            $maestria->fecha_fin = $request->fecha_fin;
            $maestria->cantidad_horas = $request->cantidad_horas;
            $maestria->cantidad_creditos = $request->cantidad_creditos;
            $maestria->duracion = $request->duracion;
            $maestria->evaluacion = $request->evaluacion;
            $maestria->es_maestria = true;
            $maestria->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $maestria->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $maestria->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));

            //cargar archivo
            if ($request->cronograma) {
                $ubicacion_archivo = $maestria->url_cronograma;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);

                $archivo = $request->cronograma;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/maestrias/cronogramas';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = 'cronograma_' . removeAccents(Str::lower(str_replace(' ', '_', $request->nombre_real)));
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                }
                $maestria->url_cronograma = $directorio . '/' . $nombre_archivo;
            }

            $maestria->actualizado_por_id = Auth::id();
            $maestria->save();

            // if (Auth::user()->can('cargar precios cursos ubs')) {
                $precios = CursoPrecio::where('curso_id', $maestria->id)->first();
                $precios->cantidad_cuotas = $request->cantidad_cuotas;
                $precios->precio_contado = $request->precio_contado;
                $precios->precio_cuota = $request->precio_cuota;
                $precios->dia_vencimiento_cuota = $request->dia_vencimiento_cuota;
                $precios->fecha_inicio_vencimiento_cuota = $request->fecha_inicio_vencimiento_cuota;
                $precios->precio_defensa = $request->precio_defensa;
                $precios->precio_titulo = $request->precio_titulo;
                $precios->save();
            // }

            DB::commit();

            return redirect()->route('maestrias.index')->with('success-message', 'La maestría ' . $maestria->nombre_fantasia . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_maestrias_ubs');

        DB::beginTransaction();

        try {
            $maestria = Curso::findOrFail($id);
            $maestria->actualizado_por_id = Auth::id();
            $maestria->estado = 'IN';
            $maestria->save();

            DB::commit();

            return redirect()->route('maestrias.index')->with('error-message','La maestría ' . $maestria->nombre_fantasia . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_maestrias_ubs');

        DB::beginTransaction();

        try {
            $maestria = Curso::findOrFail($id);
            $maestria->actualizado_por_id = Auth::id();
            $maestria->estado = 'AC';
            $maestria->save();

            DB::commit();

            return redirect()->route('maestrias.index')->with('success-message','La maestría ' . $maestria->nombre_fantasia . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_maestrias_ubs');

        DB::beginTransaction();

        try {
            $maestria = Curso::findOrFail($id);
            $maestria->delete();

            DB::commit();

            return redirect()->route('maestrias.index')->with('success-message','La maestría ' . $maestria->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('maestrias.index')->with('error-message', 'La maestría ' . $maestria->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
