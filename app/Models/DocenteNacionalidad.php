<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DocenteNacionalidad extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "docentes_nacionalidades";// <-- El nombre personalizado

    public function Docente(){
        return $this->hasMany(Docente::class);
    }

    public function Nacionalidad(){
        return $this->hasMany(Nacionalidad::class);
    }

}
