<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Cobro extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Venta(){
        return $this->belongsTo(Venta::class);
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

    public function NotaCredito(){
        return $this->belongsTo(NotaCredito::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
