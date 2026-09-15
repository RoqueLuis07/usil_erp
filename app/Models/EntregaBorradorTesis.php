<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EntregaBorradorTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "entregas_borradores_tesis";// <-- El nombre personalizado

    public function Borrador(){
        return $this->belongsTo(BorradorTesis::class);
    }

}
