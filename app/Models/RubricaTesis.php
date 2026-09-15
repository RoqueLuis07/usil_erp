<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RubricaTesis extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "rubricas_tesis";// <-- El nombre personalizado

    public function Tipo(){
        return $this->belongsTo(TipoTesis::class, 'tipo_id');
    }

    public function Detalles(){
        return $this->hasMany(RubricaDetalleTesis::class, 'rubrica_id');
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
