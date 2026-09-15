<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionModulo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones_modulos";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }
}
