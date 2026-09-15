<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Recibo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Detalles(){
        return $this->hasMany(ReciboDetalle::class);
    }

    public function Cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function FormaPago(){
        return $this->belongsTo(FormaPago::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function AnuladoPor(){
        return $this->belongsTo(User::class);
    }
}
