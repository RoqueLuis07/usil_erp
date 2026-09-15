<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ModuloCorrelatividad extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "modulos_correlatividades";// <-- El nombre personalizado

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function Correlativa(){
        return $this->belongsTo(Modulo::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
