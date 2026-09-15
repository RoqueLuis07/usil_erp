<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\InstitucionEducativa;


class InstitucionEducativaController extends Controller
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
        $this->authorize('ver_instituciones_educativas');

        return view('instituciones_educativas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_instituciones_educativas');

        try {
            $instituciones_educativas = InstitucionEducativa::orderBy('nombre', 'asc')->get();
            return response()->json([
                'instituciones_educativas' => $instituciones_educativas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_instituciones_educativas');

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            return response()->json([
                'institucion_educativa' => $institucion_educativa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_instituciones_educativas');

        DB::beginTransaction();

            $request->validate([
                'nombre_institucion_educativa' => ['required', Rule::unique('instituciones_educativas', 'nombre')],
                'tipo_institucion_educativa' => 'required',
            ]);

            try {
                $institucion_educativa = new InstitucionEducativa();
                $institucion_educativa->nombre = removeAccents(Str::upper($request->nombre_institucion_educativa));
                $institucion_educativa->tipo = $request->tipo_institucion_educativa;
                $institucion_educativa->save();

                DB::commit();

                $instituciones_educativas = InstitucionEducativa::get();
                return response()->json([
                    'message' => 'La institución educativa ' . $institucion_educativa->nombre . ' fue creada exitosamente.',
                    'instituciones_educativas' => $instituciones_educativas,
                    'selected' => $institucion_educativa,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_instituciones_educativas');

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            return response()->json([
                'institucion_educativa' => $institucion_educativa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_instituciones_educativas');

        DB::beginTransaction();

        $request->validate([
            'nombre_institucion_educativa' => ['required', Rule::unique('instituciones_educativas', 'nombre')->ignore($id)],
            'tipo_institucion_educativa' => 'required',
        ]);

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            $institucion_educativa->nombre = removeAccents(Str::upper($request->nombre_institucion_educativa));
            $institucion_educativa->tipo = $request->tipo_institucion_educativa;
            $institucion_educativa->save();

            DB::commit();

            $instituciones_educativas = InstitucionEducativa::get();
            return response()->json([
                'message' => 'La institución educativa ' . $institucion_educativa->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_instituciones_educativas');

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            return response()->json([
                'institucion_educativa' => $institucion_educativa,
            ]);
        } catch (\Exception $e) {
            return redirect('institucion_educativas')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_instituciones_educativas');

        DB::beginTransaction();

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            $institucion_educativa->estado = 'IN';
            $institucion_educativa->save();

            DB::commit();

            return response()->json([
                'message' => 'La institución educativa ' . $institucion_educativa->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('institucion_educativas')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_instituciones_educativas');

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            return response()->json([
                'institucion_educativa' => $institucion_educativa,
            ]);
        } catch (\Exception $e) {
            return redirect('institucion_educativas')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_instituciones_educativas');

        DB::beginTransaction();

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            $institucion_educativa->estado = 'AC';
            $institucion_educativa->save();

            DB::commit();

            return response()->json([
                'message' => 'La institución educativa ' . $institucion_educativa->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('institucion_educativas')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar instituciones_educativas');

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            return response()->json([
                'institucion_educativa' => $institucion_educativa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_instituciones_educativas');

        DB::beginTransaction();

        try {
            $institucion_educativa = InstitucionEducativa::findOrFail($id);
            $institucion_educativa->delete();

            DB::commit();

            return response()->json([
                'message' => 'La institución educativa ' . $institucion_educativa->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('instituciones_educativas.index')->with('error-message', 'La institución educativa ' . $institucion_educativa->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('instituciones_educativas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
