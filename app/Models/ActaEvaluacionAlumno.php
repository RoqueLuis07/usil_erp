<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ActaEvaluacionAlumno extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "actas_evaluaciones_alumnos";// <-- El nombre personalizado

    public function ActaEvaluacion(){
        return $this->belongsTo(ActaEvaluacion::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
