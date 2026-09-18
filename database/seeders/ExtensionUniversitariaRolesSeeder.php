<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\RequerimientoExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;

/**
 * Roles con permisos acotados (en vez del único SUPERADMIN que trae el
 * sistema original), enfocado en el módulo de Extensión Universitaria:
 * Alumno, Encargado Docente (dueño/responsable del proyecto) y
 * Administrador de Extensión (coordinación central). El cuarto rol del
 * modelo objetivo, Docente Tutor de Apoyo, se suma recién en la fase que
 * construya asistencia por jornada — hoy no tiene ninguna pantalla propia
 * que lo justifique.
 */
class ExtensionUniversitariaRolesSeeder extends Seeder
{
    public function run(): void
    {
        $cargadoPorId = optional(User::first())->id ?? 1;

        // Requisito de graduación (fila única global) y al menos un tipo de
        // actividad activo — sin esto, la pantalla de extensión del alumno
        // truena (RequerimientoExtensionUniversitaria::first() sin filas) y
        // el formulario de alta del docente no tiene qué ofrecer en "Tipo
        // de actividad".
        if (RequerimientoExtensionUniversitaria::count() === 0) {
            $requerimiento = new RequerimientoExtensionUniversitaria();
            $requerimiento->actividades_requeridas = 2;
            $requerimiento->horas_requeridas = 40;
            $requerimiento->cargado_por_id = $cargadoPorId;
            $requerimiento->save();
        }

        if (TipoExtensionUniversitaria::count() === 0) {
            foreach (['Voluntariado', 'Extensión académica'] as $nombre) {
                $tipo = new TipoExtensionUniversitaria();
                $tipo->nombre = $nombre;
                $tipo->maxima_cantidad_horas = 40;
                $tipo->cargado_por_id = $cargadoPorId;
                $tipo->save();
            }
        }

        $alumnoRole = Role::firstOrCreate(
            ['name' => 'ALUMNO', 'guard_name' => 'web'],
            ['state' => 'AC']
        );
        $alumnoRole->syncPermissions([
            'ver_dashboard_alumnos_pantalla',
            'ver_extensiones_alumnos_pantalla',
            'ver_catalogo_extensiones_alumnos_pantalla',
            'postular_extensiones_alumnos_pantalla',
            'ver_noticias_avisos_alumnos_pantalla',
        ]);

        // Encargado Docente: el docente que diseña y es responsable de un
        // proyecto de extensión propio (carga el proyecto, gestiona sus
        // postulaciones e informes). No confundir con el futuro rol de
        // Docente Tutor de Apoyo, que solo acompaña un proyecto ajeno.
        $docenteRole = Role::firstOrCreate(
            ['name' => 'ENCARGADO_DOCENTE', 'guard_name' => 'web'],
            ['state' => 'AC']
        );
        $docenteRole->syncPermissions([
            'ver_dashboard_docentes_pantalla',
            'ver_extensiones_docentes_pantalla',
            'crear_extensiones_docentes_pantalla',
            'crear_extensiones_universitarias',
            'gestionar_postulaciones_extensiones_universitarias',
            'cargar_informes_extensiones_docentes_pantalla',
            'cambiar_informes_extensiones_docentes_pantalla',
            'cambiar_proyectos_extensiones_docentes_pantalla',
            'ver_noticias_avisos_docentes_pantalla',
        ]);

        // Administrador de Extensión: coordinación central — gestiona el
        // ciclo de vida completo de las actividades de extensión, y puede
        // cargar los datos base de alumnos/docentes y darles de alta su
        // usuario con el rol que corresponda. No incluye eliminar_* (bajas
        // duras) ni ver_roles/editar_roles (redefinir qué puede hacer cada
        // rol queda reservado a SUPERADMIN) para no habilitar escalamiento
        // de privilegios desde este rol.
        $encargadoRole = Role::firstOrCreate(
            ['name' => 'ADMINISTRADOR_EXTENSION', 'guard_name' => 'web'],
            ['state' => 'AC']
        );
        $encargadoRole->syncPermissions([
            'gestionar_postulaciones_extensiones_universitarias',
            // Extensión universitaria: ciclo completo
            'ver_extensiones_universitarias',
            'crear_extensiones_universitarias',
            'editar_extensiones_universitarias',
            'aprobar_extensiones_universitarias',
            'rechazar_extensiones_universitarias',
            'anular_aprobacion_extensiones_universitarias',
            'anular_rechazo_extensiones_universitarias',
            'finalizar_extensiones_universitarias',
            'cargar_informes_extensiones_universitarias',
            'cambiar_adjunto_proyectos_extensiones_universitarias',
            'cambiar_adjunto_informes_extensiones_universitarias',
            'ver_adjunto_proyectos_extensiones_universitarias',
            'ver_adjunto_informes_extensiones_universitarias',
            'editar_horas_alumnos_extensiones_universitarias',
            'ver_requerimientos_extensiones_universitarias',
            'editar_requerimientos_extensiones_universitarias',
            'ver_tipos_extensiones_universitarias',
            'crear_tipos_extensiones_universitarias',
            'editar_tipos_extensiones_universitarias',
            'activar_tipos_extensiones_universitarias',
            'inactivar_tipos_extensiones_universitarias',
            'generar_reportes_extensiones_universitarias',
            'generar_reportes_extensiones_universitarias_carrera_semestre',
            // Datos base: alumnos
            'ver_alumnos',
            'crear_alumnos',
            'editar_alumnos',
            'activar_alumnos',
            'inactivar_alumnos',
            'ver_legajos_alumnos',
            'subir_legajos_alumnos',
            // Datos base: docentes
            'ver_docentes',
            'crear_docentes',
            'editar_docentes',
            'activar_docentes',
            'inactivar_docentes',
            'ver_legajos_docentes',
            'subir_legajos_docentes',
            // Alta de usuarios y asignación de rol (ALUMNO/DOCENTE/
            // ENCARGADO_EXTENSION) — no de roles con más alcance, esa
            // restricción depende de qué roles existan, no de un permiso
            // propio del sistema.
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
        ]);

        // Usuarios de prueba, uno por rol, con su registro académico
        // (Alumno/Docente) ya vinculado por usuario_id — las pantallas de
        // portal (PantallaAlumnoController/PantallaDocenteController)
        // buscan ese registro por usuario_id y truenan si no existe.
        $encargado = User::firstOrCreate(
            ['email' => 'encargado.extension@example.test'],
            [
                'name' => 'Administrador de Extensión',
                'password' => Hash::make('password'),
                'role_id' => $encargadoRole->id,
                'avatar' => 'masculino.jpg',
                'portada' => 'no_portada.jpg',
                'state' => 'AC',
            ]
        );
        $encargado->syncRoles([$encargadoRole]);
        $this->crearConfiguracion($encargado->id);

        $docenteUser = User::firstOrCreate(
            ['email' => 'docente.extension@example.test'],
            [
                'name' => 'Encargado Docente de Prueba',
                'password' => Hash::make('password'),
                'role_id' => $docenteRole->id,
                'avatar' => 'masculino.jpg',
                'portada' => 'no_portada.jpg',
                'state' => 'AC',
            ]
        );
        $docenteUser->syncRoles([$docenteRole]);
        $this->crearConfiguracion($docenteUser->id);
        Docente::firstOrCreate(
            ['usuario_id' => $docenteUser->id],
            [
                'primer_nombre' => 'Docente',
                'primer_apellido' => 'De Prueba',
                'numero_documento' => '9000001',
                'celular' => '0981900001',
                'email_personal' => 'docente.extension@example.test',
                'cargado_por_id' => $cargadoPorId,
            ]
        );

        $alumnoUser = User::firstOrCreate(
            ['email' => 'alumno.extension@example.test'],
            [
                'name' => 'Alumno de Prueba',
                'password' => Hash::make('password'),
                'role_id' => $alumnoRole->id,
                'avatar' => 'masculino.jpg',
                'portada' => 'no_portada.jpg',
                'state' => 'AC',
            ]
        );
        $alumnoUser->syncRoles([$alumnoRole]);
        $this->crearConfiguracion($alumnoUser->id);
        Alumno::firstOrCreate(
            ['usuario_id' => $alumnoUser->id],
            [
                'primer_nombre' => 'Alumno',
                'primer_apellido' => 'De Prueba',
                'numero_documento' => '9000002',
                'celular' => '0981900002',
                'email_personal' => 'alumno.extension@example.test',
                'cargado_por_id' => $cargadoPorId,
            ]
        );
    }

    /**
     * Crea la fila de `configuraciones` que AppServiceProvider espera para
     * cualquier usuario autenticado (usada en cada vista vía View::composer
     * para tema/idioma). Sin esta fila, antes de este fix, cualquier
     * página rompía con "Attempt to read property lang on null".
     */
    private function crearConfiguracion(int $userId): void
    {
        DB::table('configuraciones')->insertOrIgnore([
            'user_id' => $userId,
            'lang' => 'sp',
            'data_layout' => 'vertical',
            'data_sidebar' => 'dark',
            'data_sidebar_size' => 'lg',
            'card_layout' => null,
            'data_bs_theme' => 'light',
            'data_layout_width' => 'fluid',
            'data_sidebar_image' => 'none',
            'data_layout_position' => 'fixed',
            'data_layout_style' => 'default',
            'data_topbar' => 'warning',
            'data_preloader' => 'disable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
