<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TutoriaPrecio extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "tutorias_precios";// <-- El nombre personalizado

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function Carrera(){
        return $this->belongsTo(Carrera::class);
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
