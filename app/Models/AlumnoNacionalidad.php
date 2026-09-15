<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoNacionalidad extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "alumnos_nacionalidades";// <-- El nombre personalizado

    public function Alumno(){
        return $this->hasMany(Alumno::class);
    }

    public function Nacionalidad(){
        return $this->hasMany(Nacionalidad::class);
    }

}
