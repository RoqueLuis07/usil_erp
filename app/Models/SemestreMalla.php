<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SemestreMalla extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "semestres_mallas";// <-- El nombre personalizado

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Malla(){
        return $this->belongsTo(Malla::class);
    }

    public function Coordinador(){
        return $this->belongsTo(Docente::class);
    }

    public function SemestreMallaMaterias(){
        return $this->hasMany(SemestreMallaMateria::class);
    }
}
