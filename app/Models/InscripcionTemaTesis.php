<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionTemaTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones_temas_tesis";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
    }

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function Tipo(){
        return $this->belongsTo(TipoTesis::class);
    }

    public function Area(){
        return $this->belongsTo(AreaTesis::class);
    }

    public function Linea(){
        return $this->belongsTo(LineaTesis::class);
    }

    public function Tutor(){
        return $this->belongsTo(Docente::class);
    }

    public function AprobadoCoordinacion(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoTutor(){
        return $this->belongsTo(User::class);
    }

    public function RechazadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Anteproyectos(){
        return $this->hasMany(AnteproyectoTesis::class, 'inscripcion_id');
    }

    public function Proyectos(){
        return $this->hasMany(ProyectoTesis::class, 'inscripcion_id');
    }

    public function Borradores(){
        return $this->hasMany(BorradorTesis::class, 'inscripcion_id');
    }

    public function Rubrica(){
        return $this->hasOne(RubricaAlumnoTesis::class, 'inscripcion_id');
    }
}
