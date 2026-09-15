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
use Carbon\Carbon;

use App\Models\Convalidacion;
use App\Models\ConvalidacionDetalle;
use App\Models\PagoMatriculacion;
use App\Models\Alumno;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Programa;
use App\Models\InstitucionEducativa;
use App\Models\Materia;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\AlumnoNota;


class ConvalidacionExternaController extends Controller
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
        $this->authorize('ver_convalidaciones_externas');

        try {
            $convalidaciones = Convalidacion::where('tipo', 'EX')->orderBy('id', 'asc')->get();
            return view('convalidaciones/externas/index')->with(compact('convalidaciones'));
        } catch (\Exception $e) {
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_convalidaciones_externas');

        try {
            $convalidacion = Convalidacion::with('convalidacionDetalles')->findOrFail($id);
            $malla = Malla::where('carrera_id', $convalidacion->carrera_id)->first();
            $malla_detalles = MallaDetalle::where('malla_id', $malla->id)->get();
            $materias = collect();

            foreach ($malla_detalles as $detalle) {
                $materias->push($detalle->materia);
            }
            $materias = $materias->sortBy('nombre_fantasia');

            $fecha_hoy = Carbon::now()->format('Y-m-d');

            return view('convalidaciones/externas/show')->with(compact('convalidacion', 'materias', 'fecha_hoy'));
        } catch (\Exception $e) {
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_convalidaciones_externas');

        try {
            $alumnos = Alumno::where('estado', 'AC')->get();
            $carreras = Carrera::with('facultad', 'programa')->where('estado', 'AC')->get();
            $instituciones_educativas = InstitucionEducativa::where('tipo', 'UN')->where('estado', 'AC')->get();

            $anho_actual = Carbon::now()->format('Y'); //obtenemos el año actual
            $convalidacion = Convalidacion::orderBy('id', 'desc')->first();
            if ($convalidacion) {
                $partes_solicitud = explode('/', $convalidacion->numero_solicitud); //separamos el string de numero_solicitud
                if ($anho_actual == $partes_solicitud[1]) {
                    //comparamos si el año actual es igual al ultimo numero_solicitud obtenido, si es igual, sumamos 1 al primer numero
                    $numero_nuevo = intval($partes_solicitud[0]) + 1;
                    $solicitud_nueva = $numero_nuevo . '/' . $partes_solicitud[1]; //concatenamos el nuevo numero con el año del string
                } else {
                    //si no es igual, iniciamos en 1 el contador y concatenamos el año actual
                    $solicitud_nueva = '1/' . $anho_actual;
                }
            } else {
                $solicitud_nueva = '1/' . $anho_actual;
            }

            return view('convalidaciones/externas/create')->with(compact('alumnos', 'carreras', 'instituciones_educativas', 'solicitud_nueva'));
        } catch (\Exception $e) {
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_convalidaciones_externas');

        //obtener pagos del alumno, si es Matricula o 1ra cuota y esta pendiente o pago parcial, devuelve error.
        // $matriculacion = Matriculacion::where('alumno_id', $request->alumno)->orderBy('id', 'desc')->first();
        // $pago_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)
        //     ->whereIn('tipo', ['MA', '1C'])
        //     ->whereIn('estado', ['PE', 'PA'])
        //     ->get();
        // if ($pago_matriculacion) {
        //     return redirect()->route('convalidaciones_externas.index')->with('error-message', 'El alumno debe estar al día con sus pagos antes de realizar la solicitud de convalidación.');
        // }

        $request->validate([
            'numero_solicitud' => ['required', Rule::unique('convalidaciones')],
            'alumno' => ['required', 'numeric'],
            'universidad_origen' => ['required', 'numeric'],
            'facultad_origen' => 'required',
            'carrera_origen' => 'required',
            'certificado' => ['required', 'file', 'extensions:jpg,png,pdf'],

            'detalles' => ['required', 'array'],
            'detalles.*.materia_origen' => 'required',
            'detalles.*.calificacion_origen' => ['required', 'numeric', 'min:2', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $convalidacion = new Convalidacion();
            $convalidacion->numero_solicitud = removeAccents(Str::upper($request->numero_solicitud));
            $convalidacion->tipo = 'EX';
            $convalidacion->alumno_id = $request->alumno;
            $convalidacion->facultad_origen = removeAccents(Str::upper($request->facultad_origen));
            $matriculacion = Matriculacion::where('alumno_id', $convalidacion->alumno_id)->orderBy('id', 'desc')->first();
            $convalidacion->carrera_id = $matriculacion->carrera_id;
            $carrera = Carrera::findOrFail($convalidacion->carrera_id);
            $convalidacion->facultad_id = $carrera->facultad_id;
            $convalidacion->carrera_origen = removeAccents(Str::upper($request->carrera_origen));
            $convalidacion->programa_id = $carrera->programa_id;
            $convalidacion->universidad_origen_id = $request->universidad_origen;
            //cargar archivo
            $archivo = $request->certificado;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/convalidaciones/externas/certificados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = Str::lower($convalidacion->alumno->numero_documento) . '_' . Str::lower($convalidacion->alumno->primer_nombre) . '_' . Str::lower($convalidacion->alumno->primer_apellido);
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/convalidaciones/externas/certificados';
                $nombre = Str::lower($convalidacion->alumno->numero_documento) . '_' . Str::lower($convalidacion->alumno->primer_nombre) . '_' . Str::lower($convalidacion->alumno->primer_apellido);
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $convalidacion->ubicacion_certificado_estudios = $directorio . '/' . $nombre_archivo;
            $convalidacion->extension_certificado_estudios = $extension;
            $convalidacion->estado = 'SO';
            $convalidacion->cargado_por_id = Auth::id();
            $convalidacion->save();

            foreach ($request->detalles as $detalle) {
                $convalidacion_detalle = new ConvalidacionDetalle();
                $convalidacion_detalle->convalidacion_id = $convalidacion->id;
                $convalidacion_detalle->materia_origen = Str::upper($detalle['materia_origen']);
                $convalidacion_detalle->calificacion_origen = $detalle['calificacion_origen'];
                $convalidacion_detalle->save();
            }

            DB::commit();

            return redirect()->route('convalidaciones_externas.index')->with('success-message', 'La solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' del alumno ' . $convalidacion->alumno->primer_nombre . ' ' . $convalidacion->alumno->primer_apellido . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_convalidaciones_externas');

        try {
            $convalidacion = Convalidacion::findOrFail($id);
            $carreras = Carrera::with('facultad', 'programa')->where('estado', 'AC')->get();
            $instituciones_educativas = InstitucionEducativa::where('tipo', 'UN')->where('estado', 'AC')->get();

            return view('convalidaciones/externas/edit')->with(compact('convalidacion', 'carreras', 'instituciones_educativas'));
        } catch (\Exception $e) {
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_convalidaciones_externas');

        $request->validate([
            'universidad_origen' => ['required', 'numeric'],
            'facultad_origen' => 'required',
            'carrera_origen' => 'required',

            'detalles' => ['required', 'array'],
            'detalles.*.materia_origen' => 'required',
            'detalles.*.calificacion_origen' => ['required', 'numeric', 'min:2', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $convalidacion = Convalidacion::findOrFail($id);
            $convalidacion->facultad_origen = removeAccents(Str::upper($request->facultad_origen));
            $convalidacion->carrera_origen = removeAccents(Str::upper($request->carrera_origen));
            $convalidacion->universidad_origen_id = $request->universidad_origen;
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->save();

            $detalles = ConvalidacionDetalle::where('convalidacion_id', $convalidacion->id)->delete();
            foreach ($request->detalles as $detalle) {
                $convalidacion_detalle = new ConvalidacionDetalle();
                $convalidacion_detalle->convalidacion_id = $convalidacion->id;
                $convalidacion_detalle->materia_origen = removeAccents(Str::upper($detalle['materia_origen']));
                $convalidacion_detalle->calificacion_origen = $detalle['calificacion_origen'];
                $convalidacion_detalle->save();
            }

            DB::commit();

            return redirect()->route('convalidaciones_externas.index')->with('success-message', 'La solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' del alumno ' . $convalidacion->alumno->primer_nombre . ' ' . $convalidacion->alumno->primer_apellido . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function change_certificado(Request $request, $id)
    {
        $this->authorize('editar_convalidaciones_externas');

        $request->validate([
            'certificado' => ['required', 'file', 'extensions:jpg,png,pdf'],
        ]);

        DB::beginTransaction();

        try {
            $convalidacion = Convalidacion::findOrFail($id);
            $ubicacion_archivo = $convalidacion->ubicacion_certificado_estudios;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            //cargar archivo
            $archivo = $request->certificado;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/convalidaciones/externas/certificados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = Str::lower($convalidacion->alumno->numero_documento) . '_' . Str::lower($convalidacion->alumno->primer_nombre) . '_' . Str::lower($convalidacion->alumno->primer_apellido);
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/convalidaciones/externas/certificados';
                $nombre = Str::lower($convalidacion->alumno->numero_documento) . '_' . Str::lower($convalidacion->alumno->primer_nombre) . '_' . Str::lower($convalidacion->alumno->primer_apellido);
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $convalidacion->ubicacion_certificado_estudios = $directorio . '/' . $nombre_archivo;
            $convalidacion->extension_certificado_estudios = $extension;
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El certificado de estudios adjuntos de la solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' fue actualizado exitosamente.',
                'ubicacion' => $convalidacion->ubicacion_certificado_estudios,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_numero_dictamen($id)
    {
        $anho_actual = Carbon::now()->format('Y'); //obtenemos el año actual
        $convalidacion = Convalidacion::whereIn('estado', ['DI', 'AP', 'CO'])->orderBy('id', 'desc')->first();
        if ($convalidacion) {
            $convalidacion_detalle = ConvalidacionDetalle::where('convalidacion_id', $convalidacion->id)->orderBy('id', 'desc')->first();
            $partes_solicitud = explode('/', $convalidacion_detalle->numero_dictamen); //separamos el string de numero_dictamen
            if ($anho_actual == $partes_solicitud[1]) {
                //comparamos si el año actual es igual al ultimo numero_dictamen obtenido, si es igual, sumamos 1 al primer numero
                $numero_nuevo = intval($partes_solicitud[0]) + 1;
                $solicitud_nueva = $numero_nuevo . '/' . $partes_solicitud[1]; //concatenamos el nuevo numero con el año del string
            } else {
                //si no es igual, iniciamos en 1 el contador y concatenamos el año actual
                $solicitud_nueva = '1/' . $anho_actual;
            }
        } else {
            $solicitud_nueva = '1/' . $anho_actual;
        }

        return response()->json([
            'numero' => $solicitud_nueva,
        ]);
    }

    public function dictaminar(Request $request, $id)
    {
        $this->authorize('dictaminar_convalidaciones_externas');

        $fecha_hoy = Carbon::now()->format('Y-m-d');

        $request->validate([
            'carga_horaria_materia_origen' => ['required', 'numeric'],
            'numero_dictamen' => ['required', Rule::unique('convalidaciones_detalles', 'numero_dictamen')->ignore($id)],
            'fecha_dictamen' => ['required', 'date', 'before_or_equal:' . $fecha_hoy],
            'materia' => ['required', 'numeric'],
            'porcentaje_coincidencia_bruta' => ['required', 'numeric', 'min:75', 'max:100'],
            'dictamen' => ['nullable', 'file', 'extensions:jpg,png,pdf'],
        ]);

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $detalle->carga_horaria_materia_origen = $request->carga_horaria_materia_origen;
            $detalle->numero_dictamen = removeAccents(Str::upper($request->numero_dictamen));
            $detalle->fecha_dictamen = $request->fecha_dictamen;
            $detalle->materia_id = $request->materia;
            $detalle->porcentaje_coincidencia_bruta = $request->porcentaje_coincidencia_bruta;
            //cargar archivo
            if ($request->dictamen) {
                $archivo = $request->dictamen;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/convalidaciones/externas/dictamenes';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = Str::lower($detalle->numero_dictamen) . '_' . $detalle->fecha_dictamen;
                    $nombre = str_replace('/', '-', $nombre);
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                } else {
                    $directorio_storage = 'public/convalidaciones/externas/dictamenes';
                    $nombre = Str::lower($detalle->numero_dictamen) . '_' . $detalle->fecha_dictamen;
                    $nombre = str_replace('/', '-', $nombre);
                    $nombre_archivo = $nombre . '.' . $extension;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                }
                $detalle->ubicacion_dictamen = $directorio . '/' . $nombre_archivo;
                $detalle->extension_dictamen = $extension;
            }
            $detalle->save();

            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->estado = 'DI';
            $convalidacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El dictamen de convalidación N° ' . $detalle->numero_dictamen . ' fue guardado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function cancel_dictamen($id)
    {
        $this->authorize('anular_dictamen_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $detalle->carga_horaria_materia_origen = null;
            $detalle->numero_dictamen = null;
            $detalle->fecha_dictamen = null;
            $detalle->materia_id = null;
            $detalle->porcentaje_coincidencia_bruta = null;
            $ubicacion_archivo = $detalle->ubicacion_dictamen;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $detalle->ubicacion_dictamen = null;
            $detalle->extension_dictamen = null;
            $detalle->save();

            Storage::delete($ubicacion_archivo);

            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->estado = 'SO';
            $convalidacion->save();

            DB::commit();


            return redirect()->route('convalidaciones_externas.show', $convalidacion->id)->with('success-message', 'El dictaminado de la solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' fue revertida exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy_dictamen($id)
    {
        $this->authorize('eliminar_adjunto_dictamen_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $ubicacion_archivo = $detalle->ubicacion_dictamen;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $detalle->ubicacion_dictamen = null;
            $detalle->extension_dictamen = null;
            $detalle->save();

            Storage::delete($ubicacion_archivo);

            DB::commit();

            return redirect()->route('convalidaciones_externas.show', $convalidacion->id)->with('success-message','El adjunto del dictamen de convalidación N° ' . $detalle->numero_dictamen . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidacion_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_numero_resolucion($id)
    {
        $anho_actual = Carbon::now()->format('Y'); //obtenemos el año actual
        $convalidacion = Convalidacion::whereIn('estado', ['AP', 'CO'])->orderBy('id', 'desc')->first();
        if ($convalidacion) {
            $convalidacion_detalle = ConvalidacionDetalle::where('convalidacion_id', $convalidacion->id)->orderBy('id', 'desc')->first();
            $partes_solicitud = explode('/', $convalidacion_detalle->numero_resolucion); //separamos el string de numero_resolucion
            if ($anho_actual == $partes_solicitud[1]) {
                //comparamos si el año actual es igual al ultimo numero_resolucion obtenido, si es igual, sumamos 1 al primer numero
                $numero_nuevo = intval($partes_solicitud[0]) + 1;
                $solicitud_nueva = $numero_nuevo . '/' . $partes_solicitud[1]; //concatenamos el nuevo numero con el año del string
            } else {
                //si no es igual, iniciamos en 1 el contador y concatenamos el año actual
                $solicitud_nueva = '1/' . $anho_actual;
            }
        } else {
            $solicitud_nueva = '1/' . $anho_actual;
        }

        return response()->json([
            'numero' => $solicitud_nueva,
        ]);
    }

    public function aprobar(Request $request, $id)
    {
        $this->authorize('resolucion_convalidaciones_externas');

        $fecha_hoy = Carbon::now()->format('Y-m-d');

        $request->validate([
            'numero_resolucion' => ['required', Rule::unique('convalidaciones_detalles', 'numero_resolucion')->ignore($id)],
            'fecha_resolucion' => ['required', 'date', 'before_or_equal:' . $fecha_hoy],
            'resolucion' => ['nullable', 'file', 'extensions:jpg,png,pdf'],
        ]);

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $detalle->numero_resolucion = removeAccents(Str::upper($request->numero_resolucion));
            $detalle->fecha_resolucion = $request->fecha_resolucion;
            //cargar archivo
            if ($request->resolucion) {
                $archivo = $request->resolucion;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/convalidaciones/externas/resoluciones';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = Str::lower($detalle->numero_resolucion) . '_' . $detalle->fecha_resolucion;
                    $nombre = str_replace('/', '-', $nombre);
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                } else {
                    $directorio_storage = 'public/convalidaciones/externas/resoluciones';
                    $nombre = Str::lower($detalle->numero_resolucion) . '_' . $detalle->fecha_resolucion;
                    $nombre = str_replace('/', '-', $nombre);
                    $nombre_archivo = $nombre . '.' . $extension;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                }
                $detalle->ubicacion_resolucion = $directorio . '/' . $nombre_archivo;
                $detalle->extension_resolucion = $extension;
            }
            $detalle->save();

            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->estado = 'AP';
            $convalidacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La resolución de convalidación N° ' . $detalle->numero_resolucion . ' fue guardada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function cancel_resolucion($id)
    {
        $this->authorize('anular_resolucion_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $detalle->numero_resolucion = null;
            $detalle->fecha_resolucion = null;
            $ubicacion_archivo = $detalle->ubicacion_resolucion;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $detalle->ubicacion_resolucion = null;
            $detalle->extension_resolucion = null;
            $detalle->save();

            Storage::delete($ubicacion_archivo);

            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->estado = 'DI';
            $convalidacion->save();

            DB::commit();


            return redirect()->route('convalidaciones_externas.show', $convalidacion->id)->with('success-message', 'La aprobación de la solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' fue revertida exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy_resolucion($id)
    {
        $this->authorize('eliminar_adjunto_resolucion_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $detalle = ConvalidacionDetalle::findOrFail($id);
            $convalidacion = Convalidacion::findOrFail($detalle->convalidacion_id);
            $ubicacion_archivo = $detalle->ubicacion_resolucion;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $detalle->ubicacion_resolucion = null;
            $detalle->extension_resolucion = null;
            $detalle->save();

            Storage::delete($ubicacion_archivo);

            DB::commit();

            return redirect()->route('convalidaciones_externas.show', $convalidacion->id)->with('success-message','El adjunto de la resolución de convalidación N° ' . $detalle->numero_resolucion . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidacion_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function convalidar($id)
    {
        $this->authorize('convalidar_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $convalidacion = Convalidacion::findOrFail($id);
            $detalles = ConvalidacionDetalle::where('convalidacion_id', $convalidacion->id)->get();
            $matriculacion = Matriculacion::where('alumno_id', $convalidacion->alumno_id)->orderBy('id', 'desc')->first();

            foreach ($detalles as $detalle) {
                $inscripcion = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $detalle->materia_id)->first();
                if ($inscripcion) {
                    if ($inscripcion->estado == 'MA' || $inscripcion->estado == 'EC') {
                        $inscripcion->estado = 'CO';
                        $inscripcion->actualizado_por_id = Auth::id();
                        $inscripcion->save();

                        $nota = new AlumnoNota();
                        $nota->alumno_id = $convalidacion->alumno_id;
                        $nota->carrera_id = $convalidacion->carrera_id;
                        $nota->materia_id = $detalle->materia_id;
                        $nota->semestre_id = $matriculacion->semestre_id;
                        $nota->calificacion = $detalle->calificacion_origen;
                        $nota->evaluacion = 'CONVALIDADO';
                        $nota->save();
                    } else {
                        return redirect()->route('convalidaciones_externas.index')->with('error-message', 'La convalidación no se puede realizar. El alumno ' . $convalidacion->alumno->primer_nombre . ' ' . $convalidacion->alumno->primer_apellido . '  ya cuenta con las materias seleccionadas aprobadas.');
                    }
                } else {
                    $inscripcion = new Inscripcion();
                    $inscripcion->fecha = Carbon::now()->format('Y-m-d H:i:s');
                    $inscripcion->matriculacion_id = $matriculacion->id;
                    $inscripcion->materia_id = $detalle->materia_id;
                    $inscripcion->alumno_id = $convalidacion->alumno_id;
                    $inscripcion->estado = 'CO';
                    $inscripcion->cargado_por_id = Auth::id();
                    $inscripcion->save();

                    $nota = new AlumnoNota();
                    $nota->alumno_id = $convalidacion->alumno_id;
                    $nota->carrera_id = $convalidacion->carrera_id;
                    $nota->materia_id = $detalle->materia_id;
                    $nota->semestre_id = $matriculacion->semestre_id;
                    $nota->calificacion = $detalle->calificacion_origen;
                    $nota->evaluacion = 'CONVALIDADO';
                    $nota->save();
                }
            }

            $convalidacion->estado = 'CO';
            $convalidacion->actualizado_por_id = Auth::id();
            $convalidacion->save();

            DB::commit();

            return redirect()->route('convalidaciones_externas.index')->with('success-message', 'La convalidación de materias del alumno ' . $convalidacion->alumno->primer_nombre . ' ' . $convalidacion->alumno->primer_apellido . ' fue realizada existosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_convalidaciones_externas');

        DB::beginTransaction();

        try {
            $convalidacion = Convalidacion::findOrFail($id);
            if ($convalidacion->estado == 'SO') {
                $detalles = ConvalidacionDetalle::where('convalidacion_id', $convalidacion->id)->delete();

                $ubicacion_archivo = $convalidacion->ubicacion_certificado_estudios;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                $convalidacion->delete();

                Storage::delete($ubicacion_archivo);

                DB::commit();

                return redirect()->route('convalidaciones_externas.index')->with('success-message','La solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' fue eliminada exitosamente.');
            } else {
                return redirect()->route('convalidaciones_externas.index')->with('error-message','La solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' no se puede eliminar. Ésta ya se encuentra dictaminada o aprobada.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('convalidaciones_externas.index')->with('error-message', 'La solicitud de convalidación N° ' . $convalidacion->numero_solicitud . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_carrera($alumno)
    {
        DB::beginTransaction();

        try {
            $matriculacion = Matriculacion::where('alumno_id', $alumno)->orderBy('id', 'desc')->first();
            $carrera = Carrera::findOrFail($matriculacion->carrera_id);

            return response()->json([
                'carrera' => $carrera->nombre_fantasia,
                'facultad' => $carrera->facultad->nombre,
                'programa' => $carrera->programa->nombre,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('convalidaciones_externas.index')->with('error-message', $e->getMessage());
        }
    }
}
