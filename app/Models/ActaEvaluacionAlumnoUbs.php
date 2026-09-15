<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ActaEvaluacionAlumnoUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "actas_evaluaciones_alumnos_ubs";// <-- El nombre personalizado

    public function Acta(){
        return $this->belongsTo(ActaEvaluacionUbs::class, 'acta_evaluacion_id');
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
