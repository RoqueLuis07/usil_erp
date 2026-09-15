<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrdenCompra extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "ordenes_compras";// <-- El nombre personalizado

    public function Proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function Detalles(){
        return $this->hasMany(OrdenCompraDetalle::class, 'orden_compra_id');
    }

    public function Compra(){
        return $this->hasOne(Compra::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
