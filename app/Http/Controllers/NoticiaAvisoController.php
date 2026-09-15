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

use App\Models\NoticiaAviso;

class NoticiaAvisoController extends Controller
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
        $this->authorize('ver_noticias_avisos');

        try {
            $noticias_avisos = NoticiaAviso::orderBy('fecha_hora_publicacion', 'desc')->orderBy('destacado', 'desc')->get();

            return view('noticias_avisos/index')->with(compact('noticias_avisos'));
        } catch (\Exception $e) {
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_noticias_avisos');

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);

            return view('noticias_avisos/show')->with(compact('noticia_aviso'));
        } catch (\Exception $e) {
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
         $this->authorize('crear_noticias_avisos');

        try {
            return view('noticias_avisos/create');
        } catch (\Exception $e) {
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
         $this->authorize('crear_noticias_avisos');

        $request->validate([
            'fecha_hora_publicacion' => ['required', 'date'],
            'titulo' => 'required',
            'tipo' => ['required', 'min:2', 'max:2'],
            'destacado' => ['required'],
            'portada' => ['nullable', 'file', 'extensions:jpg,png'],
            'descripcion' => ['required']
        ]);

        DB::beginTransaction();

        try {
            $noticia_aviso = new NoticiaAviso();
            $noticia_aviso->fecha_hora_publicacion = $request->fecha_hora_publicacion;
            $noticia_aviso->tipo = $request->tipo;
            $noticia_aviso->titulo = removeAccents(Str::upper($request->titulo));
            $noticia_aviso->descripcion = $request->descripcion;
            $noticia_aviso->destacado = $request->destacado;

            //cargar archivo
            if ($request->portada) {
                $archivo = $request->portada;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/noticias_avisos';
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = str_replace(' ', '_', removeAccents(Str::lower($request->titulo)));
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
                $noticia_aviso->portada = $directorio . '/' . $nombre_archivo;
            } else {
                $noticia_aviso->portada = 'storage/no_image.png';
            }
            //fin cargar archivo

            $noticia_aviso->cargado_por_id = Auth::id();
            if (Carbon::now() >= $request->fecha_hora_publicacion) {
                $noticia_aviso->estado = 'PU';
            }
            $noticia_aviso->save();

            if ($request->tipo == 'NO') {
                $tipo = 'La noticia';
                $tipo_creacion = 'creada';
            } else {
                $tipo = 'El aviso';
                $tipo_creacion = 'creado';
            }

            DB::commit();

            return redirect()->route('noticias_avisos.index')->with('success-message', $tipo . ' ' . $noticia_aviso->titulo . ' fue ' . $tipo_creacion . ' existosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
         $this->authorize('editar_noticias_avisos');

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            return view('noticias_avisos/edit')->with(compact('noticia_aviso'));
        } catch (\Exception $e) {
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
         $this->authorize('editar_noticias_avisos');

        $request->validate([
            'fecha_hora_publicacion' => ['required', 'date'],
            'fecha_cambiada' => 'required',
            'titulo' => 'required',
            'tipo' => ['required', 'min:2', 'max:2'],
            'destacado' => ['required'],
            'portada' => ['nullable', 'file', 'extensions:jpg,png'],
            'descripcion' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            if ($request->fecha_cambiada == 'SI') {
                $noticia_aviso->fecha_hora_publicacion = $request->fecha_hora_publicacion;
                if (Carbon::now() >= $request->fecha_hora_publicacion) {
                    $noticia_aviso->estado = 'PU';
                } else {
                    $noticia_aviso->estado = 'ES';
                }
            }
            $noticia_aviso->tipo = $request->tipo;
            $noticia_aviso->titulo = removeAccents(Str::upper($request->titulo));
            $noticia_aviso->descripcion = $request->descripcion;
            $noticia_aviso->destacado = $request->destacado;

            //cargar archivo
            if ($request->portada) {
                $archivo = $request->portada;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/noticias_avisos';
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = str_replace(' ', '_', removeAccents(Str::lower($request->titulo)));
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
                $noticia_aviso->portada = $directorio . '/' . $nombre_archivo;
            }
            //fin cargar archivo

            $noticia_aviso->cargado_por_id = Auth::id();
            $noticia_aviso->save();

            if ($request->tipo == 'NO') {
                $tipo = 'La noticia';
                $tipo_creacion = 'creada';
            } else {
                $tipo = 'El aviso';
                $tipo_creacion = 'creado';
            }

            DB::commit();

            return redirect()->route('noticias_avisos.index')->with('success-message', $tipo . ' ' . $noticia_aviso->titulo . ' fue ' . $tipo_creacion . ' existosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_noticias avisos');

        DB::beginTransaction();

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            if ($noticia_aviso->tipo == 'NO') {
                $tipo = 'La noticia';
            } else {
                $tipo = 'El aviso';
            }
            $noticia_aviso->estado = 'NP';
            $noticia_aviso->save();

            DB::commit();

            return redirect()->route('noticias_avisos.index')->with('error-message', $tipo . ' ' . $noticia_aviso->titulo . ' se dejó de publicar existosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_noticias_avisos');

        DB::beginTransaction();

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            if ($noticia_aviso->tipo == 'NO') {
                $tipo = 'La noticia';
            } else {
                $tipo = 'El aviso';
            }
            if (Carbon::now() >= $noticia_aviso->fecha_hora_publicacion) {
                $noticia_aviso->estado = 'PU';
            } else {
                $noticia_aviso->estado = 'ES';
            }
            $noticia_aviso->save();

            DB::commit();

            return redirect()->route('noticias_avisos.index')->with('success-message', $tipo . ' ' . $noticia_aviso->titulo . ' se publicó existosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_noticias_avisos');

        DB::beginTransaction();

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            if ($noticia_aviso->tipo == 'NO') {
                $tipo = 'La noticia';
                $tipo_eliminacion = 'eliminada';
            } else {
                $tipo = 'El aviso';
                $tipo_eliminacion = 'eliminado';
            }
            $noticia_aviso->delete();

            DB::commit();

            return redirect()->route('noticias_avisos.index')->with('error-message', $tipo . ' ' . $noticia_aviso->titulo . ' fue ' . $tipo_eliminacion . ' existosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('noticias_avisos.index')->with('error-message', $tipo . ' ' . $noticia_aviso->titulo . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function destroy_portada($id)
    {
        $this->authorize('eliminar_portadas_noticias_avisos');

        DB::beginTransaction();

        try {
            $noticia_aviso = NoticiaAviso::findOrFail($id);
            $ubicacion_archivo = $noticia_aviso->portada;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $noticia_aviso->portada = 'storage/no_image.png';
            $noticia_aviso->actualizado_por_id = Auth::id();
            $noticia_aviso->save();

            if ($noticia_aviso->tipo == 'NO') {
                $tipo = 'La noticia';
            } else {
                $tipo = 'El aviso';
            }

            DB::commit();

            return response()->json([
                'message' => 'La portada de ' . $tipo . ' ' . $noticia_aviso->titulo . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('noticias_avisos.index')->with('error-message', $e->getMessage());
        }
    }
}
