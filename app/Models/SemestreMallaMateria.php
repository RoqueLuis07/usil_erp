<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SemestreMallaMateria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "semestres_mallas_materias";// <-- El nombre personalizado

    public function SemestreMalla(){
        return $this->belongsTo(SemestreMalla::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function SemestreMallaMateriaHorarios(){
        return $this->hasMany(SemestreMallaMateriaHorario::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Inscripciones(){
        return $this->belongsToMany(Inscripción::class, 'inscripcion_semestre_malla_materia', 'semestre_malla_materia_id', 'inscripcion_id');
    }

    public function Horarios(){
        return $this->hasMany(SemestreMallaMateriaHorario::class, 'semestre_malla_materia_id');
    }

}
