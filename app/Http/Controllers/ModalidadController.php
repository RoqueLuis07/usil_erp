<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Modalidad;


class ModalidadController extends Controller
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
        $this->authorize('ver_modalidades');

        return view('modalidades.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_modalidades');

        try {
            $modalidades = Modalidad::orderBy('nombre', 'asc')->get();
            return response()->json([
                'modalidades' => $modalidades,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_modalidades');

        try {
            $modalidad = Modalidad::findOrFail($id);
            return response()->json([
                'modalidad' => $modalidad,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_modalidades');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('modalidades')],
            ]);

            try {
                $modalidad = new Modalidad();
                $modalidad->nombre = removeAccents(Str::upper($request->nombre));
                $modalidad->save();

                DB::commit();

                $modalidades = Modalidad::get();
                return response()->json([
                    'message' => 'La modalidad ' . $modalidad->nombre . ' fue creada exitosamente.',
                    'modalidades' => $modalidades,
                    'selected' => $modalidad,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
               return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_modalidades');

        try {
            $modalidad = Modalidad::findOrFail($id);
            return response()->json([
                'modalidad' => $modalidad,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_modalidades');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('modalidades')->ignore($id)],
        ]);

        try {
            $modalidad = Modalidad::findOrFail($id);
            $modalidad->nombre = removeAccents(Str::upper($request->nombre));
            $modalidad->carga_horaria = $request->carga_horaria;
            $modalidad->save();

            DB::commit();

            $modalidades = Modalidad::get();
            return response()->json([
                'message' => 'La modalidad ' . $modalidad->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_modalidades');

        try {
            $modalidad = Modalidad::findOrFail($id);
            return response()->json([
                'modalidad' => $modalidad,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_modalidades');

        DB::beginTransaction();

        try {
            $modalidad = Modalidad::findOrFail($id);
            $modalidad->estado = 'IN';
            $modalidad->save();

            DB::commit();

            return response()->json([
                'message' => 'La modalidad ' . $modalidad->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_modalidades');

        try {
            $modalidad = Modalidad::findOrFail($id);
            return response()->json([
                'modalidad' => $modalidad,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_modalidades');

        DB::beginTransaction();

        try {
            $modalidad = Modalidad::findOrFail($id);
            $modalidad->estado = 'AC';
            $modalidad->save();

            DB::commit();

            return response()->json([
                'message' => 'La modalidad ' . $modalidad->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_modalidades');

        try {
            $modalidad = Modalidad::findOrFail($id);
            return response()->json([
                'modalidad' => $modalidad,
            ]);
        } catch (\Exception $e) {
           return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_modalidades');

        DB::beginTransaction();

        try {
            $modalidad = Modalidad::findOrFail($id);
            $modalidad->delete();

            DB::commit();

            return response()->json([
                'message' => 'La modalidad ' . $modalidad->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
               return redirect()->route('modalidades.index')->with('error-message', 'La modalidad ' . $modalidad->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
               return redirect()->route('modalidades.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
