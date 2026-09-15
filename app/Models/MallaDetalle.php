<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MallaDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "mallas_detalles";// <-- El nombre personalizado

    public function Malla(){
        return $this->belongsTo(Malla::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }
}
