<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CuentaBancaria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "cuentas_bancarias";// <-- El nombre personalizado

    public function Banco(){
        return $this->belongsTo(Banco::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function CuentaIngreso(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaEgreso(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
