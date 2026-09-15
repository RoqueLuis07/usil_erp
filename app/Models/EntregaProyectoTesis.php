<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EntregaProyectoTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "entregas_proyectos_tesis";// <-- El nombre personalizado

    public function Proyecto(){
        return $this->belongsTo(ProyectoTesis::class);
    }

}
