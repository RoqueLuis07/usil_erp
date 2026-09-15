<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaClase extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_clases";// <-- El nombre personalizado

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function Asistencias(){
        return $this->hasMany(TutoriaAsistencia::class, 'clase_id');
    }

}
