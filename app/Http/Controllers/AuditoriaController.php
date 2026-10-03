<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

/**
 * Panel de auditoría: lectura del historial que owen-it/laravel-auditing ya
 * viene grabando (tabla `audits`) para prácticamente todos los modelos del
 * sistema. No agrega una nueva fuente de datos, solo la hace visible para
 * poder respaldar acciones involuntarias y monitorear cambios.
 */
class AuditoriaController extends Controller
{
    private const EVENTOS = ['created' => 'Creó', 'updated' => 'Actualizó', 'deleted' => 'Eliminó', 'restored' => 'Restauró'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('ver_auditoria');

        $query = Audit::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('modulo')) {
            $query->where('auditable_type', $request->modulo);
        }
        if ($request->filled('evento')) {
            $query->where('event', $request->evento);
        }
        if ($request->filled('usuario')) {
            $query->where('user_id', $request->usuario)->where('user_type', 'App\\Models\\User');
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('buscar')) {
            $texto = $request->buscar;
            $query->where(function ($q) use ($texto) {
                $q->where('auditable_id', 'like', "%{$texto}%")
                    ->orWhere('old_values', 'like', "%{$texto}%")
                    ->orWhere('new_values', 'like', "%{$texto}%")
                    ->orWhere('url', 'like', "%{$texto}%");
            });
        }

        $auditorias = $query->paginate(50)->withQueryString();

        // Catálogo de módulos realmente presentes en la tabla (no todos los
        // 188 modelos auditables tienen movimientos todavía).
        $modulos = Audit::select('auditable_type')->distinct()->orderBy('auditable_type')->pluck('auditable_type')
            ->mapWithKeys(fn ($tipo) => [$tipo => $this->nombreModulo($tipo)]);

        $usuarios = \App\Models\User::whereIn('id', Audit::where('user_type', 'App\\Models\\User')->select('user_id')->distinct()->pluck('user_id'))
            ->orderBy('name')->get(['id', 'name']);

        $eventos = self::EVENTOS;

        return view('auditoria.index', compact('auditorias', 'modulos', 'usuarios', 'eventos'));
    }

    public function show($id)
    {
        $this->authorize('ver_auditoria');

        $auditoria = Audit::with('user')->findOrFail($id);
        $modulo = $this->nombreModulo($auditoria->auditable_type);

        $antes = $auditoria->old_values ?? [];
        $despues = $auditoria->new_values ?? [];
        $campos = array_unique(array_merge(array_keys($antes), array_keys($despues)));
        sort($campos);

        $eventos = self::EVENTOS;

        return view('auditoria.show', compact('auditoria', 'modulo', 'antes', 'despues', 'campos', 'eventos'));
    }

    /** "App\Models\ExtensionUniversitaria" -> "Extension Universitaria" */
    private function nombreModulo(string $claseCompleta): string
    {
        $base = class_basename($claseCompleta);
        return Str::headline($base);
    }
}
