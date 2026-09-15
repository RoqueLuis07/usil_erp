<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Empresa extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Pais(){
        return $this->belongsTo(Pais::class);
    }

    public function Ciudad(){
        return $this->belongsTo(Ciudad::class);
    }

}
