<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Tutoria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Horarios(){
        return $this->hasMany(TutoriaHorario::class);
    }

    public function Alumnos(){
        return $this->hasMany(TutoriaAlumno::class);
    }

    public function Clases(){
        return $this->hasMany(TutoriaClase::class);
    }

    public function Evaluaciones(){
        return $this->hasMany(TutoriaEvalaucion::class);
    }

    public function Acta(){
        return $this->hasOne(TutoriaActaEvaluacion::class);
    }
}
