<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionSemestreMallaMateria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones_semestres_mallas_materias";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(Inscripcion::class);
    }

    public function SemestreMallaMateria(){
        return $this->belongsTo(SemestreMallaMateria::class);
    }
}
