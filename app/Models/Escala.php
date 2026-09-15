<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Escala extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function EscalaDetalles(){
        return $this->hasMany(EscalaDetalle::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
