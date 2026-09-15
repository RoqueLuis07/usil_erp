<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoTutoria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "pagos_tutorias";// <-- El nombre personalizado

    public function Tutoria(){
        return $this->belongsTo(Tutoria::class);
    }

    public function Alumno(){
        return $this->belongsTo(Alumno::class);
    }
}
