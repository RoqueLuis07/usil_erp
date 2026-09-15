<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RubricaAlumnoDetalleTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "rubricas_alumnos_detalles_tesis";// <-- El nombre personalizado

    public function Rubrica(){
        return $this->belongsTo(RubricaTesis::class, 'rubrica_id');
    }

    public function RubricaDetalle(){
        return $this->belongsTo(RubricaDetalleTesis::class, 'rubrica_detalle_id');
    }
}
