<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SaldoCuentaContableDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "saldos_cuentas_contables_detalles";// <-- El nombre personalizado

    public function SaldoCuentaContable(){
        return $this->belongsTo(SaldoCuentaContable::class);
    }
}
