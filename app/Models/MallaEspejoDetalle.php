<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MallaEspejoDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "mallas_espejos_detalles";// <-- El nombre personalizado

    public function MallaEspejo(){
        return $this->belongsTo(MallaEspejo::class);
    }

    public function MateriaParaguay(){
        return $this->belongsTo(Materia::class);
    }

    public function MateriaSiu(){
        return $this->belongsTo(Materia::class);
    }
}
