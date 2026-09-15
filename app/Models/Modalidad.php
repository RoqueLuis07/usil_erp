<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Modalidad extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "modalidades";// <-- El nombre personalizado

    public function Alumnos(){
        return $this->belongsToMany(Alumno::class, 'alumnos_nacionalidades');
    }

}
