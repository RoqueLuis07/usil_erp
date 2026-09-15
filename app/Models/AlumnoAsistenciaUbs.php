<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoAsistenciaUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "alumnos_asistencias_ubs";// <-- El nombre personalizado

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }
}
