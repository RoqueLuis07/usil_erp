<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProyectoTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "proyectos_tesis";// <-- El nombre personalizado

    public function Inscripcion(){
        return $this->belongsTo(InscripcionTemaTesis::class);
    }

    public function Bloque(){
        return $this->belongsTo(BloqueProyectoTesis::class);
    }

    public function AprobadoTutor(){
        return $this->belongsTo(User::class);
    }

    public function Entregas(){
        return $this->hasMany(EntregaProyectoTesis::class, 'proyecto_id');
    }

}
