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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Curso;
use App\Models\Programa;
use App\Models\Facultad;
use App\Models\TipoCurso;
use App\Models\Modalidad;
use App\Models\CursoPrecio;
use App\Models\CursoCertificadoGenerado;


class CursoController extends Controller
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
        $this->authorize('ver_cursos_ubs');

        try {
            $cursos = Curso::where('es_maestria', false)->orderBy('nombre_fantasia', 'asc')->get();
            return view('ubs/cursos/index')->with(compact('cursos'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_cursos_ubs');

        try {
            $curso = Curso::with('precios', 'modulos', 'inscripciones')->findOrFail($id);
            return view('ubs/cursos/show')->with(compact('curso'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_cursos_ubs');

        try {
            $programas = Programa::where('id', '>', 4)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_cursos = TipoCurso::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('ubs/cursos/create')->with(compact('programas', 'facultades', 'tipos_cursos', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cursos_ubs');

        $request->validate([
            'nombre_fantasia' => 'required',
            'nombre_real' => 'required',
            'codigo' => 'required',
            'programa' => ['nullable', 'numeric'],
            'facultad' => ['nullable', 'numeric'],
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
            'evaluacion' => 'required',
            'numero_ley' => ['nullable', Rule::unique('cursos')],
            'numero_acta' => ['nullable', Rule::unique('cursos')],
            'numero_resolucion_cones' => ['nullable', Rule::unique('cursos')],
            'cronograma' => ['nullable', 'file', 'extensions:jpg,png'],

            'precio_contado' => ['required', 'numeric', 'min:1'],
            'cantidad_cuotas' => ['required', 'numeric', 'max:12'],
            'precio_cuota' => ['required', 'numeric', 'min:1'],
            'fecha_inicio_vencimiento_cuota' => ['required', 'date', 'after_or_equal:fecha_apertura'],
            'dia_vencimiento_cuota' => ['required', 'numeric', 'max:10'],
        ]);

        DB::beginTransaction();

        try {
            $curso = new Curso();
            $curso->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $curso->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $curso->codigo = removeAccents(Str::upper($request->codigo));
            $curso->programa_id = $request->programa;
            $curso->facultad_id = $request->facultad;
            $curso->tipo_curso_id = $request->tipo_curso;
            $curso->modalidad_id = $request->modalidad;
            $curso->llamado = $request->llamado;
            $curso->fecha_apertura = $request->fecha_apertura;
            $curso->fecha_fin = $request->fecha_fin;
            $curso->cantidad_horas = $request->cantidad_horas;
            $curso->evaluacion = $request->evaluacion;
            $curso->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $curso->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $curso->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));

            //cargar archivo
            if ($request->cronograma) {
                $archivo = $request->cronograma;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/cursos/cronogramas';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = 'cronograma_' . removeAccents(Str::lower(str_replace(' ', '_', $request->nombre_real)));
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                }
                $curso->url_cronograma = $directorio . '/' . $nombre_archivo;
            }

            $curso->cargado_por_id = Auth::id();
            $curso->save();

            $precios = new CursoPrecio();
            $precios->curso_id = $curso->id;
            $precios->moneda_id = 1;
            $precios->cantidad_cuotas = $request->cantidad_cuotas;
            $precios->precio_contado = $request->precio_contado;
            $precios->precio_cuota = $request->precio_cuota;
            $precios->precio_multa = 0;
            $precios->dia_vencimiento_cuota = $request->dia_vencimiento_cuota;
            $precios->fecha_inicio_vencimiento_cuota = $request->fecha_inicio_vencimiento_cuota;
            $precios->dias_gracia = 0;
            $precios->save();

            DB::commit();

            return redirect()->route('cursos.index')->with('success-message', 'El curso ' . $curso->nombre_fantasia . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_cursos_ubs');

        try {
            $curso = Curso::findOrFail($id);
            $programas = Programa::where('id', '>', 4)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_cursos = TipoCurso::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('ubs/cursos/edit')->with(compact('curso', 'programas', 'facultades', 'tipos_cursos', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cursos_ubs');

        $curso = Curso::findOrFail($id);
        $fecha_apertura = $curso->fecha_apertura;

        $request->validate([
            'nombre_fantasia' => 'required',
            'nombre_real' => 'required',
            'codigo' => 'required',
            'programa' => ['nullable', 'numeric'],
            'facultad' => ['nullable', 'numeric'],
            'tipo_curso' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'llamado' => ['required', 'numeric'],
            'fecha_apertura' => ['required', 'date', 'after_or_equal:' . $fecha_apertura, function ($attribute, $value, $fail) {
                if (request('fecha_fin') && $value > request('fecha_fin')) {
                    $fail('La ' . $attribute . ' no puede ser mayor a la fecha de fin.');
                }
            }],
            'fecha_fin' => ['required', 'date', 'after:fecha_apertura'],
            'cantidad_horas' => ['required', 'numeric'],
            'evaluacion' => 'required',
            'numero_ley' => ['nullable', Rule::unique('cursos')],
            'numero_acta' => ['nullable', Rule::unique('cursos')],
            'numero_resolucion_cones' => ['nullable', Rule::unique('cursos')],
            'cronograma' => ['nullable', 'file', 'extensions:jpg,png']
        ]);

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);
            $curso->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $curso->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $curso->codigo = removeAccents(Str::upper($request->codigo));
            $curso->programa_id = $request->programa;
            $curso->facultad_id = $request->facultad;
            $curso->tipo_curso_id = $request->tipo_curso;
            $curso->modalidad_id = $request->modalidad;
            $curso->llamado = $request->llamado;
            $curso->fecha_apertura = $request->fecha_apertura;
            $curso->fecha_fin = $request->fecha_fin;
            $curso->cantidad_horas = $request->cantidad_horas;
            $curso->evaluacion = $request->evaluacion;
            $curso->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $curso->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $curso->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));

            //cargar archivo
            if ($request->cronograma) {
                $ubicacion_archivo = $curso->url_cronograma;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);

                $archivo = $request->cronograma;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/cursos/cronogramas';
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = 'cronograma_' . removeAccents(Str::lower(str_replace(' ', '_', $request->nombre_real)));
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                }
                $curso->url_cronograma = $directorio . '/' . $nombre_archivo;
            }

            $curso->actualizado_por_id = Auth::id();
            $curso->save();

            $precios = CursoPrecio::where('curso_id', $curso->id)->first();
            $precios->cantidad_cuotas = $request->cantidad_cuotas;
            $precios->precio_contado = $request->precio_contado;
            $precios->precio_cuota = $request->precio_cuota;
            $precios->dia_vencimiento_cuota = $request->dia_vencimiento_cuota;
            $precios->fecha_inicio_vencimiento_cuota = $request->fecha_inicio_vencimiento_cuota;
            $precios->save();

            DB::commit();

            return redirect()->route('cursos.index')->with('success-message', 'El curso ' . $curso->nombre_fantasia . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_cursos_ubs');

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);
            $curso->actualizado_por_id = Auth::id();
            $curso->estado = 'IN';
            $curso->save();

            DB::commit();

            return redirect()->route('cursos.index')->with('error-message','El curso ' . $curso->nombre_fantasia . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_cursos_ubs');

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);
            $curso->actualizado_por_id = Auth::id();
            $curso->estado = 'AC';
            $curso->save();

            DB::commit();

            return redirect()->route('cursos.index')->with('success-message','El curso ' . $curso->nombre_fantasia . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cursos_ubs');

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);
            $curso->delete();

            DB::commit();

            return redirect()->route('cursos.index')->with('success-message','El curso ' . $curso->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            $curso = Curso::findOrFail($id);
            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('cursos.index')->with('error-message', 'El curso ' . $curso->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function certificados_generados($id)
    {
        $this->authorize('ver_certificados_generados_cursos_ubs');

        try {
            $curso = Curso::findOrFail($id);
            $certificados = CursoCertificadoGenerado::where('curso_id', $id)->where('estado', 'GE')->get();

            return view('ubs/cursos/certificados_generados/index')->with(compact('curso', 'certificados'));

        } catch (\Exception $e) {
            return redirect()->route('cursos.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function regenerate_certificados($id)
    {
        $this->authorize('regenerar_certificados_cursos_ubs');

        try {
            $certificado = CursoCertificadoGenerado::findOrFail($id);

            $curso = Curso::findOrFail($certificado->curso_id);
			$dia_inicio = Carbon::parse($curso->fecha_apertura)->format('d');
            $mes_inicio = Carbon::parse($curso->fecha_apertura)->translatedFormat('F');
            $anho_inicio = Carbon::parse($curso->fecha_apertura)->format('Y');
            $dia_fin = Carbon::parse($curso->fecha_fin)->format('d');
            $mes_fin = Carbon::parse($curso->fecha_fin)->translatedFormat('F');
            $anho_fin = Carbon::parse($curso->fecha_fin)->format('Y');

            if ($anho_inicio == $anho_fin) {
                $certificado->inicio = $dia_inicio . ' de ' . $mes_inicio;
            } else {
                $certificado->inicio = $dia_inicio . ' de ' . $mes_inicio . ' del ' . $anho_inicio;
            }
            $certificado->fin = $dia_fin . ' de ' . $mes_fin . ' del ' . $anho_fin;
            $certificado->llamado_anho = $curso->llamado . '.0' . Carbon::parse($curso->fecha_apertura)->format('y') . '.';

            $nombre_secretaria = 'Raquel Hellmann';
            $nombre_rector = 'Yan Speranza';
            $numero_resolucion = '17/20';

            $nombre_alumno = $certificado->alumno->primer_nombre;
            if ($certificado->alumno->segundo_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $certificado->alumno->segundo_nombre;
            }
            if ($certificado->alumno->tercer_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $certificado->alumno->tercer_nombre;
            }
            $nombre_alumno = $nombre_alumno . ' ' . $certificado->alumno->primer_apellido;
            if ($certificado->alumno->segundo_apellido) {
                $nombre_alumno = $nombre_alumno . ' ' . $certificado->alumno->segundo_apellido;
            }
            $numero_alumno = str_pad($certificado->numero_inscripcion, 5, '0', STR_PAD_LEFT);

            $fecha_generacion = Carbon::parse($certificado->created_at)->format('d/m/Y');

            $pdf = Pdf::loadView('ubs/cursos/certificados_generados/pdf_certificados', compact('certificado', 'nombre_secretaria', 'nombre_rector', 'numero_resolucion', 'nombre_alumno', 'numero_alumno', 'fecha_generacion'));
			$pdf->setOptions([
					'dpi' => 300,
					'isRemoteEnabled' => true,
				]);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('certificado_' . '_' . $certificado->alumno->numero_documento . '_' . Str::lower(str_replace(' ', '_', $certificado->curso->nombre_real)) . '_' . $curso->llamado . 'llamado.pdf');

        } catch (\Exception $e) {
            return redirect()->route('cursos.certificados_generados', $certificado->curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function update_certificados_generados(Request $request, $id)
    {
        $this->authorize('editar_certificados_cursos_ubs');

        $request->validate([
            'numero_orden' => ['required', 'numeric'],
            'numero_pagina' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $certificado = CursoCertificadoGenerado::findOrFail($id);
            $certificado->numero_orden = $request->numero_orden;
            $certificado->numero_pagina = $request->numero_pagina;
            $certificado->save();

            DB::commit();

            return response()->json([
                'message' => 'Los datos del certificado fueron actualizados exitosamente.',
                'curso' => $certificado->curso_id,
            ]);


        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.certificados_generados', $certificado->curso_id)->with('error-message', $e->getMessage());
        }
    }
}
