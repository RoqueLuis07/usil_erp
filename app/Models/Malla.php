<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Malla extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function TipoMalla(){
        return $this->belongsTo(TipoMalla::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function MallaDetalles(){
        return $this->hasMany(MallaDetalle::class);
    }

}
