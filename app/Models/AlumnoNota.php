<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoNota extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "alumnos_notas";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

}
