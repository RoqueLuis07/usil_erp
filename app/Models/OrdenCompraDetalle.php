<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrdenCompraDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "ordenes_compras_detalles";// <-- El nombre personalizado

    public function OrdenCompra(){
        return $this->belongsTo(OrdenCompra::class);
    }

    public function Articulo(){
        return $this->belongsTo(Articulo::class);
    }
}
