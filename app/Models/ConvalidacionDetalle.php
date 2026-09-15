<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConvalidacionDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "convalidaciones_detalles";// <-- El nombre personalizado

    public function Convalidacion(){
        return $this->belongsTo(Convalidacion::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }
}
