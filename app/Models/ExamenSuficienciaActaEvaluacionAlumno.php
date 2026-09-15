<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExamenSuficienciaActaEvaluacionAlumno extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "examenes_suficiencias_actas_evaluaciones_alumnos";// <-- El nombre personalizado

    public function ActaEvaluacion(){
        return $this->belongsTo(ExamenSuficienciaActaEvaluacion::class, 'acta_evaluacion_id');
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
