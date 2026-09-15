<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaAlumno extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_alumnos";// <-- El nombre personalizado

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }
}
