<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DocenteLegajo extends Model implements Auditable
{
    protected $table = "docentes_legajos";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

    public function TipoLegajo(){
        return $this->belongsTo(TipoLegajo::class);
    }

    public function InstitucionEducativa(){
        return $this->belongsTo(InstitucionEducativa::class);
    }

    public function Pais(){
        return $this->belongsTo(Pais::class);
    }
}
