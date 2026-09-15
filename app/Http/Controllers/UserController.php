<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Configuracion;


class UserController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_usuarios');

        try {
            $buscar = Str::upper($request->buscar);

            $usuarios = User::with('roles')->orderBy('id', 'asc')->paginate(50);

			if (!(blank($buscar))) {
				$usuarios = User::orWhere('id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
				->orWhere('name', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
				->orWhere('email', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
				->orWhereHas('roles', function ($query) use ($buscar) {
                    $query->where('name', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                })
				->paginate(50);
			}

            return view('usuarios/index')->with(compact('usuarios', 'buscar'));
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_usuarios');

        try {
            $roles = Role::where('state', 'AC')->get();
            return view('usuarios/create')->with(compact('roles'));
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_usuarios');

        $request->validate([
            'name' => 'required',
            'email' => ['required', Rule::unique('usuarios')],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
            'rol' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $usuario = new User();
            $usuario->name = removeAccents(Str::upper($request->name));
            $usuario->email = removeAccents(Str::lower($request->email));
            $usuario->password = Hash::make($request->password);
            $usuario->role_id = $request->rol;
            $rol = Role::findOrFail($request->rol);
            $usuario->assignRole($rol);
            $usuario->avatar = 'no_image.jpg';
            $usuario->portada = 'no_portada.jpg';
            $usuario->save();

            $configuracion = new Configuracion();
            $configuracion->user_id = $usuario->id;
            $configuracion->lang = 'sp';
            $configuracion->data_layout = 'vertical';
            $configuracion->data_sidebar = 'dark';
            $configuracion->data_sidebar_size = 'lg';
            $configuracion->card_layout = null;
            $configuracion->data_bs_theme = 'light';
            $configuracion->data_layout_width = 'fluid';
            $configuracion->data_sidebar_image = 'none';
            $configuracion->data_layout_position = 'fixed';
            $configuracion->data_layout_style = 'default';
            $configuracion->data_topbar = 'warning';
            $configuracion->data_preloader = 'disable';
            $configuracion->save();

            DB::commit();

            return redirect()->route('usuarios.index')->with('success-message', 'El usuario ' . $usuario->name . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_usuarios');

        try {
            $usuario = User::findOrFail($id);
            $roles = Role::where('state', 'AC')->get();
            return view('usuarios/edit')->with(compact('usuario', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_usuarios');

        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('usuarios')->ignore($id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'password_confirmation' => ['nullable', 'min:8'],
            'rol' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $usuario = User::with('roles')->findOrFail($id);
            $usuario->name = removeAccents(Str::upper($request->name));
            $usuario->email = removeAccents(Str::lower($request->email));
            $usuario->role_id = $request->rol;
            if ($request->password) {
                $usuario->password = Hash::make($request->password);
            }

            if ($request->rol) {
                $role_name = $usuario->roles->pluck('name')->first();
                if ($role_name) {
                    $role = Role::findById($usuario->role_id);
                    $usuario->removeRole($role_name);
                    $usuario->assignRole($role);
                } else {
                    $role = Role::findById($usuario->role_id);
                    $usuario->assignRole($role);
                }
            }
            $usuario->save();

            DB::commit();

            return redirect()->route('usuarios.index')->with('success-message', 'El usuario ' . $usuario->name . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_usuarios');

        DB::beginTransaction();

        try {
            $usuario = User::findOrFail($id);
            $usuario->state = 'IN';
            $usuario->save();

            DB::commit();

            return redirect()->route('usuarios.index')->with('error-message','El usuario ' . $usuario->name . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_usuarios');

        DB::beginTransaction();

        try {
            $usuario = User::findOrFail($id);
            $usuario->state = 'AC';
            $usuario->save();

            DB::commit();

            return redirect()->route('usuarios.index')->with('success-message','El usuario ' . $usuario->name . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_usuarios');

        DB::beginTransaction();

        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            DB::commit();

            return redirect()->route('usuarios.index')->with('success-message','El usuario ' . $usuario->name . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('usuarios.index')->with('error-message', 'El usuario ' . $usuario->name . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('usuarios.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
