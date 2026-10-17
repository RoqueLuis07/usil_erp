<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Facultades y carreras de la Universidad Nihon Gakko (listado entregado por la institución), necesarias
 * para que la carga masiva de alumnos y actividades pueda asignar carrera y facultad.
 *
 * Idempotente: una facultad o carrera que ya existe (comparando sin tildes ni mayúsculas) no se toca ni se duplica.
 * "nombre_fantasia" es el nombre corto con el que figura en la base de estudiantes y en el sistema anterior;
 * "nombre_real" es la denominación oficial. El número de ley/acta/resolución CONES queda vacío para completarlo luego.
 */
return new class extends Migration
{
    /** facultad => [[nombre_fantasia, nombre_real, abreviatura, cantidad_semestres], ...] */
    private const ESTRUCTURA = [
        'Facultad de Humanidades y Ciencias de la Educación' => [
            ['Ciencias de la Educación', 'Licenciatura en Ciencias de la Educación', 'CED', 8],
            ['Psicología', 'Licenciatura en Psicología Clínica', 'PSI', 10],
            ['Ciencias del Deporte', 'Licenciatura en Ciencias del Deporte', 'CDE', 8],
            ['Educación Inicial', 'Licenciatura en Educación Inicial', 'EIN', 8],
            ['Educación Escolar Básica', 'Licenciatura en Educación Escolar Básica', 'EEB', 9],
        ],
        'Facultad de Derecho y Ciencias Sociales' => [
            ['Derecho', 'Derecho', 'DER', 10],
            ['Notariado', 'Notariado', 'NOT', 8],
        ],
        'Facultad de Ciencias y Tecnología' => [
            ['Ingeniería en Informática', 'Ingeniería Informática', 'INF', 10],
            ['Ingeniería Electromecánica', 'Ingeniería Electromecánica', 'IEM', 10],
            ['Diseño Gráfico', 'Licenciatura en Diseño Gráfico', 'DIG', 10],
            ['Ingeniería Agronómica', 'Ingeniería Agronómica', 'AGR', 10],
        ],
        'Facultad de Ciencias Empresariales' => [
            ['Ciencias Contables', 'Licenciatura en Ciencias Contables', 'CCO', 8],
            ['Administración de Empresas', 'Licenciatura en Administración de Empresas', 'ADE', 8],
            ['Ingeniería Comercial', 'Ingeniería Comercial', 'ICO', 10],
        ],
    ];

    public function up(): void
    {
        $cargadoPor = DB::table('usuarios')->orderBy('id')->value('id');
        if (!$cargadoPor) {
            return; // base vacía: se carga desde el seeder cuando exista el primer usuario
        }
        $ahora = now();

        $programaId = $this->buscarOCrear('programas', 'GRADO', [
            'cantidad_creditos' => 0, 'duracion' => 5, 'turno' => 'M', 'cargado_por_id' => $cargadoPor, 'estado' => 'AC',
        ], $ahora);
        $tipoId = $this->buscarOCrear('tipos_carreras', 'GRADO', ['cargado_por_id' => $cargadoPor, 'estado' => 'AC'], $ahora);
        $modalidadId = $this->buscarOCrear('modalidades', 'PRESENCIAL', ['estado' => 'AC'], $ahora);

        $carrerasExistentes = DB::table('carreras')->get();

        foreach (self::ESTRUCTURA as $nombreFacultad => $carreras) {
            $facultad = DB::table('facultades')->get()->first(fn ($f) => $this->clave($f->nombre) === $this->clave($nombreFacultad));
            $facultadId = $facultad
                ? $facultad->id
                : DB::table('facultades')->insertGetId([
                    'nombre' => $nombreFacultad, 'cargado_por_id' => $cargadoPor, 'estado' => 'AC',
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);

            foreach ($carreras as [$fantasia, $real, $abreviatura, $semestres]) {
                $existe = $carrerasExistentes->first(fn ($c) => in_array($this->clave($fantasia), [$this->clave($c->nombre_fantasia), $this->clave($c->nombre_real)], true)
                    || in_array($this->clave($real), [$this->clave($c->nombre_fantasia), $this->clave($c->nombre_real)], true));
                if ($existe) {
                    continue;
                }
                DB::table('carreras')->insert([
                    'nombre_fantasia' => $fantasia, 'nombre_real' => $real, 'abreviatura' => $abreviatura,
                    'programa_id' => $programaId, 'facultad_id' => $facultadId, 'tipo_carrera_id' => $tipoId,
                    'doble_grado' => false, 'cantidad_semestres' => $semestres, 'modalidad_id' => $modalidadId,
                    'cargado_por_id' => $cargadoPor, 'estado' => 'AC', 'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No se borra nada: las carreras pueden tener alumnos y actividades asociados.
    }

    private function buscarOCrear(string $tabla, string $nombre, array $extra, $ahora): int
    {
        $existente = DB::table($tabla)->get()->first(fn ($f) => $this->clave($f->nombre) === $this->clave($nombre));
        if ($existente) {
            return $existente->id;
        }
        return DB::table($tabla)->insertGetId(array_merge(['nombre' => $nombre, 'created_at' => $ahora, 'updated_at' => $ahora], $extra));
    }

    private function clave(?string $texto): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii((string) $texto)));
    }
};
