<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoLegajo extends Model implements Auditable
{
    protected $table = "alumnos_legajos";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function TipoLegajo(){
        return $this->belongsTo(TipoLegajo::class);
    }

}
