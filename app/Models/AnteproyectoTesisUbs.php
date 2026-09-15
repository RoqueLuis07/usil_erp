<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AnteproyectoTesisUbs extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "anteproyectos_tesis_ubs";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(InscripcionTemaTesisUbs::class);
    }

    public function Bloque(){
        return $this->belongsTo(BloqueAnteproyectoTesisUbs::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function AprobadoTutor(){
        return $this->belongsTo(User::class);
    }

    public function AprobadoCalidad(){
        return $this->belongsTo(User::class);
    }

    public function Entregas(){
        return $this->hasMany(EntregaAnteproyectoTesisUbs::class, 'anteproyecto_id');
    }

}
