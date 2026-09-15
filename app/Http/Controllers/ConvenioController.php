<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Convenio;
use App\Models\ConvenioDetalle;

class ConvenioController extends Controller
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
        $this->authorize('ver_convenios');

        try {
            $convenios = Convenio::orderBy('id', 'desc')->get();

            return view('convenios/index')->with(compact('convenios'));
        } catch (\Exception $e) {
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_convenios');

        try {
            $convenio = Convenio::with('detalle')->findOrFail($id);
            return view('convenios/show')->with(compact('convenio'));
        } catch (\Exception $e) {
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_convenios');

        try {
            return view('convenios/create');
        } catch (\Exception $e) {
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_convenios');

        $rules = [
            'nombre' => ['required', Rule::unique('convenios')],
            'tipo' => 'required',

            'tipo_matricula' => 'nullable',
            'descuento_matricula' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_matricula,VA'],
            'porcentaje_matricula' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_matricula,PO'],
            'tipo_contado' => 'nullable',
            'descuento_contado' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_contado,VA'],
            'porcentaje_contado' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_contado,PO'],
            'tipo_cuotas' => 'nullable',
            'aplica_a_cuotas' => 'nullable',
            'descuento_cuotas' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuotas' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_1' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_1' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_2' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_2' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_3' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_3' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_4' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_4' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_5' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_5' => ['nullable', 'numeric', 'min:1'],
            'tipo_descuento' => ['nullable', 'required_if:tipo,DE'],
            'precio_descuento' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_descuento' => ['nullable', 'numeric', 'min:1'],
        ];

        if ($request->tipo ==  'CO') {
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == 'TO') {
                $rules['descuento_cuotas'] = 'required|numeric|min:1';   
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == 'TO') {
                $rules['porcentaje_cuotas'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == '1C') {
                $rules['descuento_cuota_1'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == '1C') {
                    $rules['porcentaje_cuota_1'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == 'CO') {
                if (!isset($request->descuento_cuota_1) && !isset($request->descuento_cuota_2) && !isset($request->descuento_cuota_3) && !isset($request->descuento_cuota_4) && !isset($request->descuento_cuota_5)) {
                    $rules['cuotas'] = 'required';
                    $rules['descuentos'] = 'required';
                }
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == 'CO') {
                if (!isset($request->porcentaje_cuota_1) && !isset($request->porcentaje_cuota_2) && !isset($request->porcentaje_cuota_3) && !isset($request->porcentaje_cuota_4) && !isset($request->porcentaje_cuota_5)) {
                    $rules['cuotas'] = 'required';
                    $rules['porcentajes'] = 'required';
                }
            }
        }

        if ($request->tipo == 'DE' && $request->tipo_descuento == 'VA') {
            $rules['precio_descuento'] = ['required', 'numeric', 'min:1'];
        }

        if ($request->tipo == 'DE' && $request->tipo_descuento == 'PO') {
            $rules['porcentaje_descuento'] = ['required', 'numeric', 'min:1'];
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $convenio = new Convenio();
            $convenio->nombre = removeAccents(Str::upper($request->nombre));
            $convenio->tipo = $request->tipo;
            $convenio->cargado_por_id = Auth::id();
            $convenio->save();

            $convenio_detalle = new ConvenioDetalle();
            $convenio_detalle->convenio_id = $convenio->id;

            if ($convenio->tipo == 'CO') {
                if ($request->tipo_matricula) {
                    $convenio_detalle->tipo_matricula = $request->tipo_matricula;
                    switch ($request->tipo_matricula) {
                        case 'VA':
                            $convenio_detalle->descuento_matricula = $request->descuento_matricula;
                            break;
                        case 'PO':
                            $convenio_detalle->porcentaje_matricula = $request->porcentaje_matricula;
                            break;
                    }
                }
    
                if ($request->tipo_contado) {
                    $convenio_detalle->tipo_contado = $request->tipo_contado;
                    switch ($request->tipo_contado) {
                        case 'VA':
                            $convenio_detalle->descuento_contado = $request->descuento_contado;
                            break;
                        case 'PO':
                            $convenio_detalle->porcentaje_contado = $request->porcentaje_contado;
                            break;
                    }
                }
    
                if ($request->tipo_cuotas) {
                    $convenio_detalle->tipo_cuotas = $request->tipo_cuotas;
                    if ($request->aplica_a_cuotas) {
                        $convenio_detalle->aplica_a_cuotas = $request->aplica_a_cuotas;
                        switch ($request->aplica_a_cuotas) {
                            case 'TO':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuotas = $request->descuento_cuotas;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuotas = $request->porcentaje_cuotas;
                                        break;
                                }
                                break;
                            case '1C':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuota_1 = $request->descuento_cuota_1;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuota_1 = $request->porcentaje_cuota_1;
                                        break;
                                }
                                break;
                            case 'CO':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuota_1 = $request->descuento_cuota_1;
                                        $convenio_detalle->descuento_cuota_2 = $request->descuento_cuota_2;
                                        $convenio_detalle->descuento_cuota_3 = $request->descuento_cuota_3;
                                        $convenio_detalle->descuento_cuota_4 = $request->descuento_cuota_4;
                                        $convenio_detalle->descuento_cuota_5 = $request->descuento_cuota_5;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuota_1 = $request->porcentaje_cuota_1;
                                        $convenio_detalle->porcentaje_cuota_2 = $request->porcentaje_cuota_2;
                                        $convenio_detalle->porcentaje_cuota_3 = $request->porcentaje_cuota_3;
                                        $convenio_detalle->porcentaje_cuota_4 = $request->porcentaje_cuota_4;
                                        $convenio_detalle->porcentaje_cuota_5 = $request->porcentaje_cuota_5;
                                        break;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            } else if ($convenio->tipo == 'DE') {
                $convenio_detalle->tipo_descuento = $request->tipo_descuento;
                switch ($request->tipo_descuento) {
                    case 'VA':
                        $convenio_detalle->precio_descuento = $request->precio_descuento;
                        break;
                    case 'PO':
                        $convenio_detalle->porcentaje_descuento = $request->porcentaje_descuento;
                        break;
                }
            }
            $convenio_detalle->save();

            DB::commit();

            return redirect()->route('convenios.index')->with('success-message', 'El convenio ' . $convenio->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_convenios');

        try {
            $convenio = Convenio::with('detalle')->findOrFail($id);
            return view('convenios/edit')->with(compact('convenio'));
        } catch (\Exception $e) {
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_convenios');

        $rules = [
            'nombre' => ['required', Rule::unique('convenios')->ignore($id)],
            'tipo' => 'required',

            'tipo_matricula' => 'nullable',
            'descuento_matricula' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_matricula,VA'],
            'porcentaje_matricula' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_matricula,PO'],
            'tipo_contado' => 'nullable',
            'descuento_contado' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_contado,VA'],
            'porcentaje_contado' => ['nullable', 'numeric', 'min:1', 'required_if:tipo_contado,PO'],
            'tipo_cuotas' => 'nullable',
            'aplica_a_cuotas' => 'nullable',
            'descuento_cuotas' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuotas' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_1' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_1' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_2' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_2' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_3' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_3' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_4' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_4' => ['nullable', 'numeric', 'min:1'],
            'descuento_cuota_5' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_cuota_5' => ['nullable', 'numeric', 'min:1'],
            'tipo_descuento' => ['nullable', 'required_if:tipo,DE'],
            'precio_descuento' => ['nullable', 'numeric', 'min:1'],
            'porcentaje_descuento' => ['nullable', 'numeric', 'min:1'],
        ];

        if ($request->tipo == 'CO') {
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == 'TO') {
                $rules['descuento_cuotas'] = 'required|numeric|min:1';   
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == 'TO') {
                $rules['porcentaje_cuotas'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == '1C') {
                $rules['descuento_cuota_1'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == '1C') {
                    $rules['porcentaje_cuota_1'] = 'required|numeric|min:1';
            }
    
            if ($request->tipo_cuotas == 'VA' && $request->aplica_a_cuotas == 'CO') {
                if (!isset($request->descuento_cuota_1) && !isset($request->descuento_cuota_2) && !isset($request->descuento_cuota_3) && !isset($request->descuento_cuota_4) && !isset($request->descuento_cuota_5)) {
                    $rules['cuotas'] = 'required';
                    $rules['descuentos'] = 'required';
                }
            }
    
            if ($request->tipo_cuotas == 'PO' && $request->aplica_a_cuotas == 'CO') {
                if (!isset($request->porcentaje_cuota_1) && !isset($request->porcentaje_cuota_2) && !isset($request->porcentaje_cuota_3) && !isset($request->porcentaje_cuota_4) && !isset($request->porcentaje_cuota_5)) {
                    $rules['cuotas'] = 'required';
                    $rules['porcentajes'] = 'required';
                }
            }
        }

        if ($request->tipo == 'DE' && $request->tipo_descuento == 'VA') {
            $rules['precio_descuento'] = ['required', 'numeric', 'min:1'];
        }

        if ($request->tipo == 'DE' && $request->tipo_descuento == 'PO') {
            $rules['porcentaje_descuento'] = ['required', 'numeric', 'min:1'];
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $convenio = Convenio::findOrFail($id);
            $convenio->nombre = removeAccents(Str::upper($request->nombre));
            $convenio->tipo = $request->tipo;
            $convenio->actualizado_por_id = Auth::id();
            $convenio->save();

            $convenio_detalle = ConvenioDetalle::where('convenio_id', $convenio->id)->first();

            if ($convenio->tipo == 'CO') {
                if ($request->tipo_matricula) {
                    $convenio_detalle->tipo_matricula = $request->tipo_matricula;
                    switch ($request->tipo_matricula) {
                        case 'VA':
                            $convenio_detalle->descuento_matricula = $request->descuento_matricula;
                            break;
                        case 'PO':
                            $convenio_detalle->porcentaje_matricula = $request->porcentaje_matricula;
                            break;
                    }
                }
    
                if ($request->tipo_contado) {
                    $convenio_detalle->tipo_contado = $request->tipo_contado;
                    switch ($request->tipo_contado) {
                        case 'VA':
                            $convenio_detalle->descuento_contado = $request->descuento_contado;
                            break;
                        case 'PO':
                            $convenio_detalle->porcentaje_contado = $request->porcentaje_contado;
                            break;
                    }
                }
    
                if ($request->tipo_cuotas) {
                    $convenio_detalle->tipo_cuotas = $request->tipo_cuotas;
                    if ($request->aplica_a_cuotas) {
                        $convenio_detalle->aplica_a_cuotas = $request->aplica_a_cuotas;
                        switch ($request->aplica_a_cuotas) {
                            case 'TO':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuotas = $request->descuento_cuotas;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuotas = $request->porcentaje_cuotas;
                                        break;
                                }
                                break;
                            case '1C':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuota_1 = $request->descuento_cuota_1;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuota_1 = $request->porcentaje_cuota_1;
                                        break;
                                }
                                break;
                            case 'CO':
                                switch ($request->tipo_cuotas) {
                                    case 'VA':
                                        $convenio_detalle->descuento_cuota_1 = $request->descuento_cuota_1;
                                        $convenio_detalle->descuento_cuota_2 = $request->descuento_cuota_2;
                                        $convenio_detalle->descuento_cuota_3 = $request->descuento_cuota_3;
                                        $convenio_detalle->descuento_cuota_4 = $request->descuento_cuota_4;
                                        $convenio_detalle->descuento_cuota_5 = $request->descuento_cuota_5;
                                        break;
                                    case 'PO':
                                        $convenio_detalle->porcentaje_cuota_1 = $request->porcentaje_cuota_1;
                                        $convenio_detalle->porcentaje_cuota_2 = $request->porcentaje_cuota_2;
                                        $convenio_detalle->porcentaje_cuota_3 = $request->porcentaje_cuota_3;
                                        $convenio_detalle->porcentaje_cuota_4 = $request->porcentaje_cuota_4;
                                        $convenio_detalle->porcentaje_cuota_5 = $request->porcentaje_cuota_5;
                                        break;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            } elseif ($convenio->tipo == 'DE') {
                $convenio_detalle->tipo_descuento = $request->tipo_descuento;
                switch ($request->tipo_descuento) {
                    case 'VA':
                        $convenio_detalle->precio_descuento = $request->precio_descuento;
                        break;
                    case 'PO':
                        $convenio_detalle->porcentaje_descuento = $request->porcentaje_descuento;
                        break;
                }
            }
            $convenio_detalle->save();

            DB::commit();

            return redirect()->route('convenios.index')->with('success-message', 'El convenio ' . $convenio->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_convenios');

        DB::beginTransaction();

        try {
            $convenio = Convenio::findOrFail($id);
            $convenio->actualizado_por_id = Auth::id();
            $convenio->estado = 'IN';
            $convenio->save();

            DB::commit();

            return redirect()->route('convenios.index')->with('error-message','El convenio ' . $convenio->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_convenios');

        DB::beginTransaction();

        try {
            $convenio = Convenio::findOrFail($id);
            $convenio->actualizado_por_id = Auth::id();
            $convenio->estado = 'AC';
            $convenio->save();

            DB::commit();

            return redirect()->route('convenios.index')->with('success-message','El convenio ' . $convenio->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_convenios');

        DB::beginTransaction();

        try {
            $convenio = Convenio::findOrFail($id);
            ConvenioDetalle::where('convenio_id', $convenio->id)->delete();
            $convenio->delete();

            DB::commit();

            return redirect()->route('convenios.index')->with('success-message','El convenio ' . $convenio->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('convenios.index')->with('error-message', 'El convenio ' . $convenio->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('convenios.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
