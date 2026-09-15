<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Convenio extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Detalle(){
        return $this->hasOne(ConvenioDetalle::class);
    }
}
