<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaAsistencia extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_asistencias";// <-- El nombre personalizado

    public function Clase(){
        return $this->belongsTo(TutoriaClase::class, 'clase_id');
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

}
