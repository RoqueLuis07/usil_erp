<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExtensionUniversitariaUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "extensiones_universitarias_ubs";// <-- El nombre personalizado

    public function TipoExtension(){
        return $this->belongsTo(TipoExtensionUniversitariaUbs::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
