<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AnulacionCorrelatividad extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "anulaciones_correlatividades";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

}
