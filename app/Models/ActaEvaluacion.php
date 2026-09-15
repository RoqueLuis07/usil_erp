<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ActaEvaluacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "actas_evaluaciones";// <-- El nombre personalizado

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function GeneradoPor(){
        return $this->belongsTo(User::class);
    }

    public function Alumnos(){
        return $this->hasMany(ActaEvaluacionAlumno::class);
    }

}
