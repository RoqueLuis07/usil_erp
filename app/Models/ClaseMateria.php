<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ClaseMateria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "clases_materias";// <-- El nombre personalizado

    public function Docente(){
        return $this->belongsTo(Docente::class);
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

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function AlumnoAsistencias(){
        return $this->hasMany(AlumnoAsistencia::class);
    }

}
