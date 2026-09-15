<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Timbrado;
use App\Models\User;


class TimbradoController extends Controller
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
        $this->authorize('ver_timbrados');

        return view('timbrados.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_timbrados');

        try {
            $timbrados = Timbrado::orderBy('valido_hasta', 'desc')->get();
            foreach ($timbrados as $timbrado) {
                $timbrado->valido_desde = Carbon::parse($timbrado->valido_desde)->format('d/m/Y');
                $timbrado->valido_hasta = Carbon::parse($timbrado->valido_hasta)->format('d/m/Y');
            }

            return response()->json([
                'timbrados' => $timbrados,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_timbrados');

        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->valido_desde = Carbon::parse($timbrado->valido_desde)->format('d/m/Y');
            $timbrado->valido_hasta = Carbon::parse($timbrado->valido_hasta)->format('d/m/Y');
            $cargado = User::findOrFail($timbrado->cargado_por_id);
            $created_at = Carbon::parse($timbrado->created_at)->format('d/m/Y H:i:s');
            if ($timbrado->actualizado_por_id) {
                $actualizado = User::findOrFail($timbrado->actualizado_por_id);
                $updated_at = Carbon::parse($timbrado->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'timbrado' => $timbrado,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_timbrados');

        DB::beginTransaction();

            $request->validate([
                'numero' => ['required', Rule::unique('timbrados')],
                'valido_desde' => ['required', 'date'],
                'valido_hasta' => ['required', 'date', 'after:valido_desde'],
            ]);

            try {
                $timbrado = new Timbrado();
                $timbrado->numero = $request->numero;
                $timbrado->valido_desde = $request->valido_desde;
                $timbrado->valido_hasta = $request->valido_hasta;
                $timbrado->cargado_por_id = Auth::id();
                $timbrado->save();

                DB::commit();

                $timbrados = Timbrado::get();
                return response()->json([
                    'message' => 'El timbrado ' . $timbrado->numero . ' fue creado exitosamente.',
                    'timbrados' => $timbrados,
                    'selected' => $timbrado,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_timbrados');

        try {
            $timbrado = Timbrado::findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_timbrados');

        DB::beginTransaction();

        $request->validate([
            'numero' => ['required', Rule::unique('timbrados')->ignore($id)],
            'valido_desde' => ['required', 'date'],
            'valido_hasta' => ['required', 'date', 'after:valido_desde'],
        ]);

        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->numero = $request->numero;
            $timbrado->valido_desde = $request->valido_desde;
            $timbrado->valido_hasta = $request->valido_hasta;
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->save();

            DB::commit();

            $timbrados = Timbrado::get();
            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_timbrados');

        try {
            $timbrado = Timbrado::findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_timbrados');

        DB::beginTransaction();

        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->estado = 'IN';
            $timbrado->save();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_timbrados');

        try {
            $timbrado = Timbrado::findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_timbrados');

        DB::beginTransaction();

        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->estado = 'AC';
            $timbrado->save();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_timbrados');

        try {
            $timbrado = Timbrado::findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_timbrados');

        DB::beginTransaction();

        try {
            $timbrado = Timbrado::findOrFail($id);
            $timbrado->delete();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('timbrados.index')->with('error-message', 'El timbrado ' . $timbrado->numero . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('timbrados.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
