<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Catálogos base que el sistema original dejaba vacíos (sin UI para algunos:
 * sexos, niveles académicos, relaciones familiares) y de los que dependen
 * los formularios de alumno/docente, la carga masiva y los reportes.
 * Idempotente: solo inserta lo que todavía no existe (por nombre).
 * Nombres en mayúsculas y sin tildes, como el resto del sistema.
 */
class CatalogosBaseSeeder extends Seeder
{
    public function run(): void
    {
        $ahora = now();

        $this->insertarSiNoExiste('sexos', ['MASCULINO', 'FEMENINO'], [], $ahora);

        $this->insertarSiNoExiste('nacionalidades', [
            'PARAGUAYA', 'ARGENTINA', 'BRASILERA', 'URUGUAYA', 'BOLIVIANA', 'CHILENA', 'PERUANA',
            'COLOMBIANA', 'VENEZOLANA', 'ECUATORIANA', 'ESPANOLA', 'ITALIANA', 'ALEMANA',
            'ESTADOUNIDENSE', 'JAPONESA', 'COREANA', 'CHINA', 'OTRA',
        ], [], $ahora);

        $this->insertarSiNoExiste('relaciones_familiares', [
            'PADRE', 'MADRE', 'HERMANO/A', 'CONYUGE', 'ABUELO/A', 'TIO/A', 'TUTOR LEGAL', 'OTRO',
        ], [], $ahora);

        $this->insertarSiNoExiste('alumnos_formaciones', [
            'BACHILLER', 'TECNICO SUPERIOR', 'GRADO UNIVERSITARIO', 'POSGRADO',
        ], [], $ahora);

        foreach ([
            'CIENCIAS EXACTAS Y NATURALES' => 'CEN',
            'CIENCIAS DE LA SALUD' => 'CSA',
            'TECNOLOGIA E INGENIERIA' => 'TIN',
            'CIENCIAS SOCIALES' => 'CSO',
            'HUMANIDADES Y EDUCACION' => 'HED',
            'ARTES Y DISENO' => 'ARD',
            'ECONOMIA Y ADMINISTRACION' => 'EAD',
        ] as $nombre => $abreviatura) {
            if (!DB::table('areas_conocimientos')->where('nombre', $nombre)->exists()) {
                DB::table('areas_conocimientos')->insert(['nombre' => $nombre, 'abreviatura' => $abreviatura, 'created_at' => $ahora, 'updated_at' => $ahora]);
            }
        }

        // Departamentos de Paraguay con su capital, y las ciudades principales.
        $departamentos = [
            'ASUNCION' => ['ASUNCION', []],
            'CONCEPCION' => ['CONCEPCION', ['HORQUETA', 'LORETO']],
            'SAN PEDRO' => ['SAN PEDRO DEL YCUAMANDYYU', ['SANTA ROSA DEL AGUARAY']],
            'CORDILLERA' => ['CAACUPE', ['ALTOS', 'EUSEBIO AYALA']],
            'GUAIRA' => ['VILLARRICA', []],
            'CAAGUAZU' => ['CORONEL OVIEDO', ['CAAGUAZU', 'REPATRIACION']],
            'CAAZAPA' => ['CAAZAPA', []],
            'ITAPUA' => ['ENCARNACION', ['HOHENAU', 'OBLIGADO']],
            'MISIONES' => ['SAN JUAN BAUTISTA', ['AYOLAS']],
            'PARAGUARI' => ['PARAGUARI', ['YAGUARON', 'PIRIBEBUY']],
            'ALTO PARANA' => ['CIUDAD DEL ESTE', ['PRESIDENTE FRANCO', 'HERNANDARIAS', 'MINGA GUAZU']],
            'CENTRAL' => ['AREGUA', ['LUQUE', 'SAN LORENZO', 'LAMBARE', 'FERNANDO DE LA MORA', 'CAPIATA', 'NEMBY', 'LIMPIO', 'MARIANO ROQUE ALONSO', 'VILLA ELISA', 'ITAUGUA', 'ITA', 'YPANE', 'GUARAMBARE', 'SAN ANTONIO', 'VILLETA']],
            'NEEMBUCU' => ['PILAR', []],
            'AMAMBAY' => ['PEDRO JUAN CABALLERO', []],
            'CANINDEYU' => ['SALTO DEL GUAIRA', []],
            'PRESIDENTE HAYES' => ['VILLA HAYES', []],
            'BOQUERON' => ['FILADELFIA', []],
            'ALTO PARAGUAY' => ['FUERTE OLIMPO', []],
        ];

        foreach ($departamentos as $departamento => [$capital, $otras]) {
            $depId = DB::table('departamentos_paraguay')->where('nombre', $departamento)->value('id');
            if (!$depId) {
                $depId = DB::table('departamentos_paraguay')->insertGetId([
                    'nombre' => $departamento, 'capital' => $capital, 'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }
            foreach (array_merge([$capital], $otras) as $ciudad) {
                if (!DB::table('ciudades')->where('nombre', $ciudad)->where('departamento_id', $depId)->exists()) {
                    DB::table('ciudades')->insert(['nombre' => $ciudad, 'departamento_id' => $depId, 'created_at' => $ahora, 'updated_at' => $ahora]);
                }
            }
        }

        // Los niveles académicos exigen un usuario que los "cargue"; si todavía
        // no hay ninguno (migración sobre base vacía) se siembran luego, desde
        // DatabaseSeeder.
        $cargadoPor = DB::table('usuarios')->orderBy('id')->value('id');
        if ($cargadoPor) {
            foreach (['LICENCIADO', 'ESPECIALIZACION', 'MAESTRIA', 'DOCTORADO'] as $nivel) {
                if (!DB::table('docentes_niveles_academicos')->where('nombre', $nivel)->exists()) {
                    DB::table('docentes_niveles_academicos')->insert([
                        'nombre' => $nivel, 'honorario' => 0, 'cargado_por_id' => $cargadoPor,
                        'estado' => 'AC', 'created_at' => $ahora, 'updated_at' => $ahora,
                    ]);
                }
            }
        }
    }

    private function insertarSiNoExiste(string $tabla, array $nombres, array $extra, $ahora): void
    {
        foreach ($nombres as $nombre) {
            if (!DB::table($tabla)->where('nombre', $nombre)->exists()) {
                DB::table($tabla)->insert(array_merge(['nombre' => $nombre, 'created_at' => $ahora, 'updated_at' => $ahora], $extra));
            }
        }
    }
}
