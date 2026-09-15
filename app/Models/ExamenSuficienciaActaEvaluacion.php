<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExamenSuficienciaActaEvaluacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "examenes_suficiencias_actas_evaluaciones";// <-- El nombre personalizado

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function GeneradoPor(){
        return $this->belongsTo(User::class);
    }

    public function Alumnos(){
        return $this->hasMany(ExamenSuficienciaActaEvaluacionAlumno::class, 'acta_evaluacion_id');
    }

}
