<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Cobro;


class CobroController extends Controller
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
        $this->authorize('ver_cobros');

        try {
            $cobros = Cobro::orderBy('id', 'desc')->get();
            return view('cobros/index')->with(compact('cobros'));
        } catch (\Exception $e) {
            return redirect()->route('cobros.index')->with('error-message', $e->getMessage());
        }
    }
}
