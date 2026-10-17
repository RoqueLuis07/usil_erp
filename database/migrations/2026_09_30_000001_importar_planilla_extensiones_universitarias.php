<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Alumno;
use App\Models\AlumnoExtension;
use App\Models\Carrera;
use App\Models\ExtensionUniversitaria;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\TipoExtensionUniversitaria;
use App\Models\User;

/**
 * Importa la planilla histórica de Extensión Universitaria (Extenciones2.xlsx)
 * que el usuario compartió: actividades, alumnos y su participación.
 *
 * Los datos ya vienen limpios (encoding reparado, actividades con errores de
 * tipeo agrupadas, nombres inválidos descartados) en database/data — ese
 * trabajo se hizo aparte con un script en Python, revisado con el usuario
 * antes de escribir esto.
 *
 * Deliberadamente NO incluye las filas de "Ingeniería Electromecánica"
 * (2088 de 3948) porque esa carrera todavía no existe en el sistema; el
 * usuario pidió omitirlas por ahora. Se pueden cargar después con la misma
 * mecánica una vez que la carrera exista.
 *
 * Los alumnos se crean solo como registro de participación (sin cuenta de
 * usuario ni documento real: la planilla no trae documento, así que se usa
 * un número sintético "EXT-IMP-00001" claramente identificable como tal).
 */
return new class extends Migration
{
    public function up(): void
    {
        $rutaJson = database_path('data/extensiones_universitarias_2026.json');
        if (!file_exists($rutaJson)) {
            return;
        }
        $datos = json_decode(file_get_contents($rutaJson), true);
        if (!is_array($datos) || empty($datos['actividades'])) {
            return;
        }

        // Si esta importación ya corrió antes (re-despliegue), no duplicar.
        if (Alumno::where('numero_documento', 'like', 'EXT-IMP-%')->exists()) {
            return;
        }

        $usuarioSistema = User::orderBy('id')->first();
        if (!$usuarioSistema) {
            return;
        }

        $carreraInformatica = Carrera::where('estado', 'AC')->get()
            ->first(fn ($c) => Str::contains(Str::upper(removeAccents($c->nombre_fantasia ?? '')), 'INFORMATICA')
                || Str::contains(Str::upper(removeAccents($c->nombre_real ?? '')), 'INFORMATICA'));

        $tipos = TipoExtensionUniversitaria::get();
        $tipoVoluntariado = $tipos->first(fn ($t) => Str::contains(Str::upper(removeAccents($t->nombre ?? '')), 'VOLUNTARIADO'));
        $tipoAcademica = $tipos->first(fn ($t) => Str::contains(Str::upper(removeAccents($t->nombre ?? '')), 'ACADEMICA'))
            ?: $tipos->first();

        if (!$tipoAcademica) {
            return; // sin ningún tipo de extensión configurado, no se puede importar
        }

        DB::transaction(function () use ($datos, $usuarioSistema, $carreraInformatica, $tipoVoluntariado, $tipoAcademica) {
            // 1) Actividades (extensiones_universitarias)
            $actividadIdPorNombre = [];
            foreach ($datos['actividades'] as $act) {
                $tipoId = ($act['tipo'] === 'Voluntariado' && $tipoVoluntariado) ? $tipoVoluntariado->id : $tipoAcademica->id;

                $ext = new ExtensionUniversitaria();
                $ext->nombre = Str::limit($act['nombre'], 190, '');
                $ext->cantidad_horas = $act['cantidad_horas'] ?: 1;
                $ext->tipo_extension_id = $tipoId;
                $ext->fecha_inicio = $act['fecha_inicio'] ?? null;
                $ext->fecha_fin = $act['fecha_fin'] ?? $act['fecha_inicio'] ?? null;
                $ext->ubicacion_proyecto = 'importado-sin-archivo';
                $ext->extension_proyecto = '';
                $ext->tiene_certificado = false;
                $ext->estado = 'FI'; // ya sucedió: horas finalizadas, igual que "Finalizar" a mano
                $ext->cargado_por_id = $usuarioSistema->id;
                $ext->save();

                $actividadIdPorNombre[$act['nombre']] = $ext->id;
            }

            // 2) Alumnos (participación, sin cuenta de usuario)
            $alumnoIds = [];
            $numero = 1;
            foreach ($datos['alumnos'] as $i => $al) {
                $alumno = new Alumno();
                $alumno->primer_nombre = Str::limit($al['nombres'], 190, '');
                $alumno->primer_apellido = Str::limit($al['apellidos'], 190, '');
                $alumno->numero_documento = 'EXT-IMP-' . str_pad((string) $numero, 5, '0', STR_PAD_LEFT);
                $alumno->celular = $al['celular'] ?: '';
                $alumno->email_personal = $al['correo'] ?: '';
                if ($al['carrera'] === 'informatica' && $carreraInformatica) {
                    $alumno->carrera_id = $carreraInformatica->id;
                }
                $alumno->anho_ingreso = $al['anho_ingreso'] ?? null;
                $alumno->semestre_ingreso = $al['semestre_ingreso'] ?? null;
                $alumno->estado = 'AC';
                $alumno->ubs = false;
                $alumno->cargado_por_id = $usuarioSistema->id;
                $alumno->observaciones = 'Importado desde planilla histórica de Extensión Universitaria (Excel).';
                $alumno->save();

                $alumnoIds[$i] = $alumno->id;
                $numero++;
            }

            // 3) Detalles de participación + acumulado de horas por alumno/tipo
            $acumulado = []; // "alumnoId-tipoId" => ['horas' => x, 'actividades' => n]
            foreach ($datos['detalles'] as $d) {
                $actId = $actividadIdPorNombre[$d['actividad']] ?? null;
                $alumnoId = $alumnoIds[$d['alumno']] ?? null;
                if (!$actId || !$alumnoId) {
                    continue;
                }

                $detalle = new ExtensionUniversitariaDetalle();
                $detalle->extension_universitaria_id = $actId;
                $detalle->alumno_id = $alumnoId;
                $detalle->carrera_id = ($datos['alumnos'][$d['alumno']]['carrera'] === 'informatica' && $carreraInformatica)
                    ? $carreraInformatica->id : null;
                $detalle->cantidad_horas = $d['horas'] ?? null;
                $detalle->estado = 'AC';
                $detalle->save();

                if ($d['horas']) {
                    $tipoId = ExtensionUniversitaria::find($actId)->tipo_extension_id;
                    $clave = $alumnoId . '-' . $tipoId;
                    if (!isset($acumulado[$clave])) {
                        $acumulado[$clave] = ['alumno_id' => $alumnoId, 'tipo_extension_id' => $tipoId, 'horas' => 0, 'actividades' => 0];
                    }
                    $acumulado[$clave]['horas'] += $d['horas'];
                    $acumulado[$clave]['actividades']++;
                }
            }

            foreach ($acumulado as $fila) {
                $alumnoExtension = AlumnoExtension::where('alumno_id', $fila['alumno_id'])
                    ->where('tipo_extension_id', $fila['tipo_extension_id'])
                    ->first() ?: new AlumnoExtension();
                $alumnoExtension->alumno_id = $fila['alumno_id'];
                $alumnoExtension->tipo_extension_id = $fila['tipo_extension_id'];
                $alumnoExtension->cantidad_actividades = ($alumnoExtension->exists ? $alumnoExtension->cantidad_actividades : 0) + $fila['actividades'];
                $alumnoExtension->cantidad_horas = ($alumnoExtension->exists ? $alumnoExtension->cantidad_horas : 0) + $fila['horas'];
                $alumnoExtension->save();
            }
        });
    }

    public function down(): void
    {
        // Borrado dirigido: solo lo que trajo esta importación (numero_documento
        // con el prefijo sintético), no toca nada cargado a mano.
        $ids = Alumno::where('numero_documento', 'like', 'EXT-IMP-%')->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }
        DB::table('alumnos_extensiones')->whereIn('alumno_id', $ids)->delete();
        DB::table('extensiones_universitarias_detalles')->whereIn('alumno_id', $ids)->delete();
        DB::table('alumnos')->whereIn('id', $ids)->delete();
        DB::table('extensiones_universitarias')->where('ubicacion_proyecto', 'importado-sin-archivo')->delete();
    }
};
