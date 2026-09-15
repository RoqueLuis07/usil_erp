<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoMatriculacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "pagos_matriculaciones";// <-- El nombre personalizado

    public function Matriculacion(){
        return $this->belongsTo(Matriculacion::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function VentaDetalle(){
        return $this->hasOne(VentaDetalle::class);
    }

}
