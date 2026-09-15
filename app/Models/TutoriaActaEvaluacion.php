<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaActaEvaluacion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_actas_evaluaciones";// <-- El nombre personalizado

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function GeneradoPor(){
        return $this->belongsTo(User::class);
    }

    public function Alumnos(){
        return $this->hasMany(TutoriaActaEvaluacionAlumno::class, 'acta_evaluacion_id');
    }

}
