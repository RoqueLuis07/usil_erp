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

use App\Models\Alumno;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\RequerimientoExtensionUniversitaria;
use App\Support\ResumenExtensionAlumno;
use App\Models\TipoExtensionUniversitaria;
use App\Traits\ResuelveCarreraSemestreAlumno;

class PantallaAlumnoController extends Controller
{
    use ResuelveCarreraSemestreAlumno;

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

    public function extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            // El progreso de horas/actividades solo cuenta postulaciones ya
            // aceptadas; las pendientes/rechazadas se muestran aparte en
            // "Mis Postulaciones" (variable $postulaciones más abajo).
            $extensiones = ExtensionUniversitariaDetalle::with('extensionUniversitaria.tipoExtension')->where('alumno_id', $alumno->id)->where('estado', 'AC')->get();
            $postulaciones = ExtensionUniversitariaDetalle::with('extensionUniversitaria.tipoExtension')->where('alumno_id', $alumno->id)->orderByDesc('fecha_postulacion')->get();

            // El requerimiento puede estar definido por carrera; si la carrera del alumno no tiene uno propio, se usa el general (carrera_id null).
            $requerimiento = null;
            $carreraRequisito = $alumno->carrera_id;
            if ($carreraRequisito) {
                $requerimiento = RequerimientoExtensionUniversitaria::where('carrera_id', $carreraRequisito)->first();
            }
            if (!$requerimiento) {
                $requerimiento = RequerimientoExtensionUniversitaria::whereNull('carrera_id')->first();
            }
            $horas_requeridas = $requerimiento->horas_requeridas ?? 0;
            $actividades_requeridas = $requerimiento->actividades_requeridas ?? 0;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $extension->periodo = $fecha->year . '-' . ($fecha->month <= 7 ? 1 : 2);
            }

            // Avance por tipo de actividad (cualquier cantidad de tipos).
            $resumen = ResumenExtensionAlumno::para(collect([$alumno]))[$alumno->id];
            $horas_acreditadas = $resumen['horas_acreditadas'];
            $actividades_realizadas = $resumen['actividades_realizadas'];

            return view('pantallas_alumnos/extensiones_universitarias')->with(compact('alumno', 'extensiones', 'postulaciones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'resumen', 'actividades_realizadas'));

        } catch (\Exception $e) {
            report($e);
            abort(500, $e->getMessage());
        }
    }

    public function adjuntar_certificado_extensiones_universitarias(Request $request, $id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        $request->validate([
            'adjunto' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaDetalle::findOrFail($id);

            //cargar archivo
            $archivo = $request->adjunto;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/extensiones_universitarias/alumnos_certificados';
            if ($extension_archivo == 'jpg' || $extension_archivo == 'jpeg' || $extension_archivo == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = $extension->extension_universitaria_id . '_' . Str::lower($extension->alumno->numero_documento);
                $nombre_archivo = $nombre . '.' . $extension_archivo;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/extensiones_universitarias/alumnos_certificados';
                $nombre = $extension->extension_universitaria_id . '_' . Str::lower($extension->alumno->numero_documento);
                $nombre_archivo = $nombre . '.' . $extension_archivo;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $extension->url_certificado = $directorio . '/' . $nombre_archivo;
            $extension->save();

            DB::commit();

            return response()->json([
                'message' => 'El certificado de la extensión universitaria ' . $extension->extensionUniversitaria->nombre . ' fue adjuntado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_certificado_extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaDetalle::findOrFail($id);
            Storage::delete($extension->url_certificado);
            $extension->url_certificado = null;
            $extension->save();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('error-message','El certificado de la extensión universitaria ' . $extension->extensionUniversitaria->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    /**
     * Catálogo de proyectos de extensión abiertos a postulación: aprobados,
     * dentro de la fecha, abiertos a la carrera del alumno (o sin
     * restricción de carrera) y a los que el alumno todavía no se postuló.
     */
    public function catalogo_extensiones_universitarias($id)
    {
        $this->authorize('ver_catalogo_extensiones_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $carreraAlumnoId = $alumno->carrera_id;

            $yaPostuladoIds = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->pluck('extension_universitaria_id');

            $proyectos = \App\Models\ExtensionUniversitaria::with(['tipoExtension', 'docente', 'carreras'])
                ->where('estado', 'AP')
                ->where(function ($query) {
                    $query->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', now()->toDateString());
                })
                ->whereNotIn('id', $yaPostuladoIds)
                ->get()
                ->filter(function ($proyecto) use ($carreraAlumnoId) {
                    if ($proyecto->carreras->isEmpty()) {
                        return true; // sin restricción de carrera = abierto a todas
                    }
                    return $carreraAlumnoId && $proyecto->carreras->contains('id', $carreraAlumnoId);
                })
                ->values();

            return view('pantallas_alumnos/catalogo_extensiones_universitarias')->with(compact('alumno', 'proyectos'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function postular_extension_universitaria($idUsuario, $idExtension)
    {
        $this->authorize('postular_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $extension = \App\Models\ExtensionUniversitaria::with('carreras')->findOrFail($idExtension);

            if (!$alumno->tieneDatosParaExtension()) {
                throw new \Exception('Tu perfil todavía no tiene la carrera y el año de ingreso cargados. Pedile al encargado de Extensión que los complete para poder postularte.');
            }

            if (!$extension->estaAbiertaParaPostulacion()) {
                throw new \Exception('Este proyecto ya no está abierto a postulaciones.');
            }

            $yaPostulado = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->where('alumno_id', $alumno->id)->exists();
            if ($yaPostulado) {
                throw new \Exception('Ya te postulaste a este proyecto.');
            }

            $cupos = $extension->cuposDisponibles();
            if ($cupos !== null && $cupos <= 0) {
                throw new \Exception('No quedan cupos disponibles en este proyecto.');
            }

            $carreraPropia = $alumno->carrera_id;
            if (!$extension->carreras->isEmpty() && (!$carreraPropia || !$extension->carreras->contains("id", $carreraPropia))) {
                throw new \Exception('Este proyecto no está habilitado para tu carrera.');
            }

            $carreraSemestre = $this->resolverCarreraSemestreAlumno($alumno->id, $extension->fecha_inicio);

            $detalle = new ExtensionUniversitariaDetalle();
            $detalle->extension_universitaria_id = $extension->id;
            $detalle->alumno_id = $alumno->id;
            $detalle->carrera_id = $carreraSemestre['carrera_id'];
            $detalle->semestre_id = $carreraSemestre['semestre_id'];
            $detalle->estado = 'PE';
            $detalle->fecha_postulacion = now();
            $detalle->save();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('success-message', 'Tu postulación a ' . $extension->nombre . ' fue enviada. Vas a ver el resultado en "Mis Postulaciones".');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.catalogo_extensiones_universitarias', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function cancelar_postulacion_extension_universitaria($idUsuario, $idDetalle)
    {
        $this->authorize('postular_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $detalle = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->findOrFail($idDetalle);

            if ($detalle->estado !== 'PE') {
                throw new \Exception('Solo se puede cancelar una postulación mientras está pendiente de revisión.');
            }

            $detalle->delete();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('success-message', 'La postulación fue cancelada.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('error-message', $e->getMessage());
        }
    }

}
