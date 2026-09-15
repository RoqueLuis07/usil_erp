<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SubunidadNegocioContable extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "subunidades_negocios_contables";// <-- El nombre personalizado

    public function UnidadNegocioContable(){
        return $this->hasMany(UnidadNegocioContable::class, 'unidad_id');
    }

}
