<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EntregaAnteproyectoTesisUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "entregas_anteproyectos_tesis_ubs";// <-- El nombre personalizado

    public function Anteproyecto(){
        return $this->belongsTo(AnteproyectoTesisUbs::class);
    }

}
