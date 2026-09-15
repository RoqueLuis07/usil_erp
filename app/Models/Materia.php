<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Materia extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Correlativas(){
        return $this->belongsToMany(Materia::class, 'correlatividades', 'materia_id', 'correlativa_id')->withTimestamps();
    }

    public function EsCorrelativa(){
        return $this->belongsToMany(Materia::class, 'correlatividades', 'correlativa_id', 'materia_id')->withTimestamps();
    }

    public function Inscripciones(){
        return $this->belongsToMany(Alumno::class, 'inscripciones')
        ->withPivot('estado')
        ->withTimestamps();
    }

    public function EstaAprobadaConvalidadaEspejoSuficiencia($alumno){
        return $this->inscripciones()
                     ->where('alumno_id', $alumno)
                     ->where(function ($query) {
                         $query->where('inscripciones.estado', 'AP')
                               ->orWhere('inscripciones.estado', 'CO')
                               ->orWhere('inscripciones.estado', 'ES')
                               ->orWhere('inscripciones.estado', 'SU');
                     })
                     ->exists();
    }
}
