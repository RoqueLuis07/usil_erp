<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "pagos_tesis";// <-- El nombre personalizado

    public function Tesis(){
        return $this->belongsTo(InscripcionTemaTesis::class, 'inscripcion_id');
    }
}
