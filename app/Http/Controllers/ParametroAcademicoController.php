<?php

namespace App\Http\Controllers;

class ParametroAcademicoController extends Controller
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
        $this->authorize('ver_parametros_academicos');

        try {
            return view('parametros_academicos/index');
        } catch (\Exception $e) {
            return redirect('/')->with('error-message', $e->getMessage());
        }
    }
}
