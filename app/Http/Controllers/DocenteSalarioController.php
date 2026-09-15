<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\DocenteSalario;

class DocenteSalarioController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_salarios_docentes');

        try {
            $buscar = Str::upper($request->buscar);

            $salarios = DocenteSalario::orderBy('fecha_inicio', 'desc')->paginate(50);

            if (!(blank($buscar))) {
				$salarios = DocenteSalario::orWhere('id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                ->orWhereHas('docente', function ($query) use ($buscar) {
                    $query->where('primer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                        ->orWhere('segundo_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                        ->orWhere('tercer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                        ->orWhere('primer_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                        ->orWhere('segundo_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                        ->orWhere('numero_documento', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                })->orWhere('fecha_inicio', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                    ->orWhere('fecha_fin', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
				->paginate(50);
			}

            return view('docentes/salarios/index')->with(compact('salarios', 'buscar'));
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_salarios_docentes');

        DB::beginTransaction();

        try {
            $salario = DocenteSalario::findOrFail($id);
            $salario->rechazado_por_id = Auth::id();
            $salario->estado = 'RE';
            $salario->save();

            DB::commit();

            return redirect()->route('docentes_salarios.index')->with('error-message','El salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes_salarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_salarios_docentes');

        DB::beginTransaction();

        try {
            $salario = DocenteSalario::findOrFail($id);
            $salario->rechazado_por_id = null;
            $salario->estado = 'PE';
            $salario->save();

            DB::commit();

            return redirect()->route('docentes_salarios.index')->with('success-message','El rechazo del salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes_salarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_salarios_docentes');

        DB::beginTransaction();

        try {
            $salario = DocenteSalario::findOrFail($id);
            $salario->aprobado_por_id = Auth::id();
            $salario->estado = 'AP';
            $salario->save();

            DB::commit();

            return redirect()->route('docentes_salarios.index')->with('success-message','El salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes_salarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_salarios_docentes');

        DB::beginTransaction();

        try {
            $salario = DocenteSalario::findOrFail($id);
            $salario->aprobado_por_id = null;
            $salario->estado = 'PE';
            $salario->save();

            DB::commit();

            return redirect()->route('docentes_salarios.index')->with('error-message','La aprobación del salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('docentes_salarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_salarios_docentes');

        DB::beginTransaction();

        try {
            $salario = DocenteSalario::findOrFail($id);
            $ubicacion_archivo = str_replace('storage', 'public', $salario->url_ubicacion);
            Storage::delete($ubicacion_archivo);

            $salario->delete();

            DB::commit();

            return redirect()->route('docentes_salarios.index')->with('success-message','El salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('docentes_salarios.index')->with('error-message', 'El salario del docente ' . $salario->docente->primer_nombre . ' ' . $salario->docente->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('docentes_salarios.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
