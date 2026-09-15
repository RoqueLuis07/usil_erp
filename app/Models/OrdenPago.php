<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrdenPago extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "ordenes_pagos";// <-- El nombre personalizado

    public function Compra(){
        return $this->belongsTo(Compra::class);
    }

    public function Proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function FormaPago(){
        return $this->belongsTo(FormaPago::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function Cotizacion(){
        return $this->belongsTo(Cotizacion::class);
    }

    public function CuentaContable(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function UnidadNegocio(){
        return $this->hasOne(UnidadNegocioContable::class);
    }

    public function SubunidadNegocio(){
        return $this->hasOne(SubunidadNegocioContable::class);
    }

    public function Pago(){
        return $this->hasOne(Pago::class);
    }

    public function AsientoContable(){
        return $this->hasOne(AsientoContable::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
