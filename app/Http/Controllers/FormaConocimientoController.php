<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\FormaConocimiento;


class FormaConocimientoController extends Controller
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
        $this->authorize('ver_formas_conocimientos');

        return view('formas_conocimientos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_formas_conocimientos');

        try {
            $formas_conocimientos = FormaConocimiento::orderBy('id', 'asc')->get();
            return response()->json([
                'formas_conocimientos' => $formas_conocimientos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_formas_conocimientos');

        try {
            $forma_conocimiento = FormaConocimiento::findOrFail($id);
            return response()->json([
                'forma_conocimiento' => $forma_conocimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_formas_conocimientos');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('formas_conocimientos')]
            ]);

            try {
                $forma_conocimiento = new FormaConocimiento();
                $forma_conocimiento->nombre = removeAccents(Str::upper($request->nombre));
                $forma_conocimiento->save();

                DB::commit();

                $formas_conocimientos = FormaConocimiento::get();
                return response()->json([
                    'message' => 'La forma de conocimiento ' . $forma_conocimiento->nombre . ' fue creada exitosamente.',
                    'formas_conocimientos' => $formas_conocimientos,
                    'selected' => $forma_conocimiento,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_formas_conocimientos');

        try {
            $forma_conocimiento = FormaConocimiento::findOrFail($id);
            return response()->json([
                'forma_conocimiento' => $forma_conocimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_formas_conocimientos');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('formas_conocimientos')->ignore($id)]
        ]);

        try {
            $forma_conocimiento = FormaConocimiento::findOrFail($id);
            $forma_conocimiento->nombre = removeAccents(Str::upper($request->nombre));
            $forma_conocimiento->save();

            DB::commit();

            $formas_conocimientos = FormaConocimiento::get();
            return response()->json([
                'message' => 'La forma de conocimiento ' . $forma_conocimiento->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_formas_conocimientos');

        try {
            $forma_conocimiento = FormaConocimiento::findOrFail($id);
            return response()->json([
                'forma_conocimiento' => $forma_conocimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_formas_conocimientos');

        DB::beginTransaction();

        try {
            $forma_conocimiento = FormaConocimiento::findOrFail($id);
            $forma_conocimiento->delete();

            DB::commit();

            return response()->json([
                'message' => 'La forma de conocimiento ' . $forma_conocimiento->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('formas_conocimientos.index')->with('error-message', 'La forma de conocimiento ' . $forma_conocimiento->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('formas_conocimientos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
