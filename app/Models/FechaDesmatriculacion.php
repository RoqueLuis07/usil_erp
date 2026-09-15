<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FechaDesmatriculacion extends Model implements Auditable
{
    protected $table = "fechas_desmatriculaciones";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
