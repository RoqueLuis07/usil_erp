<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Venta extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function PuntoImpresion(){
        return $this->belongsTo(PuntoImpresion::class);
    }

    public function DescuentoAplicado(){
        return $this->belongsTo(Convenio::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AnuladoPor(){
        return $this->belongsTo(User::class);
    }

    public function VentaDetalles(){
        return $this->hasMany(VentaDetalle::class);
    }

    public function Cobros(){
        return $this->hasMany(Cobro::class);
    }

    public function AsientoContable(){
        return $this->hasOne(AsientoContable::class);
    }
}