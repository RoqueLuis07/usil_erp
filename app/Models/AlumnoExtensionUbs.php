<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoExtensionUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "alumnos_extensiones_ubs";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function TipoExtension(){
        return $this->belongsTo(TipoExtension::class);
    }
}
