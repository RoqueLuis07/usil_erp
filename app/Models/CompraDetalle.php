<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompraDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "compras_detalles";// <-- El nombre personalizado

    public function Compra(){
        return $this->belongsTo(Compra::class);
    }

    public function Articulo(){
        return $this->belongsTo(Articulo::class);
    }

    public function CentroCosto(){
        return $this->belongsTo(CentroCostoContable::class);
    }

    public function SubcentroCosto(){
        return $this->belongsTo(SubcentroCostoContable::class);
    }

}
