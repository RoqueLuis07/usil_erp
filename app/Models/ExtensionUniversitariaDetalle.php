<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExtensionUniversitariaDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "extensiones_universitarias_detalles";// <-- El nombre personalizado

    public function ExtensionUniversitaria(){
        return $this->belongsTo(ExtensionUniversitaria::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }
}
