<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConvenioDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "convenios_detalles";// <-- El nombre personalizado

    public function Convenio(){
        return $this->belongsTo(Venta::class);
    }

}
