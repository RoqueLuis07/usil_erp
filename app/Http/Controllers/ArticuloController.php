<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Articulo;
use App\Models\ArticuloDetalle;
use App\Models\CuentaContable;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\CursoPrecio;
use App\Models\UnidadNegocioContable;
use App\Models\SubunidadNegocioContable;
use App\Models\CentroCostoContable;
use App\Models\SubcentroCostoContable;

class ArticuloController extends Controller
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
        $this->authorize('ver_articulos');

        try {
            $articulos = Articulo::orderBy('id', 'desc')->get();

            return view('articulos/index')->with(compact('articulos'));
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_articulos');

        try {
            $articulo = Articulo::findOrFail($id);
            return view('articulos/show')->with(compact('articulo'));
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_articulos');

        try {
            $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();
            $carreras = Carrera::where('estado', 'AC')->get();
            $cursos = Curso::where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();
            return view('articulos/create')->with(compact('cuentas_contables', 'carreras', 'cursos', 'unidades_negocios', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_articulos');

        $request->validate([
            'codigo' => ['required', Rule::unique('articulos')],
            'nombre' => ['required', Rule::unique('articulos')],
            'impuesto' => ['required', 'numeric'],
            'compra_venta' => 'required',
            'cuenta_compra' => ['nullable', 'numeric', 'required_if:compra_venta,compra'],
            'stock' => 'required',
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],
            'centro_costo' => ['required', 'numeric'],
            'subcentro_costo' => ['required', 'numeric'],
            'carrera_curso' => 'nullable',
            'carrera' => ['nullable', 'numeric', 'required_if:carrera_curso,carrera'],
            'curso' => ['nullable', 'numeric', 'required_if:carrera_curso,curso'],

            'precio_matricula' => ['nullable', 'numeric', 'min:1'],
            'cuenta_matricula' => ['nullable', 'numeric'],
            'precio_contado' => ['nullable', 'numeric', 'min:1'],
            'cuenta_contado' => ['nullable', 'numeric'],
            'precio_cuota' => ['nullable', 'numeric', 'min:1'],
            'cuenta_cuota' => ['nullable', 'numeric'],
            'cuenta_descuento' => ['nullable', 'numeric'],
            'precio_defensa' => ['nullable', 'numeric', 'min:1'],
            'cuenta_defensa' => ['nullable', 'numeric'],
            'precio_titulo' => ['nullable', 'numeric', 'min:1'],
            'cuenta_titulo' => ['nullable', 'numeric'],
            'precio_multa' => ['nullable', 'numeric', 'min:0'],
            'cuenta_multa' => ['nullable', 'numeric'],
            'precio_certificado' => ['nullable', 'numeric', 'min:1'],
            'cuenta_certificado' => ['nullable', 'numeric'],
            'precio_examen_suficiencia' => ['nullable', 'numeric', 'min:1'],
            'cuenta_examen_suficiencia' => ['nullable', 'numeric'],
            'precio_constancia_carrera' => ['nullable', 'numeric', 'min:1'],
            'cuenta_constancia_carrera' => ['nullable', 'numeric'],
            'cantidad_cuotas' => ['nullable', 'numeric', 'min:2', 'max:24'],
            'fecha_vencimiento_primera_cuota' => ['nullable', 'date'],
            'dia_vencimiento_cuotas' => ['nullable', 'numeric', 'min:1', 'max:28'],
            'dias_gracia' => ['nullable', 'numeric', 'min:0', 'max:28'],

        ]);

        DB::beginTransaction();

        try {
            $articulo = new Articulo();
            $articulo->codigo = removeAccents(Str::upper($request->codigo));
            $articulo->nombre = removeAccents(Str::upper($request->nombre));
            $articulo->impuesto = $request->impuesto;
            $articulo->compra_venta = Str::upper($request->compra_venta);
            $articulo->tiene_stock = $request->stock;
            if ($articulo->tiene_stock == true) {
                $articulo->stock = 0;
            }
            $articulo->unidad_id = $request->unidad_negocio;
            $articulo->subunidad_id = $request->subunidad_negocio;
            $articulo->centro_costo_id = $request->centro_costo;
            $articulo->subcentro_costo_id = $request->subcentro_costo;
            $articulo->cargado_por_id = Auth::id();
            if ($request->compra_venta == 'compra') {
                $articulo->save();

                $detalle = new ArticuloDetalle();
                $detalle->articulo_id = $articulo->id;
                $detalle->cuenta_compra_id = $request->cuenta_compra;
                $detalle->save();
            } elseif ($request->compra_venta == 'venta') {
                $articulo->carrera_curso = Str::upper($request->carrera_curso);
                $articulo->save();

                $detalle = new ArticuloDetalle();
                $detalle->articulo_id = $articulo->id;
                $detalle->cantidad_cuotas = $request->cantidad_cuotas;
                $detalle->precio_matricula = $request->precio_matricula;
                $detalle->cuenta_matricula_id = $request->cuenta_matricula;
                $detalle->precio_contado = $request->precio_contado;
                $detalle->cuenta_contado_id = $request->cuenta_contado;
                $detalle->precio_cuota = $request->precio_cuota;
                $detalle->cuenta_cuota_id = $request->cuenta_cuota;
                $detalle->cuenta_descuento_id = $request->cuenta_descuento;
                if ($request->precio_multa) {
                    $detalle->precio_multa = $request->precio_multa;
                } else {
                    $detalle->precio_multa = 0;
                }
                $detalle->cuenta_multa_id = $request->cuenta_multa;
                $detalle->dia_vencimiento_cuotas = $request->dia_vencimiento_cuotas;
                $detalle->fecha_vencimiento_primera_cuota = $request->fecha_vencimiento_primera_cuota;
                if ($request->dias_gracia) {
                    $detalle->dias_gracia = $request->dias_gracia;
                } else {
                    $detalle->dias_gracia = 0;
                }
                $detalle->precio_defensa = $request->precio_defensa;
                $detalle->cuenta_defensa_id = $request->cuenta_defensa;
                $detalle->precio_titulo = $request->precio_titulo;
                $detalle->cuenta_titulo_id = $request->cuenta_titulo;
                $detalle->precio_certificado = $request->precio_certificado;
                $detalle->cuenta_certificado_id = $request->cuenta_certificado;
                $detalle->precio_examen_suficiencia = $request->precio_examen_suficiencia;
                $detalle->cuenta_examen_suficiencia_id = $request->cuenta_examen_suficiencia;
                $detalle->precio_constancia_carrera = $request->precio_constancia_carrera;
                $detalle->cuenta_constancia_carrera_id = $request->cuenta_constancia_carrera;
                $detalle->save();
            }

            if ($request->carrera) {
                $carrera = Carrera::find($request->carrera);
                if ($carrera) {
                    $carrera->articulo_id = $articulo->id;
                    $carrera->save();
                }
            } elseif ($request->curso) {
                $curso = Curso::find($request->curso);
                if ($curso) {
                    $curso->articulo_id = $articulo->id;
                    $curso->save();

                    $precio_curso = CursoPrecio::where('curso_id', $curso->id)->first();
                    if ($precio_curso) {
                        $precio_curso->delete();
                    }
                    $precio = new CursoPrecio();
                    $precio->curso_id = $curso->id;
                    $precio->moneda_id = 1;
                    $precio->cantidad_cuotas = $articulo->detalle->cantidad_cuotas;
                    $precio->precio_contado = $articulo->detalle->precio_contado;
                    $precio->precio_cuota = $articulo->detalle->precio_cuota;
                    $precio->precio_multa = $articulo->detalle->precio_multa;
                    $precio->dia_vencimiento_cuota = $articulo->detalle->dia_vencimiento_cuotas;
                    $precio->fecha_inicio_vencimiento_cuota = $articulo->detalle->fecha_vencimiento_primera_cuota;
                    $precio->dias_gracia = $articulo->detalle->dias_gracia;
                    $precio->precio_defensa = $articulo->detalle->precio_defensa;
                    $precio->precio_titulo = $articulo->detalle->precio_titulo;
                    $precio->save();
                }
            }

            DB::commit();

            return redirect()->route('articulos.index')->with('success-message', 'El artículo ' . $articulo->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_articulos');

        try {
            $articulo = Articulo::findOrFail($id);
            $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();
            $carreras = Carrera::where('estado', 'AC')->get();
            $cursos = Curso::where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $articulo->unidad_id)->where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $articulo->centro_costo_id)->where('estado', 'AC')->get();

            return view('articulos/edit')->with(compact('articulo', 'cuentas_contables', 'carreras', 'cursos', 'unidades_negocios', 'subunidades_negocios', 'centros_costos', 'subcentros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_articulos');

        $request->validate([
            'codigo' => ['required', Rule::unique('articulos')->ignore($id)],
            'nombre' => ['required', Rule::unique('articulos')->ignore($id)],
            'impuesto' => ['required', 'numeric'],
            'compra_venta' => 'required',
            'cuenta_compra' => ['nullable', 'numeric', 'required_if:compra_venta,compra'],
            'stock' => 'required',
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],
            'centro_costo' => ['required', 'numeric'],
            'subcentro_costo' => ['required', 'numeric'],
            'carrera_curso' => 'nullable',
            'carrera' => ['nullable', 'numeric', 'required_if:carrera_curso,carrera'],
            'curso' => ['nullable', 'numeric', 'required_if:carrera_curso,curso'],

            'precio_matricula' => ['nullable', 'numeric', 'min:1', 'required_with:carrera'],
            'cuenta_matricula' => ['nullable', 'numeric', 'required_with:carrera'],
            'precio_contado' => ['nullable', 'numeric', 'min:1', 'required_with:carrera,curso'],
            'cuenta_contado' => ['nullable', 'numeric', 'required_with:carrera,curso'],
            'precio_cuota' => ['nullable', 'numeric', 'min:1', 'required_with:carrera,curso'],
            'cuenta_cuota' => ['nullable', 'numeric'],
            'cuenta_descuento' => ['nullable', 'numeric'],
            'precio_defensa' => ['nullable', 'numeric', 'min:1', 'required_with:carrera'],
            'cuenta_defensa' => ['nullable', 'numeric', 'required_with:carrera'],
            'precio_titulo' => ['nullable', 'numeric', 'min:1', 'required_with:carrera'],
            'cuenta_titulo' => ['nullable', 'numeric', 'required_with:carrera'],
            'precio_multa' => ['nullable', 'numeric', 'min:0'],
            'cuenta_multa' => ['nullable', 'numeric'],
            'precio_certificado' => ['nullable', 'numeric', 'min:1'],
            'cuenta_certificado' => ['nullable', 'numeric'],
            'precio_examen_suficiencia' => ['nullable', 'numeric', 'min:1'],
            'cuenta_examen_suficiencia' => ['nullable', 'numeric'],
            'precio_constancia_carrera' => ['nullable', 'numeric', 'min:1'],
            'cuenta_constancia_carrera' => ['nullable', 'numeric'],
            'cantidad_cuotas' => ['nullable', 'numeric', 'min:2', 'max:24', 'required_with:carrera,curso'],
            'fecha_vencimiento_primera_cuota' => ['nullable', 'date', 'required_with:carrera,curso'],
            'dia_vencimiento_cuotas' => ['nullable', 'numeric', 'min:1', 'max:28', 'required_with:carrera,curso'],
            'dias_gracia' => ['nullable', 'numeric', 'min:0', 'max:28'],
        ]);

        DB::beginTransaction();

        try {
            $articulo = Articulo::findOrFail($id);
            $articulo->codigo = removeAccents(Str::upper($request->codigo));
            $articulo->nombre = removeAccents(Str::upper($request->nombre));
            $articulo->impuesto = $request->impuesto;
            $articulo->compra_venta = Str::upper($request->compra_venta);
            $articulo->tiene_stock = $request->stock;
            if ($articulo->tiene_stock == true) {
                $articulo->stock = 0;
            }
            $articulo->unidad_id = $request->unidad_negocio;
            $articulo->subunidad_id = $request->subunidad_negocio;
            $articulo->centro_costo_id = $request->centro_costo;
            $articulo->subcentro_costo_id = $request->subcentro_costo;
            if ($request->compra_venta == 'compra') {
                $articulo->cuenta_compra_id = $request->cuenta_compra;
            } elseif ($request->compra_venta == 'venta') {
                $articulo->carrera_curso = Str::upper($request->carrera_curso);

                $detalle = ArticuloDetalle::where('articulo_id', $articulo->id)->first();
                $detalle->articulo_id = $articulo->id;
                $detalle->cantidad_cuotas = $request->cantidad_cuotas;
                $detalle->precio_matricula = $request->precio_matricula;
                $detalle->cuenta_matricula_id = $request->cuenta_matricula;
                $detalle->precio_contado = $request->precio_contado;
                $detalle->cuenta_contado_id = $request->cuenta_contado;
                $detalle->precio_cuota = $request->precio_cuota;
                $detalle->cuenta_cuota_id = $request->cuenta_cuota;
                $detalle->cuenta_descuento_id = $request->cuenta_descuento;
                if ($request->precio_multa) {
                    $detalle->precio_multa = $request->precio_multa;
                } else {
                    $detalle->precio_multa = 0;
                }
                $detalle->cuenta_multa_id = $request->cuenta_multa;
                $detalle->dia_vencimiento_cuotas = $request->dia_vencimiento_cuotas;
                $detalle->fecha_vencimiento_primera_cuota = $request->fecha_vencimiento_primera_cuota;
                if ($request->dias_gracia) {
                    $detalle->dias_gracia = $request->dias_gracia;
                } else {
                    $detalle->dias_gracia = 0;
                }
                $detalle->precio_defensa = $request->precio_defensa;
                $detalle->cuenta_defensa_id = $request->cuenta_defensa;
                $detalle->precio_titulo = $request->precio_titulo;
                $detalle->cuenta_titulo_id = $request->cuenta_titulo;
                $detalle->precio_certificado = $request->precio_certificado;
                $detalle->cuenta_certificado_id = $request->cuenta_certificado;
                $detalle->precio_examen_suficiencia = $request->precio_examen_suficiencia;
                $detalle->cuenta_examen_suficiencia_id = $request->cuenta_examen_suficiencia;
                $detalle->precio_constancia_carrera = $request->precio_constancia_carrera;
                $detalle->cuenta_constancia_carrera_id = $request->cuenta_constancia_carrera;
                $detalle->save();
            }
            $articulo->actualizado_por_id = Auth::id();
            $articulo->save();

            if ($request->carrera) {
                $carrera = Carrera::find($request->carrera);
                if ($carrera) {
                    $carrera->articulo_id = $articulo->id;
                    $carrera->save();
                }
            } elseif ($request->curso) {
                $curso = Curso::find($request->curso);
                if ($curso) {
                    $curso->articulo_id = $articulo->id;
                    $curso->save();

                    $precio_curso = CursoPrecio::where('curso_id', $curso->id)->first();
                    if ($precio_curso) {
                        $precio_curso->delete();
                    }

                    $precio = new CursoPrecio();
                    $precio->curso_id = $curso->id;
                    $precio->moneda_id = 1;
                    $precio->cantidad_cuotas = $articulo->detalle->cantidad_cuotas;
                    $precio->precio_contado = $articulo->detalle->precio_contado;
                    $precio->precio_cuota = $articulo->detalle->precio_cuota;
                    $precio->precio_multa = $articulo->detalle->precio_multa;
                    $precio->dia_vencimiento_cuota = $articulo->detalle->dia_vencimiento_cuotas;
                    $precio->fecha_inicio_vencimiento_cuota = $articulo->detalle->fecha_vencimiento_primera_cuota;
                    $precio->dias_gracia = $articulo->detalle->dias_gracia;
                    $precio->precio_defensa = $articulo->detalle->precio_defensa;
                    $precio->precio_titulo = $articulo->detalle->precio_titulo;
                    $precio->save();
                }
            }


            DB::commit();

            return redirect()->route('articulos.index')->with('success-message', 'El artículo ' . $articulo->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_articulos');

        DB::beginTransaction();

        try {
            $articulo = Articulo::findOrFail($id);
            $articulo->actualizado_por_id = Auth::id();
            $articulo->estado = 'IN';
            $articulo->save();

            DB::commit();

            return redirect()->route('articulos.index')->with('error-message','El artículo ' . $articulo->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_articulos');

        DB::beginTransaction();

        try {
            $articulo = Articulo::findOrFail($id);
            $articulo->actualizado_por_id = Auth::id();
            $articulo->estado = 'AC';
            $articulo->save();

            DB::commit();

            return redirect()->route('articulos.index')->with('success-message','El artículo ' . $articulo->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_articulos');

        DB::beginTransaction();

        try {
            $articulo = Articulo::findOrFail($id);
            ArticuloDetalle::where('articulo_id', $articulo->id)->delete();
            $carrera = Carrera::where('articulo_id', $articulo->id)->first();
            if ($carrera) {
                $carrera->articulo_id = null;
                $carrera->save();
            }
            $curso = Curso::where('articulo_id', $articulo->id)->first();
            if ($curso) {
                $curso->articulo_id = null;
                $curso->save();

                $curso_precio = CursoPrecio::where('curso_id', $curso->id)->first();
                if ($curso_precio) {
                    $curso_precio->delete();
                }
            }
            $articulo->delete();

            DB::commit();

            return redirect()->route('articulos.index')->with('success-message','El artículo ' . $articulo->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('articulos.index')->with('error-message', 'El artículo ' . $articulo->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_subunidades_negocios($id)
    {
        $this->authorize('crear_articulos');

        DB::beginTransaction();

        try {
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subunidades_negocios' => $subunidades_negocios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_subcentros_costos($id)
    {
        $this->authorize('crear_articulos');

        DB::beginTransaction();

        try {
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subcentros_costos' => $subcentros_costos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('articulos.index')->with('error-message', $e->getMessage());
        }
    }
}
