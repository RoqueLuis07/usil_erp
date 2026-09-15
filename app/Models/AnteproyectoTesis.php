<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AnteproyectoTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "anteproyectos_tesis";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(InscripcionTemaTesis::class);
    }

    public function Bloque(){
        return $this->belongsTo(BloqueAnteproyectoTesis::class);
    }

    public function AprobadoTutor(){
        return $this->belongsTo(User::class);
    }

    public function Entregas(){
        return $this->hasMany(EntregaAnteproyectoTesis::class, 'anteproyecto_id');
    }

}
