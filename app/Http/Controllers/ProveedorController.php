<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Rules\PuntajesSolapados;

use App\Models\Proveedor;
use App\Models\DepartamentoParaguay;
use App\Models\Ciudad;
use App\Models\CategoriaProveedor;
use App\Models\Banco;
use App\Models\Moneda;


class ProveedorController extends Controller
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
        $this->authorize('ver_proveedores');

        try {
            $proveedores = Proveedor::orderBy('id', 'desc')->get();
            return view('proveedores/index')->with(compact('proveedores'));
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_proveedores');

        try {
            $proveedor = Proveedor::findOrFail($id);
            return view('proveedores/show')->with(compact('proveedor'));
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_proveedores');

        try {
            $departamentos_paraguay = DepartamentoParaguay::get();
            $ciudades = Ciudad::get();
            $categorias = CategoriaProveedor::where('estado', 'AC')->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            return view('proveedores/create')->with(compact('departamentos_paraguay', 'ciudades', 'categorias', 'bancos', 'monedas'));
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_proveedores');

        $request->validate([
            'nombre_fantasia' => ['required', Rule::unique('proveedores')],
            'razon_social' => ['required', Rule::unique('proveedores')],
            'ruc' => ['required', Rule::unique('proveedores')],
            'telefono' => ['required'],
            'email' => ['required', 'email'],
            'direccion' => 'required',
            'departamento_paraguay' => ['nullable', 'numeric'],
            'ciudad' => ['nullable', 'numeric'],
            'categoria' => ['required', 'numeric'],
            'nombre_contacto' => 'nullable',
            'telefono_contacto' => 'nullable',
            'email_contacto' => ['nullable', 'email'],
            'banco' => ['nullable', 'numeric'],
            'numero_cuenta' => ['nullable', 'numeric'],
            'moneda' => ['nullable', 'numeric'],
            'titular' => 'nullable',
            'documento_titular' => 'nullable',
            'alias_cuenta' => 'nullable',
            'observaciones' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $proveedor = new Proveedor();
            $proveedor->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $proveedor->razon_social = removeAccents(Str::upper($request->razon_social));
            $proveedor->ruc = removeAccents(Str::upper($request->ruc));
            $proveedor->telefono = $request->telefono;
            $proveedor->email = removeAccents(Str::lower($request->email));
            $proveedor->direccion = removeAccents(Str::upper($request->direccion));
            $proveedor->departamento_id = $request->departamento_paraguay;
            $proveedor->ciudad_id = $request->ciudad;
            $proveedor->categoria_id = $request->categoria;
            $proveedor->nombre_contacto = removeAccents(Str::upper($request->nombre_contacto));
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->email_contacto = removeAccents(Str::lower($request->email_contacto));
            $proveedor->banco_id = $request->banco;
            $proveedor->numero_cuenta = $request->numero_cuenta;
            $proveedor->tipo_cuenta = $request->tipo_cuenta;
            $proveedor->moneda_id = $request->moneda;
            $proveedor->titular = removeAccents(Str::upper($request->titular));
            $proveedor->documento_titular = removeAccents(Str::upper($request->documento_titular));
            $proveedor->alias_cuenta = removeAccents(Str::lower($request->alias_cuenta));
            $proveedor->observaciones = removeAccents(Str::upper($request->observaciones));
            $proveedor->cargado_por_id = Auth::id();
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')->with('success-message', 'El proveedor ' . $proveedor->razon_social . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_proveedores');

        try {
            $proveedor = Proveedor::findOrFail($id);
            $departamentos_paraguay = DepartamentoParaguay::get();
            $ciudades = Ciudad::get();
            $categorias = CategoriaProveedor::where('estado', 'AC')->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            return view('proveedores/edit')->with(compact('proveedor', 'departamentos_paraguay', 'ciudades', 'categorias', 'bancos', 'monedas'));
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_proveedores');

        $request->validate([
            'nombre_fantasia' => ['required', Rule::unique('proveedores')->ignore($id)],
            'razon_social' => ['required', Rule::unique('proveedores')->ignore($id)],
            'ruc' => ['required', Rule::unique('proveedores')->ignore($id)],
            'telefono' => ['required'],
            'email' => ['required', 'email'],
            'direccion' => 'required',
            'departamento_paraguay' => ['nullable', 'numeric'],
            'ciudad' => ['nullable', 'numeric'],
            'categoria' => ['required', 'numeric'],
            'nombre_contacto' => 'nullable',
            'telefono_contacto' => 'nullable',
            'email_contacto' => ['nullable', 'email'],
            'banco' => ['nullable', 'numeric'],
            'numero_cuenta' => ['nullable', 'numeric'],
            'moneda' => ['nullable', 'numeric'],
            'titular' => 'nullable',
            'documento_titular' => 'nullable',
            'alias_cuenta' => 'nullable',
            'observaciones' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $proveedor->razon_social = removeAccents(Str::upper($request->razon_social));
            $proveedor->ruc = removeAccents(Str::upper($request->ruc));
            $proveedor->telefono = $request->telefono;
            $proveedor->email = removeAccents(Str::lower($request->email));
            $proveedor->direccion = removeAccents(Str::upper($request->direccion));
            $proveedor->departamento_id = $request->departamento_paraguay;
            $proveedor->ciudad_id = $request->ciudad;
            $proveedor->categoria_id = $request->categoria;
            $proveedor->nombre_contacto = removeAccents(Str::upper($request->nombre_contacto));
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->email_contacto = removeAccents(Str::lower($request->email_contacto));
            $proveedor->banco_id = $request->banco;
            $proveedor->numero_cuenta = $request->numero_cuenta;
            $proveedor->tipo_cuenta = $request->tipo_cuenta;
            $proveedor->moneda_id = $request->moneda;
            $proveedor->titular = removeAccents(Str::upper($request->titular));
            $proveedor->documento_titular = removeAccents(Str::upper($request->documento_titular));
            $proveedor->alias_cuenta = removeAccents(Str::lower($request->alias_cuenta));
            $proveedor->observaciones = removeAccents(Str::upper($request->observaciones));
            $proveedor->actualizado_por_id = Auth::id();
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')->with('success-message', 'El proveedor ' . $proveedor->razon_social . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_proveedores');

        DB::beginTransaction();

        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->actualizado_por_id = Auth::id();
            $proveedor->estado = 'IN';
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')->with('error-message','El proveedor ' . $proveedor->razon_social . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_proveedores');

        DB::beginTransaction();

        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->actualizado_por_id = Auth::id();
            $proveedor->estado = 'AC';
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')->with('success-message','El proveedor ' . $proveedor->razon_social . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_proveedores');

        DB::beginTransaction();

        try {
            $proveedor = Proveedor::findOrFail($id);

            $proveedor_detalles = ProveedorDetalle::where('proveedor_id', $proveedor->id)->delete();

            $proveedor->delete();

            DB::commit();

            return redirect()->route('proveedores.index')->with('success-message','El proveedor ' . $proveedor->razon_social . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('proveedores.index')->with('error-message', 'El proveedor ' . $proveedor->razon_social . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('proveedores.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
