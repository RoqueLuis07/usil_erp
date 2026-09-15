<?php

namespace App\Http\Controllers;

class TesisController extends Controller
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
        $this->authorize('ver_tesis');

        try {
            return view('tesis/index');
        } catch (\Exception $e) {
            return redirect('/')->with('error-message', $e->getMessage());
        }
    }
}
