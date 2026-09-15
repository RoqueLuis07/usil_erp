<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AlumnoPuntajeUbs extends Model implements Auditable
{
    protected $table = "alumnos_puntajes_ubs";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function Evaluacion(){
        return $this->belongsTo(EvaluacionUbs::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
