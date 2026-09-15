<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaHorario extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_horarios";// <-- El nombre personalizado

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function DiaSemana(){
        return $this->belongsTo(DiaSemana::class);
    }
}
