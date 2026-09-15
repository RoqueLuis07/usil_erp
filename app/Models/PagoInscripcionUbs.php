<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoInscripcionUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "pagos_inscripciones_ubs";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(InscripcionUbs::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }
}
