<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EvaluacionUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "evaluaciones_ubs";// <-- El nombre personalizado

    public function TipoEvaluacion(){
        return $this->belongsTo(TipoEvaluacionUbs::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AlumnosPuntajes(){
        return $this->hasMany(AlumnoPuntajeUbs::class, 'evaluacion_id');
    }
}
