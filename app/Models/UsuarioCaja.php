<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UsuarioCaja extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "usuarios_cajas";// <-- El nombre personalizado

    public function Usuario(){
        return $this->belongsTo(User::class);
    }

    public function Caja(){
        return $this->belongsTo(Caja::class);
    }
}
