<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Cliente extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Sexo(){
        return $this->belongsTo(Sexo::class);
    }

    public function EstadoCivil(){
        return $this->belongsTo(EstadoCivil::class);
    }

    public function Nacionalidad(){
        return $this->belongsTo(Nacionalidad::class);
    }

    public function Departamento(){
        return $this->belongsTo(DepartamentoParaguay::class);
    }

    public function Ciudad(){
        return $this->belongsTo(Ciudad::class);
    }

    public function Barrio(){
        return $this->belongsTo(Barrio::class);
    }

    public function DatoLaboral(){
        return $this->belongsTo(ClienteDatoLaboral::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

}
