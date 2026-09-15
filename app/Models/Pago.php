<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pago extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function OrdenPago(){
        return $this->belongsTo(OrdenPago::class, 'orden_pago_id');
    }

    public function Proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function FormaPago(){
        return $this->belongsTo(FormaPago::class);
    }

    public function Caja(){
        return $this->belongsTo(Caja::class);
    }

    public function Banco(){
        return $this->belongsTo(Banco::class);
    }

    public function CuentaBancaria(){
        return $this->belongsTo(CuentaBancaria::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
