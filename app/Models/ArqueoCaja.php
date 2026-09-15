<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ArqueoCaja extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "arqueos_cajas";// <-- El nombre personalizado

    public function Caja(){
        return $this->belongsTo(Caja::class);
    }

    public function GeneradoPor(){
        return $this->belongsTo(User::class);
    }
}
