<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Articulo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Carrera(){
        return $this->hasOne(Carrera::class);
    }

    public function Curso(){
        return $this->hasOne(Curso::class);
    }

    public function CentroCosto(){
        return $this->belongsTo(CentroCostoContable::class, 'centro_costo_id');
    }

    public function SubcentroCosto(){
        return $this->belongsTo(SubcentroCostoContable::class, 'subcentro_costo_id');
    }

    public function UnidadNegocio(){
        return $this->belongsTo(UnidadNegocioContable::class, 'unidad_id');
    }

    public function SubunidadNegocio(){
        return $this->belongsTo(SubunidadNegocioContable::class, 'subunidad_id');
    }

    public function Detalle(){
        return $this->hasOne(ArticuloDetalle::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
    
}
