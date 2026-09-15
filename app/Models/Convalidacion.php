<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Convalidacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "convalidaciones";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Facultad(){
        return $this->belongsTo(Facultad::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function UniversidadOrigen(){
        return $this->belongsTo(InstitucionEducativa::class);
    }

    public function ConvalidacionDetalles(){
        return $this->hasMany(ConvalidacionDetalle::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
