<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('ver_permisos');

        try {
            $permisos = Permission::get();

            return view ('permisos/index')->with(compact('permisos'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
