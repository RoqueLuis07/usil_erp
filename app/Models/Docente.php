<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Docente extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Sexo(){
        return $this->belongsTo(Sexo::class);
    }

    public function Nacionalidades(){
        return $this->belongsToMany(Nacionalidad::class, 'docentes_nacionalidades');
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

    public function NivelAcademico(){
        return $this->belongsTo(DocenteNivelAcademico::class);
    }

    public function AreaConocimiento(){
        return $this->belongsTo(AreaConocimiento::class);
    }

    public function DatoLaboral(){
        return $this->belongsTo(DocenteDatoLaboral::class);
    }

    public function Banco(){
        return $this->belongsTo(Banco::class);
    }

    public function Usuario(){
        return $this->belongsTo(User::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
