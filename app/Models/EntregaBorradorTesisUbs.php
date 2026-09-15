<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EntregaBorradorTesisUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "entregas_borradores_tesis_ubs";// <-- El nombre personalizado

    public function Borrador(){
        return $this->belongsTo(BorradorTesisUbs::class);
    }

}
