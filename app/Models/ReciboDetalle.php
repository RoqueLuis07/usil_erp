<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ReciboDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "recibos_detalles";// <-- El nombre personalizado

    public function Recibo(){
        return $this->belongsTo(Recibo::class);
    }

    public function Venta(){
        return $this->belongsTo(Venta::class);
    }
}
