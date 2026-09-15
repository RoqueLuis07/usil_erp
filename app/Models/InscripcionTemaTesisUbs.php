<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionTemaTesisUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "inscripciones_temas_tesis_ubs";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Linea(){
        return $this->belongsTo(LineaTesisUbs::class);
    }

    public function Tutor(){
        return $this->belongsTo(Docente::class);
    }

    public function FechaDefensa(){
        return $this->belongsTo(FechaDefensaTesisUbs::class);
    }

    public function AprobadoCalidad(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoTutor(){
        return $this->belongsTo(User::class);
    }

    public function RechazadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Anteproyectos(){
        return $this->hasMany(AnteproyectoTesisUbs::class, 'inscripcion_id');
    }

    public function Borradores(){
        return $this->hasMany(BorradorTesisUbs::class, 'inscripcion_id');
    }

    public function Pagado(){
        return $this->hasOne(PagoTesisUbs::class, 'inscripcion_id');
    }
}
