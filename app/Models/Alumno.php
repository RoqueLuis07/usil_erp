<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Alumno extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Sexo(){
        return $this->belongsTo(Sexo::class);
    }

    public function Nacionalidades(){
        return $this->belongsToMany(Nacionalidad::class, 'alumnos_nacionalidades');
    }

    public function Formacion(){
        return $this->belongsTo(AlumnoFormacion::class);
    }

    public function InstitucionEducativa(){
        return $this->belongsTo(InstitucionEducativa::class);
    }

    public function Departamento(){
        return $this->belongsTo(DepartamentoParaguay::class);
    }

    public function Ciudad(){
        return $this->belongsTo(Ciudad::class);
    }

    public function Barrio(){
        return $this->belongsTo(Barrio::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    /**
     * Semestre (nivel) que está cursando, calculado desde el período de
     * ingreso: cada período académico transcurrido suma un semestre (enero-
     * julio = 1.er período, agosto-diciembre = 2.º, igual que el resto del
     * módulo de extensión), con tope en la duración de la carrera.
     */
    public function getSemestreActualAttribute(): ?int
    {
        return $this->semestreEn(now());
    }

    /** Semestre (nivel) que cursaba en una fecha dada (p. ej. el inicio de una actividad). */
    public function semestreEn($fecha): ?int
    {
        if (!$this->anho_ingreso || !$fecha) {
            return null;
        }

        $fecha = \Carbon\Carbon::parse($fecha);
        $periodoActual = $fecha->month <= 7 ? 1 : 2;
        $periodoIngreso = $this->semestre_ingreso ?: 1;
        $nivel = (($fecha->year - $this->anho_ingreso) * 2) + ($periodoActual - $periodoIngreso) + 1;

        if ($nivel < 1) {
            return null;
        }

        $duracion = optional($this->Carrera)->cantidad_semestres;
        return $duracion ? min($nivel, (int) $duracion) : $nivel;
    }

    public function getIngresoTextoAttribute(): ?string
    {
        return $this->anho_ingreso ? $this->anho_ingreso . '-' . ($this->semestre_ingreso ?: 1) : null;
    }

    /**
     * Datos mínimos para poder participar de un proyecto de extensión:
     * carrera (de la que sale la facultad) y período de ingreso (del que
     * sale el semestre).
     */
    public function tieneDatosParaExtension(): bool
    {
        return (bool) ($this->carrera_id && $this->anho_ingreso);
    }

    public function FormaConocimiento(){
        return $this->belongsTo(FormaConocimiento::class);
    }

    public function FamiliarUno(){
        return $this->belongsTo(AlumnoFamiliar::class);
    }

    public function FamiliarDos(){
        return $this->belongsTo(AlumnoFamiliar::class);
    }

    public function DatoLaboral(){
        return $this->belongsTo(AlumnoDatoLaboral::class);
    }

    public function Usuario(){
        return $this->belongsTo(User::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Matriculaciones(){
        return $this->hasMany(Matriculacion::class, 'alumno_id');
    }

    public function AlumnoPuntajes(){
        return $this->hasMany(AlumnoPuntaje::class, 'alumno_id');
    }

    public function AlumnoAsistencias(){
        return $this->hasMany(AlumnoAsistencia::class, 'alumno_id');
    }

    public function AlumnoNotas(){
        return $this->hasMany(AlumnoNota::class, 'alumno_id');
    }

    public function AlumnoClientes(){
        return $this->hasMany(AlumnoCliente::class, 'alumno_id');
    }

    public function ActaEvaluacionAlumno(){
        return $this->hasMany(ActaEvaluacionAlumno::class, 'alumno_id');
    }

    public function extensionesDetalles(){
        return $this->hasMany(ExtensionUniversitariaDetalle::class, 'alumno_id');
    }

    public function Solicitudes(){
        return $this->hasMany(Solicitud::class);
    }

    public function PagosUbs(){
        return $this->hasMany(PagoInscripcionUbs::class, 'alumno_id');
    }

    public function InscripcionesUbs(){
        return $this->hasMany(InscripcionUbs::class, 'alumno_id');
    }

    public function InscripcionesModulos(){
        return $this->hasMany(InscripcionModulo::class, 'alumno_id');
    }

    public function PuntajesUbs(){
        return $this->hasMany(AlumnoPuntajeUbs::class, 'alumno_id');
    }

    public function AsistenciasUbs(){
        return $this->hasMany(AlumnoAsistenciaUbs::class);
    }

    public function NotasUbs(){
        return $this->hasMany(AlumnoNotaUbs::class);
    }

    public function ActaEvaluacionAlumnoUbs(){
        return $this->hasMany(ActaEvaluacionAlumnoUbs::class, 'alumno_id');
    }

}
