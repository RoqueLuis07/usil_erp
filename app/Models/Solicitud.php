<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Solicitud extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "solicitudes";// <-- El nombre personalizado

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }

    public function TipoSolicitud(){
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

    public function Semestre(){
        return $this->belongsTo(Semestre::class);
    }

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function PagoSolicitud(){
        return $this->hasOne(PagoSolicitud::class);
    }

    public function AprobadoPor(){
        return $this->belongsTo(User::class);
    }

    public function RechazadoPor(){
        return $this->belongsTo(User::class);
    }

    public function EntregadoPor(){
        return $this->belongsTo(User::class);
    }
}
