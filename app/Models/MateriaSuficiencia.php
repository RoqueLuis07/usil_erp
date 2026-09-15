<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MateriaSuficiencia extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "materias_suficiencias";// <-- El nombre personalizado

    public function Materia(){
        return $this->belongsTo(Materia::class);
    }

}
