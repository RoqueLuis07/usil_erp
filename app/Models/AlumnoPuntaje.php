<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoPuntaje extends Model implements Auditable
{
    protected $table = "alumnos_puntajes";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Evaluacion(){
        return $this->belongsTo(Evaluacion::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

}
