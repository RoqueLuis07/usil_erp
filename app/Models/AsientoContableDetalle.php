<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AsientoContableDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "asientos_contables_detalles";// <-- El nombre personalizado

    public function Asiento(){
        return $this->belongsTo(AsientoContable::class, 'asiento_id');
    }

    public function CuentaContable(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CentroCosto(){
        return $this->belongsTo(CentroCostoContable::class, 'centro_costo_id');
    }

    public function SubcentroCosto(){
        return $this->belongsTo(SubcentroCostoContable::class, 'subcentro_costo_id');
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
