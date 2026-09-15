<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoFamiliar extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "alumnos_familiares";// <-- El nombre personalizado

    public function Relacion(){
        return $this->belongsTo(RelacionFamiliar::class);
    }

}
