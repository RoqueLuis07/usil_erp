<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MovimientoCaja extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "movimientos_cajas";// <-- El nombre personalizado

    public function CajaOrigen(){
        return $this->belongsTo(Caja::class);
    }

    public function CajaDestino(){
        return $this->belongsTo(Caja::class);
    }

    public function TipoMovimiento(){
        return $this->belongsTo(TipoMovimiento::class);
    }

    public function Venta(){
        return $this->belongsTo(Venta::class);
    }

    public function Pago(){
        return $this->belongsTo(Pago::class);
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
