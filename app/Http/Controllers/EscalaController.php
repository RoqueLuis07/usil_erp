<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Rules\PuntajesSolapados;

use App\Models\Escala;
use App\Models\EscalaDetalle;
use App\Models\Programa;


class EscalaController extends Controller
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
        $this->authorize('ver_escalas');

        try {
            $escalas = Escala::orderBy('nombre', 'asc')->get();
            return view('escalas/index')->with(compact('escalas'));
        } catch (\Exception $e) {
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_escalas');

        try {
            $escala = Escala::with(['escalaDetalles' => function ($query) {
                $query->orderBy('punto_minimo', 'desc');
            }])->findOrFail($id);
            return view('escalas/show')->with(compact('escala'));
        } catch (\Exception $e) {
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_escalas');

        try {
            $programas = Programa::where('estado', 'AC')->get();
            return view('escalas/create')->with(compact('programas'));
        } catch (\Exception $e) {
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_escalas');

        $request->validate([
            'nombre' => ['required', Rule::unique('escalas')],
            'detalles' => ['required', 'array', new PuntajesSolapados],
            'detalles.*.punto_minimo' => ['required', 'numeric', 'max:100'],
            'detalles.*.punto_maximo' => ['required', 'numeric', 'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $punto_minimo = $request->input("detalles.$index.punto_minimo");

                    if ($value <= $punto_minimo) {
                        $fail('El :attribute debe ser mayor al puntaje mínimo.');
                    }
                }],
            'detalles.*.nota' => ['required',
                function ($attribute, $value, $fail) use ($request) {
                    if (!is_numeric($value) && !is_string($value)) {
                        $fail('El :attribute debe ser un número o una cadena.');
                    }
                },
                function ($attribute, $value, $fail) use ($request) {
                    if (is_numeric($value)) {
                        if ($value < 1 || $value > 5) {
                            $fail('El :attribute debe estar entre 1 y 5.');
                        }
                    } else if (is_string($value)) {
                        if (strlen($value) < 1 || strlen($value) > 2) {
                            $fail ('El :attribute debe tener entre 1 y 2 caracteres.');
                        }
                    }
                },
            function ($attribute, $value, $fail) use ($request) {
                $notas = array_column($request->input('detalles'), 'nota');
                $notas = array_filter($notas, function($nota) {
                    return is_string($nota) || is_int($nota);
                });
                $notaCounts = array_count_values($notas);
                if ($notaCounts[$value] > 1) {
                    $fail ('La :attribute debe ser única.');
                }
            }],
        ]);

        DB::beginTransaction();

        try {
            $escala = new Escala();
            $escala->nombre = removeAccents(Str::upper($request->nombre));
            $escala->programa_id = $request->programa;
            $escala->cargado_por_id = Auth::id();
            $escala->save();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $escala_detalle = new EscalaDetalle();
                $escala_detalle->escala_id = $escala->id;
                $escala_detalle->punto_minimo = $detalle['punto_minimo'];
                $escala_detalle->punto_maximo = $detalle['punto_maximo'];
                $escala_detalle->nota = removeAccents(Str::upper($detalle['nota']));
                $escala_detalle->save();
            }

            DB::commit();

            return redirect()->route('escalas.index')->with('success-message', 'La escala ' . $escala->nombre . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_escalas');

        try {
            $escala = Escala::with(['escalaDetalles' => function ($query) {
                $query->orderBy('nota');
            }])->findOrFail($id);
            $programas = Programa::where('estado', 'AC')->get();
            return view('escalas/edit')->with(compact('escala', 'programas'));
        } catch (\Exception $e) {
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_escalas');

        $request->validate([
            'nombre' => ['required', Rule::unique('escalas')->ignore($id)],
            'detalles' => ['required', 'array', new PuntajesSolapados],
            'detalles.*.punto_minimo' => ['required', 'numeric', 'max:100'],
            'detalles.*.punto_maximo' => ['required', 'numeric', 'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $punto_minimo = $request->input("detalles.$index.punto_minimo");

                    if ($value <= $punto_minimo) {
                        $fail('El :attribute debe ser mayor al puntaje mínimo.');
                    }
                }],
            'detalles.*.nota' => ['required',
                function ($attribute, $value, $fail) use ($request) {
                    if (!is_numeric($value) && !is_string($value)) {
                        $fail('El :attribute debe ser un número o una cadena.');
                    }
                },
                function ($attribute, $value, $fail) use ($request) {
                    if (is_numeric($value)) {
                        if ($value < 1 || $value > 5) {
                            $fail('El :attribute debe estar entre 1 y 5.');
                        }
                    } else if (is_string($value)) {
                        if (strlen($value) < 1 || strlen($value) > 2) {
                            $fail ('El :attribute debe tener entre 1 y 2 caracteres.');
                        }
                    }
                },
            function ($attribute, $value, $fail) use ($request) {
                $notas = array_column($request->input('detalles'), 'nota');
                $notas = array_filter($notas, function($nota) {
                    return is_string($nota) || is_int($nota);
                });
                $notaCounts = array_count_values($notas);
                if ($notaCounts[$value] > 1) {
                    $fail ('La :attribute debe ser única.');
                }
            }],
        ]);

        DB::beginTransaction();

        try {
            $escala = Escala::findOrFail($id);
            $escala->nombre = removeAccents(Str::upper($request->nombre));
            $escala->programa_id = $request->programa;
            $escala->actualizado_por_id = Auth::id();
            $escala->save();

            //obtener el detalle de la escala y eliminar lo que habia para poder crear de vuelta
            $escala_detalles = EscalaDetalle::where('escala_id', $id)->delete();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $escala_detalle = new EscalaDetalle();
                $escala_detalle->escala_id = $escala->id;
                $escala_detalle->punto_minimo = $detalle['punto_minimo'];
                $escala_detalle->punto_maximo = $detalle['punto_maximo'];
                $escala_detalle->nota = removeAccents(Str::upper($detalle['nota']));
                $escala_detalle->save();
            }

            DB::commit();

            return redirect()->route('escalas.index')->with('success-message', 'La escala ' . $escala->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_escalas');

        DB::beginTransaction();

        try {
            $escala = Escala::findOrFail($id);
            $escala->actualizado_por_id = Auth::id();
            $escala->estado = 'IN';
            $escala->save();

            DB::commit();

            return redirect()->route('escalas.index')->with('error-message','La escala ' . $escala->nombre . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_escalas');

        DB::beginTransaction();

        try {
            $escala = Escala::findOrFail($id);
            $escala->actualizado_por_id = Auth::id();
            $escala->estado = 'AC';
            $escala->save();

            DB::commit();

            return redirect()->route('escalas.index')->with('success-message','La escala ' . $escala->nombre . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_escalas');

        DB::beginTransaction();

        try {
            $escala = Escala::findOrFail($id);

            $escala_detalles = EscalaDetalle::where('escala_id', $escala->id)->delete();

            $escala->delete();

            DB::commit();

            return redirect()->route('escalas.index')->with('success-message','La escala ' . $escala->nombre . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('escalas.index')->with('error-message', 'La escala ' . $escala->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('escalas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
