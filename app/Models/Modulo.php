<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Modulo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Correlativas(){
        return $this->belongsToMany(Modulo::class, 'modulos_correlatividades', 'modulo_id', 'correlativa_id')->withTimestamps();
    }

    public function EsCorrelativa(){
        return $this->belongsToMany(Modulo::class, 'modulos_correlatividades', 'correlativa_id', 'modulo_id')->withTimestamps();
    }
}
