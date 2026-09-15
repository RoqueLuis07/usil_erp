<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Inscripcion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones";// <-- El nombre personalizado

    public function Matriculacion(){
        return $this->belongsTo(Matriculacion::class, 'matriculacion_id');
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function SemestreMallaMaterias(){
        return $this->belongsToMany(SemestreMallaMateria::class, 'inscripciones_semestres_mallas_materias', 'inscripcion_id', 'semestre_malla_materia_id');
    }
}
