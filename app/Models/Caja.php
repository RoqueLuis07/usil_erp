<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Caja extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Usuario(){
        return $this->hasOneThrough(User::class, UsuarioCaja::class, 'caja_id', 'id', 'id', 'usuario_id');
    }

    public function CuentaIngreso(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaEgreso(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
