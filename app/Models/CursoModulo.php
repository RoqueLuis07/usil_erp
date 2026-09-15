<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CursoModulo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "cursos_modulos";// <-- El nombre personalizado

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Alumnos(){
        return $this->hasMany(InscripcionModulo::class, 'modulo_id', 'modulo_id');
    }
}
