<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SemestreMallaMateriaHorario extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "semestres_mallas_materias_horarios";// <-- El nombre personalizado

    public function SemestreMallaMateria(){
        return $this->belongsTo(SemestreMallaMateria::class);
    }

    public function DiaSemana(){
        return $this->belongsTo(DiaSemana::class);
    }
}
