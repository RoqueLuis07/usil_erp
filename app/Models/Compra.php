<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Compra extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function OrdenCompra(){
        return $this->belongsTo(OrdenCompra::class);
    }

    public function Proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function TimbradoProveedor(){
        return $this->belongsTo(TimbradoProveedor::class, 'timbrado_id');
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function Cotizacion(){
        return $this->belongsTo(Cotizacion::class);
    }

    public function UnidadNegocio(){
        return $this->belongsTo(UnidadNegocioContable::class);
    }

    public function SubunidadNegocio(){
        return $this->belongsTo(SubunidadNegocioContable::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AnuladoPor(){
        return $this->belongsTo(User::class);
    }

    public function Detalles(){
        return $this->hasMany(CompraDetalle::class);
    }

    public function OrdenesPagos(){
        return $this->hasMany(OrdenPago::class);
    }

    public function AsientoContable(){
        return $this->hasOne(AsientoContable::class);
    }
}
