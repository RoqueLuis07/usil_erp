<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaEvaluacion extends Model implements Auditable
{
    protected $table = "tutorias_evaluaciones";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Evaluacion(){
        return $this->belongsTo(Evaluacion::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
