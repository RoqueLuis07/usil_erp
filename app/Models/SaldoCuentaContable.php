<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SaldoCuentaContable extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "saldos_cuentas_contables";// <-- El nombre personalizado

    public function CuentaContable(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function Detalles(){
        return $this->hasMany(SaldoCuentaContableDetalle::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
