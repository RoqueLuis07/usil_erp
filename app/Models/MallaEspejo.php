<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MallaEspejo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "mallas_espejos";// <-- El nombre personalizado

    public function MallaParaguay(){
        return $this->belongsTo(Malla::class);
    }

    public function MallaSiu(){
        return $this->belongsTo(Malla::class);
    }

    public function MallaEspejoDetalles(){
        return $this->hasMany(MallaEspejoDetalle::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
