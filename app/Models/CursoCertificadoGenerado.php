<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CursoCertificadoGenerado extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "cursos_certificados_generados";// <-- El nombre personalizado

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
