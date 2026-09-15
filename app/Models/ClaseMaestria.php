<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ClaseMaestria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "clases_maestrias";// <-- El nombre personalizado

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Curso(){
        return $this->belongsTo(Curso::class);
    }

    public function Modulo(){
        return $this->belongsTo(Modulo::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }
}
