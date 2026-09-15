<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Modulo;


class ModuloMaestriaController extends Controller
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
        $this->authorize('ver_modulos_maestrias_ubs');

        return view('ubs/modulos_maestrias.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_modulos_maestrias_ubs');

        try {
            $modulos = Modulo::with('correlativas')->where('es_maestria', true)->orderBy('nombre_fantasia', 'asc')->get();
            return response()->json([
                'modulos' => $modulos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_modulos_maestrias_ubs');

        DB::beginTransaction();

            $request->validate([
                'nombre_fantasia' => 'required',
                'nombre_real' => 'required',
                'codigo' => 'nullable',
                'carga_horaria' => ['required', 'numeric']
            ]);

            try {
                $modulo = new Modulo();
                $modulo->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
                $modulo->nombre_real = removeAccents(Str::upper($request->nombre_real));
                $modulo->codigo = removeAccents(Str::upper($request->codigo));
                $modulo->carga_horaria = removeAccents(Str::upper($request->carga_horaria));
                $modulo->es_maestria = true;
                $modulo->cargado_por_id = Auth::id();
                $modulo->save();

                DB::commit();

                $modulos = Modulo::get();
                return response()->json([
                    'message' => 'El módulo ' . $modulo->nombre_fantasia . ' fue creado exitosamente.',
                    'modulos' => $modulos,
                    'selected' => $modulo,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_modulos_maestrias_ubs');

        DB::beginTransaction();

        $request->validate([
            'nombre_fantasia' => 'required',
            'nombre_real' => 'required',
            'codigo' => 'nullable',
            'carga_horaria' => ['required', 'numeric']
        ]);

        try {
            $modulo = Modulo::findOrFail($id);
            $modulo->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $modulo->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $modulo->codigo = removeAccents(Str::upper($request->codigo));
            $modulo->carga_horaria = removeAccents(Str::upper($request->carga_horaria));
            $modulo->es_maestria = true;
            $modulo->actualizado_por_id = Auth::id();
            $modulo->save();

            DB::commit();

            $modulos = Modulo::get();
            return response()->json([
                'message' => 'El módulo ' . $modulo->nombre_fantasia . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_modulos_maestrias_ubs');

        DB::beginTransaction();

        try {
            $modulo = Modulo::findOrFail($id);
            $modulo->actualizado_por_id = Auth::id();
            $modulo->estado = 'IN';
            $modulo->save();

            DB::commit();

            return response()->json([
                'message' => 'El módulo ' . $modulo->nombre_fantasia . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_modulos_maestrias_ubs');

        DB::beginTransaction();

        try {
            $modulo = Modulo::findOrFail($id);
            $modulo->actualizado_por_id = Auth::id();
            $modulo->estado = 'AC';
            $modulo->save();

            DB::commit();

            return response()->json([
                'message' => 'El módulo ' . $modulo->nombre_fantasia . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_modulos_maestrias_ubs');

        DB::beginTransaction();

        try {
            $modulo = Modulo::findOrFail($id);
            $modulo->delete();

            DB::commit();

            return response()->json([
                'message' => 'El módulo ' . $modulo->nombre_fantasia . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('modulos_maestrias.index')->with('error-message', 'El módulo ' . $modulo->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
