<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaActaEvaluacionAlumno extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_actas_evaluaciones_alumnos";// <-- El nombre personalizado

    public function ActaEvaluacion(){
        return $this->belongsTo(TutoriaActaEvaluacion::class, 'acta_evaluacion_id');
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
