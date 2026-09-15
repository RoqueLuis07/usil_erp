<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AsientoContable extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "asientos_contables";// <-- El nombre personalizado

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function Cotizacion(){
        return $this->belongsTo(Cotizacion::class);
    }

    public function UnidadNegocio(){
        return $this->belongsTo(UnidadNegocioContable::class, 'unidad_negocio_id');
    }

    public function SubunidadNegocio(){
        return $this->belongsTo(SubunidadNegocioContable::class, 'subunidad_negocio_id');
    }

    public function Detalles(){
        return $this->hasMany(AsientoContableDetalle::class, 'asiento_id');
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
