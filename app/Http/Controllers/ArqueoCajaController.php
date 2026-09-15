<?php

namespace App\Http\Controllers;

use App\Models\ArqueoCaja;


class ArqueoCajaController extends Controller
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
        //$this->authorize('ver arqueos de cajas');

        try {
            $arqueos_cajas = ArqueoCaja::orderBy('id', 'desc')->get();
            return view('arqueos_cajas/index')->with(compact('arqueos_cajas'));
        } catch (\Exception $e) {
            return redirect()->route('arqueos_cajas.index')->with('error-message', $e->getMessage());
        }
    }
}
