<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Carrera extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function Facultad(){
        return $this->belongsTo(Facultad::class);
    }

    public function TipoCarrera(){
        return $this->belongsTo(TipoCarrera::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function Articulo(){
        return $this->belongsTo(Articulo::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
