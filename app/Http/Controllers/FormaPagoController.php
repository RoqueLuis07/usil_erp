<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\FormaPago;


class FormaPagoController extends Controller
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
        $this->authorize('ver_pagos_formas');

        return view('formas_pagos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_pagos_formas');

        try {
            $formas_pagos = FormaPago::orderBy('id', 'asc')->get();
            return response()->json([
                'formas_pagos' => $formas_pagos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_pagos_formas');

        try {
            $forma_pago = FormaPago::findOrFail($id);
            return response()->json([
                'forma_pago' => $forma_pago,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_pagos_formas');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('formas_pagos')],
            ]);

            try {
                $forma_pago = new FormaPago();
                $forma_pago->nombre = removeAccents(Str::upper($request->nombre));
                $forma_pago->cargado_por_id = Auth::id();
                $forma_pago->save();

                DB::commit();

                $formas_pagos = FormaPago::get();
                return response()->json([
                    'message' => 'La forma de pago ' . $forma_pago->nombre . ' fue creada exitosamente.',
                    'formas_pagos' => $formas_pagos,
                    'selected' => $forma_pago,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_pagos_formas');

        try {
            $forma_pago = FormaPago::findOrFail($id);
            return response()->json([
                'forma_pago' => $forma_pago,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_pagos_formas');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('formas_pagos')->ignore($id)],
        ]);

        try {
            $forma_pago = FormaPago::findOrFail($id);
            $forma_pago->nombre = removeAccents(Str::upper($request->nombre));
            $forma_pago->actualizado_por_id = Auth::id();
            $forma_pago->save();

            DB::commit();

            $formas_pagos = FormaPago::get();
            return response()->json([
                'message' => 'La forma de pago ' . $forma_pago->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_pagos_formas');

        try {
            $forma_pago = FormaPago::findOrFail($id);
            return response()->json([
                'forma_pago' => $forma_pago,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_pagos_formas');

        DB::beginTransaction();

        try {
            $forma_pago = FormaPago::findOrFail($id);
            $forma_pago->actualizado_por_id = Auth::id();
            $forma_pago->estado = 'IN';
            $forma_pago->save();

            DB::commit();

            return response()->json([
                'message' => 'La forma de pago ' . $forma_pago->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_pagos_formas');

        try {
            $forma_pago = FormaPago::findOrFail($id);
            return response()->json([
                'forma_pago' => $forma_pago,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_pagos_formas');

        DB::beginTransaction();

        try {
            $forma_pago = FormaPago::findOrFail($id);
            $forma_pago->actualizado_por_id = Auth::id();
            $forma_pago->estado = 'AC';
            $forma_pago->save();

            DB::commit();

            return response()->json([
                'message' => 'La forma de pago ' . $forma_pago->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_pagos_formas');

        try {
            $forma_pago = FormaPago::findOrFail($id);
            return response()->json([
                'forma_pago' => $forma_pago,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_pagos_formas');

        DB::beginTransaction();

        try {
            $forma_pago = FormaPago::findOrFail($id);
            $forma_pago->delete();

            DB::commit();

            return response()->json([
                'message' => 'La forma de pago ' . $forma_pago->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('formas_pagos.index')->with('error-message', 'La forma de pago ' . $forma_pago->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('formas_pagos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
