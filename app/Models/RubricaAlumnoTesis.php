<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RubricaAlumnoTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "rubricas_alumnos_tesis";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(InscripcionTemaTesis::class, 'inscripcion_id');
    }

    public function Detalles(){
        return $this->hasMany(RubricaAlumnoDetalleTesis::class, 'rubrica_id');
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
