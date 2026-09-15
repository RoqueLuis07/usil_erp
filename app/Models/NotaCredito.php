<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NotaCredito extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "notas_creditos";// <-- El nombre personalizado

    public function Cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function PuntoImpresion(){
        return $this->belongsTo(PuntoImpresion::class);
    }

    public function Venta(){
        return $this->belongsTo(Venta::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AnuladoPor(){
        return $this->belongsTo(User::class);
    }

    public function AsientoContable(){
        return $this->hasOne(AsientoContable::class);
    }

    public function Cobro(){
        return $this->hasOne(Cobro::class);
    }
}