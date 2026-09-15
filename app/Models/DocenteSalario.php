<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DocenteSalario extends Model implements Auditable
{
    protected $table = "docentes_salarios";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoPor(){
        return $this->belongsTo(User::class);
    }

    public function RechazadoPor(){
        return $this->belongsTo(User::class);
    }
}
