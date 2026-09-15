<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;


class EmpresaController extends Controller
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
        $this->authorize('editar_empresa');

        try {
            $empresa = Empresa::first();
            $paises = Pais::get();
            $ciudades = Ciudad::get();

            if ($empresa) {
                return view('empresas/edit')->with(compact('empresa', 'ciudades', 'paises'));
            } else {
                return view('empresas/create')->with(compact('ciudades', 'paises'));
            }
        } catch (\Exception $e) {
            return redirect()->route('empresas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('editar_empresa');

        $request->validate([
            'nombre_fantasia' => 'required',
            'razon_social' => 'required',
            'ruc' => ['required', 'regex:/^[0-9]{1,10}-[0-9]{1}?$/'],
            'actividad' => 'required',
            'check_telefono' => 'required',
            'telefono_celular' => 'required_if:check_telefono,1',
            'telefono_linea_baja' => 'required_if:check_telefono,2',
            'email' => ['required', 'email'],
            'direccion' => 'required',
            'pais' => ['numeric', 'required'],
            'ciudad' => ['numeric', 'required'],
            'logo' => ['nullable', 'extensions:jpg,png']
        ]);

        DB::beginTransaction();

        try {
            $empresa = new Empresa();
            $empresa->razon_social = removeAccents(Str::upper($request->razon_social));
            $empresa->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $empresa->ruc = $request->ruc;
            $empresa->actividad = removeAccents(Str::upper($request->actividad));
            if ($request->telefono_celular == null) {
                $empresa->telefono = $request->telefono_linea_baja;
            } else {
                $empresa->telefono = $request->telefono_celular;
            }
            $empresa->email = removeAccents(Str::lower($request->email));
            $empresa->direccion = removeAccents(Str::upper($request->direccion));
            $empresa->pais_id = $request->pais;
            $empresa->ciudad_id = $request->ciudad;

            //cargar imagen
            $imagen = $request->logo;
            if ($imagen != null) {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($imagen);
                $image = $image->encode(new AutoEncoder(quality: 50));

                $nombre = removeAccents(Str::lower($empresa->razon_social));
                $extension = $imagen->getClientOriginalExtension();
                $nombre_imagen = $nombre . '.' . $extension;
                $image->save('storage/empresa/' . $nombre_imagen);
                $empresa->logo = $nombre_imagen;
            } else {
                $empresa->logo = 'no_image.png';
            }
            $empresa->save();

            DB::commit();

            return redirect()->route('empresas.index')->with('success-message', 'Los datos de la empresa fueron guardados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('empresas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->authorize('editar_empresa');

        $request->validate([
            'nombre_fantasia' => 'required',
            'razon_social' => 'required',
            'ruc' => ['required', 'regex:/^[0-9]{1,10}-[0-9]{1}?$/'],
            'actividad' => 'required',
            'check_telefono' => 'required',
            'telefono_celular' => 'required_if:check_telefono,1',
            'telefono_linea_baja' => 'required_if:check_telefono,2',
            'email' => ['required', 'email'],
            'direccion' => 'required',
            'pais' => ['numeric', 'required'],
            'ciudad' => ['numeric', 'required'],
        ]);

        DB::beginTransaction();

        try {
            $empresa = Empresa::first();
            $empresa->razon_social = removeAccents(Str::upper($request->razon_social));
            $empresa->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $empresa->ruc = $request->ruc;
            $empresa->actividad = removeAccents(Str::upper($request->actividad));
            if ($request->telefono_celular == null) {
                $empresa->telefono = $request->telefono_linea_baja;
            } else {
                $empresa->telefono = $request->telefono_celular;
            }
            $empresa->email = removeAccents(Str::lower($request->email));
            $empresa->direccion = removeAccents(Str::upper($request->direccion));
            $empresa->pais_id = $request->pais;
            $empresa->ciudad_id = $request->ciudad;

            //eliminar y cargar imagen
            $imagen = $request->logo;
            if ($imagen != null) {
                if ($empresa->logo != 'no_image.png') {
                    $ubicacion_y_nombre = 'storage/empresa/' . $empresa->logo;
                    File::delete($ubicacion_y_nombre);
                }

                $manager = new ImageManager(Driver::class);
                $image = $manager->read($imagen);
                $image = $image->encode(new AutoEncoder(quality: 50));

                $nombre = removeAccents(Str::lower($empresa->razon_social));
                $extension = $imagen->getClientOriginalExtension();
                $nombre_imagen = $nombre . '.' . $extension;
                $image->save('storage/empresa/' . $nombre_imagen);
                $empresa->logo = $nombre_imagen;
            } else {
                if ($request->logo_delete == 'logo-borrado') {
                    if ($empresa->logo != 'no_image.png') {
                        $ubicacion_y_nombre = 'storage/empresa/' . $empresa->logo;
                        File::delete($ubicacion_y_nombre);
                    }
                    $empresa->logo = 'no_image.png';
                }
            }
            $empresa->save();

            DB::commit();

            return redirect()->route('empresas.index')->with('success-message', 'Los datos de la empresa fueron actualizados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('empresas.index')->with('error-message', $e->getMessage());
        }
    }
}
