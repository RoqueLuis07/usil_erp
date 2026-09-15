<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones_ubs";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Convenio(){
        return $this->belongsTo(Convenio::class);
    }

    public function Venta(){
        return $this->belongsTo(Venta::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function PagosInscripciones(){
        return $this->hasMany(PagoInscripcionUbs::class, 'inscripcion_id');
    }
}
