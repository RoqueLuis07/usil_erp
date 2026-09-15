<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExtensionUniversitaria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "extensiones_universitarias";// <-- El nombre personalizado

    public function TipoExtension(){
        return $this->belongsTo(TipoExtensionUniversitaria::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ExtensionUniversitariaDetalles(){
        return $this->hasMany(ExtensionUniversitariaDetalle::class);
    }

}
