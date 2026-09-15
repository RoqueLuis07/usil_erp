<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RubricaDetalleTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "rubricas_detalles_tesis";// <-- El nombre personalizado

    public function Rubrica(){
        return $this->belongsTo(RubricaTesis::class, 'rubrica_id');
    }

    public function Nivel(){
        return $this->belongsTo(NivelRubricaTesis::class, 'nivel_id');
    }
}
