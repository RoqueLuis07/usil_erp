<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MovimientoCajaBanco extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "movimientos_cajas_bancos";// <-- El nombre personalizado

    public function Caja(){
        return $this->belongsTo(Caja::class);
    }

    public function CuentaBancaria(){
        return $this->belongsTo(CuentaBancaria::class);
    }

    public function TipoMovimiento(){
        return $this->belongsTo(TipoMovimiento::class);
    }

    public function CreadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoPor(){
        return $this->belongsTo(User::class);
    }

    public function RechazadoPor(){
        return $this->belongsTo(User::class);
    }
}
