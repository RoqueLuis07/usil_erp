<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Cliente;
use App\Models\Sexo;
use App\Models\EstadoCivil;
use App\Models\Nacionalidad;
use App\Models\DepartamentoParaguay;
use App\Models\Ciudad;
use App\Models\Barrio;
use App\Models\ClienteDatoLaboral;
use App\Models\ClienteDireccion;
use App\Models\ClienteTelefono;


class ClienteController extends Controller
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
        $this->authorize('ver_clientes');

        try {
            $clientes = Cliente::orderBy('id', 'desc')->get();
            return view('clientes/index')->with(compact('clientes'));
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_clientes');

        try {
            $cliente = Cliente::findOrFail($id);
            return view('clientes/show')->with(compact('cliente'));
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_clientes');

        try {
            $sexos = Sexo::where('estado', 'AC')->get();
            $estados_civiles = EstadoCivil::get();
            $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
            $departamentos_paraguay = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            $ciudades = Ciudad::orderBy('nombre', 'asc')->get();
            $barrios = Barrio::orderBy('nombre', 'asc')->get();
            return view('clientes/create')->with(compact('sexos', 'estados_civiles', 'nacionalidades', 'departamentos_paraguay', 'ciudades', 'barrios'));
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_clientes');

        $request->validate([
            'nombre' => 'required',
            'razon_social' => 'nullable',
            'numero_documento' => ['required', Rule::unique('clientes')],
            'celular' => 'nullable',
            'telefono' => 'nullable',
            'email' => ['nullable', 'email'],
            'nacionalidad' => ['nullable', 'numeric'],
            'direccion' => 'nullable',
            'departamento' => ['nullable', 'numeric'],
            'ciudad' => ['nullable', 'numeric'],
            'barrio' => ['nullable', 'numeric'],
            'sexo' => ['nullable', 'numeric'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'estado_civil' => ['nullable', 'numeric'],
            'observaciones' => 'nullable',

            'empresa' => 'nullable',
            'cargo' => ['nullable', 'required_with:empresa'],
            'telefono_laboral' => ['nullable', 'required_with:empresa'],
            'celular_laboral' => 'nullable',
            'email_laboral' => ['nullable', 'email'],
        ]);

        DB::beginTransaction();

        try {
            $cliente = new Cliente();
            $cliente->nombre = removeAccents(Str::upper($request->nombre));
            if ($request->razon_social) {
                $cliente->razon_social = removeAccents(Str::upper($request->razon_social));
            } else {
                $cliente->razon_social = removeAccents(Str::upper($request->nombre));
            }
            $cliente->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $cliente->celular = $request->celular;
            $cliente->telefono = $request->telefono;
            $cliente->email = removeAccents(Str::lower($request->email));
            $cliente->nacionalidad_id = $request->nacionalidad;
            $cliente->direccion = removeAccents(Str::upper($request->direccion));
            $cliente->departamento_id = $request->departamento;
            $cliente->ciudad_id = $request->ciudad;
            $cliente->barrio_id = $request->barrio;
            $cliente->sexo_id = $request->sexo;
            $cliente->fecha_nacimiento = $request->fecha_nacimiento;
            $cliente->estado_civil_id = $request->estado_civil;
            $cliente->observaciones = removeAccents(Str::upper($request->observaciones));


            if ($request->empresa != null) {
                $laboral = new ClienteDatoLaboral();
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
                $laboral->telefono = $request->telefono_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->email = removeAccents(Str::lower($request->email_laboral));
                $laboral->save();
                $cliente->dato_laboral_id = $laboral->id;
            }

            $cliente->cargado_por_id = Auth::id();
            $cliente->save();

            DB::commit();

            return redirect()->route('clientes.index')->with('success-message', 'El cliente ' . $cliente->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_clientes');

        try {
            $cliente = Cliente::findOrFail($id);
            $sexos = Sexo::where('estado', 'AC')->get();
            $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
            $departamentos_paraguay = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            $ciudades = Ciudad::orderBy('nombre', 'asc')->get();
            $barrios = Barrio::orderBy('nombre', 'asc')->get();
            $estados_civiles = EstadoCivil::get();
            return view('clientes/edit')->with(compact('cliente', 'sexos', 'nacionalidades', 'departamentos_paraguay', 'ciudades', 'barrios', 'estados_civiles'));
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_clientes');

        $request->validate([
            'nombre' => 'required',
            'razon_social' => 'nullable',
            'numero_documento' => ['required', Rule::unique('clientes')->ignore($id)],
            'celular' => 'nullable',
            'telefono' => 'nullable',
            'email' => ['nullable', 'email'],
            'nacionalidad' => ['nullable', 'numeric'],
            'direccion' => 'nullable',
            'departamento' => ['nullable', 'numeric'],
            'ciudad' => ['nullable', 'numeric'],
            'barrio' => ['nullable', 'numeric'],
            'sexo' => ['nullable', 'numeric'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'estado_civil' => ['nullable', 'numeric'],
            'observaciones' => 'nullable',

            'empresa' => 'nullable',
            'cargo' => ['nullable', 'required_with:empresa'],
            'telefono_laboral' => ['nullable', 'required_with:empresa'],
            'celular_laboral' => 'nullable',
            'email_laboral' => ['nullable', 'email'],
        ]);

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->nombre = removeAccents(Str::upper($request->nombre));
            if ($request->razon_social) {
                $cliente->razon_social = removeAccents(Str::upper($request->razon_social));
            } else {
                $cliente->razon_social = removeAccents(Str::upper($request->nombre));
            }
            $cliente->numero_documento = removeAccents(Str::upper($request->numero_documento));
            $cliente->celular = $request->celular;
            $cliente->telefono = $request->telefono;
            $cliente->email = removeAccents(Str::lower($request->email));
            $cliente->nacionalidad_id = $request->nacionalidad;
            $cliente->direccion = removeAccents(Str::upper($request->direccion));
            $cliente->departamento_id = $request->departamento;
            $cliente->ciudad_id = $request->ciudad;
            $cliente->barrio_id = $request->barrio;
            $cliente->sexo_id = $request->sexo;
            $cliente->fecha_nacimiento = $request->fecha_nacimiento;
            $cliente->estado_civil_id = $request->estado_civil;
            $cliente->observaciones = removeAccents(Str::upper($request->observaciones));


            if ($request->empresa != null) {
                $laboral = new ClienteDatoLaboral();
                $laboral->empresa = removeAccents(Str::upper($request->empresa));
                $laboral->cargo = removeAccents(Str::upper($request->cargo));
                $laboral->telefono = $request->telefono_laboral;
                $laboral->celular = $request->celular_laboral;
                $laboral->email = removeAccents(Str::lower($request->email_laboral));
                $laboral->save();
                $cliente->dato_laboral_id = $laboral->id;
            }

            $cliente->cargado_por_id = Auth::id();
            $cliente->save();

            DB::commit();

            return redirect()->route('clientes.index')->with('success-message', 'El cliente ' . $cliente->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_clientes');

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->actualizado_por_id = Auth::id();
            $cliente->estado = 'IN';
            $cliente->save();

            DB::commit();

            return redirect()->route('clientes.index')->with('error-message','El cliente ' . $cliente->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_clientes');

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->actualizado_por_id = Auth::id();
            $cliente->estado = 'AC';
            $cliente->save();

            DB::commit();

            return redirect()->route('clientes.index')->with('success-message','El cliente ' . $cliente->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_clientes');

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($id);
            $cliente_dato_laboral = ClienteDatoLaboral::where('id', $cliente->dato_laboral_id)->delete();

            $cliente->delete();

            DB::commit();

            return redirect()->route('clientes.index')->with('success-message','El cliente ' . $cliente->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('clientes.index')->with('error-message', 'El cliente ' . $cliente->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
