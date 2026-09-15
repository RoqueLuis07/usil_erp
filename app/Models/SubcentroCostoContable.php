<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SubcentroCostoContable extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "subcentros_costos_contables";// <-- El nombre personalizado

    public function centroCostoContable(){
        return $this->hasMany(CentroCostoContable::class, 'centro_costo_id');
    }
    
}
