<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Materia;
use App\Models\User;
use App\Models\Correlatividad;


class MateriaController extends Controller
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
        $this->authorize('ver_materias');

        return view('materias.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_materias');

        try {
            $materias = Materia::with('correlativas')->orderBy('nombre_fantasia', 'asc')->get();
            return response()->json([
                'materias' => $materias,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('ver_materias');

        try {
            $materia = Materia::findOrFail($id);
            $cargado = User::findOrFail($materia->cargado_por_id);
            $created_at = Carbon::parse($materia->created_at)->format('d/m/Y H:i:s');
            if ($materia->actualizado_por_id) {
                $actualizado = User::findOrFail($materia->actualizado_por_id);
                $updated_at = Carbon::parse($materia->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'materia' => $materia,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_materias');

        DB::beginTransaction();

            $request->validate([
                'nombre_fantasia' => ['required', Rule::unique('materias')],
                'nombre_real' => ['required'],
                'carga_horaria' => ['required', 'numeric'],
                'codigo' => ['nullable'],
            ]);

            try {
                $materia = new Materia();
                $materia->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
                $materia->nombre_real = removeAccents(Str::upper($request->nombre_real));
                $materia->carga_horaria = $request->carga_horaria;
                $materia->codigo = removeAccents(Str::upper($request->codigo));
                $materia->cargado_por_id = Auth::id();
                $materia->save();

                $correlatividad = New Correlatividad();
                $correlatividad->materia_id = $materia->id;
                $correlatividad->save();

                DB::commit();

                $materias = Materia::get();
                return response()->json([
                    'message' => 'La materia ' . $materia->nombre_fantasia . ' fue creada exitosamente.',
                    'materias' => $materias,
                    'selected' => $materia,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_materias');

        try {
            $materia = Materia::findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_materias');

        DB::beginTransaction();

        $request->validate([
            'nombre_fantasia' => ['required', Rule::unique('materias')->ignore($id)],
            'nombre_real' => ['required'],
            'carga_horaria' => ['required', 'numeric'],
            'codigo' => ['nullable'],
        ]);

        try {
            $materia = Materia::findOrFail($id);
            $materia->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $materia->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $materia->carga_horaria = $request->carga_horaria;
            $materia->codigo = removeAccents(Str::upper($request->codigo));
            $materia->actualizado_por_id = Auth::id();
            $materia->save();

            DB::commit();

            $materias = Materia::get();
            return response()->json([
                'message' => 'La materia ' . $materia->nombre_fantasia . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_materias');

        try {
            $materia = Materia::findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_materias');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($id);
            $materia->actualizado_por_id = Auth::id();
            $materia->estado = 'IN';
            $materia->save();

            DB::commit();

            return response()->json([
                'message' => 'La materia ' . $materia->nombre_fantasia . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_materias');

        try {
            $materia = Materia::findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_materias');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($id);
            $materia->actualizado_por_id = Auth::id();
            $materia->estado = 'AC';
            $materia->save();

            DB::commit();

            return response()->json([
                'message' => 'La materia ' . $materia->nombre_fantasia . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_materias');

        try {
            $materia = Materia::findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_materias');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($id);

            $correlatividad = Correlatividad::where('materia_id', $materia->id)->delete();

            $materia->delete();

            DB::commit();

            return response()->json([
                'message' => 'La materia ' . $materia->nombre_fantasia . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return response()->json([
                    'message' => 'La materia ' . $materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.',
                ]);
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
