<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoSolicitud extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "pagos_solicitudes";// <-- El nombre personalizado

    public function Solicitud(){
        return $this->belongsTo(Solicitud::class);
    }
}
