<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Matriculacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "matriculaciones";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function CarreraSiu(){
        return $this->belongsTo(Carrera::class);
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

    public function PagosMatriculacion(){
        return $this->hasMany(PagoMatriculacion::class, 'matriculacion_id');
    }

    public function Inscripciones(){
        return $this->hasMany(Inscripcion::class, 'matriculacion_id');
    }

}
