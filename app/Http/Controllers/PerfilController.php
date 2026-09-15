<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

use App\Models\User;
use App\Models\Configuracion;


class PerfilController extends Controller
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

    public function show($id)
    {
        try {
            $usuario = User::with('roles')->findOrFail($id);
            return view('perfiles/show')->with(compact('usuario'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        if ($request->tipo == 'update') {
            $request->validate([
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('usuarios')->ignore($id)],
            ]);

            try {
                $usuario = User::findOrFail($id);
                $usuario->name = removeAccents(Str::upper($request->name));
                $usuario->email = removeAccents(Str::lower($request->email));
                $usuario->save();

                DB::commit();

                return response()->json([
                    'message' => 'Su información fue actualizada exitosamente.',
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        } else if ($request->tipo == 'change_password') {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'new_password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
                'new_password_confirmation' => ['required', 'min:8'],
            ]);

            try {
                $usuario = User::findOrFail($id);
                $usuario->password = Hash::make($request->new_password);
                $usuario->save();

                DB::commit();

                return response()->json([
                    'message' => 'Su contraseña fue actualizada exitosamente.',
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        } else if ($request->tipo == 'customizer') {
            try {
                $configuracion = Configuracion::where('user_id', $id)->first();
                $configuracion->lang = 'sp';
                $configuracion->data_layout = $request['data-layout'];
                $configuracion->data_sidebar = $request['data-sidebar'];
                $configuracion->data_sidebar_size = $request['data-sidebar-size'];
                $configuracion->card_layout = null;
                $configuracion->data_bs_theme = $request['data-bs-theme'];
                $configuracion->data_layout_width = $request['data-layout-width'];
                $configuracion->data_sidebar_image = 'none';
                $configuracion->data_layout_position = $request['data-layout-position'];
                $configuracion->data_layout_style = $request['data-layout-style'];
                $configuracion->data_topbar = $request['data-topbar'];
                $configuracion->data_preloader = 'disable';
                $configuracion->save();

                DB::commit();

                return response()->json([
                    'message' => 'Su diseño de vista fue actualizado exitosamente.',
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        } else if ($request->tipo == 'reset_customizer') {
            try {
                $configuracion = Configuracion::where('user_id', $id)->first();
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

                return response()->json([
                    'message' => 'Su diseño de vista fue restablecido exitosamente.',
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        } else if ($request->tipo == 'avatar') {
            $request->validate([
                'avatar' => ['required', 'extensions:jpg,png'],
            ]);

            try {
                $usuario = User::findOrFail($id);

                //eliminar y cargar imagen
                $imagen = $request->avatar;
                if ($imagen != null) {
                    if ($usuario->avatar != 'no_image.jpg') {
                        $ubicacion_y_nombre = 'storage/usuarios/' . $usuario->avatar;
                        File::delete($ubicacion_y_nombre);
                    }

                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($imagen);
                    $image = $image->encode(new AutoEncoder(quality: 50));

                    $id = $usuario->id;
                    $nombre = removeAccents(Str::lower($usuario->name));
                    $extension = $imagen->getClientOriginalExtension();
                    $nombre_imagen = $id . '_' . $nombre . '.' . $extension;
                    $image->save('storage/usuarios/' . $nombre_imagen);
                    $usuario->avatar = $nombre_imagen;
                }
                $usuario->save();

            DB::commit();

            return response()->json([
                'message' => 'Su imagen de perfil fue actualizada exitosamente.',
            ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        } else if ($request->tipo == 'portada') {
            try {
                $usuario = User::findOrFail($id);

                //cargar imagen
                $imagen = $request->portada;
                if ($imagen != null) {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($imagen);
                    $image = $image->encode(new AutoEncoder(quality: 50));

                    $id = $usuario->id;
                    $nombre = removeAccents(Str::lower($usuario->name));
                    $extension = $imagen->getClientOriginalExtension();
                    $nombre_imagen = $id . '_' . $nombre . '.' . $extension;
                    $image->save('storage/portadas/' . $nombre_imagen);
                    $usuario->portada = $nombre_imagen;
                } else {
                    $usuario->portada = 'no_portada.jpg';
                }
                $usuario->save();

            DB::commit();

                return response()->json([
                    'message' => 'Su imagen de portada fue actualizada exitosamente.',
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return $e->getMessage();
            }
        }
    }
}
