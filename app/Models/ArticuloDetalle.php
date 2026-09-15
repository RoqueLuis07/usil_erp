<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ArticuloDetalle extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "articulos_detalles";// <-- El nombre personalizado

    public function Articulo(){
        return $this->belongsTo(Articulo::class);
    }
    
    public function CuentaMatricula(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaContado(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaCuota(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaDescuento(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaDefensa(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaTitulo(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaMulta(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaCertificado(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaExamenSuficiencia(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaConstanciaCarrera(){
        return $this->belongsTo(CuentaContable::class);
    }

    public function CuentaTutoria(){
        return $this->belongsTo(CuentaContable::class);
    }
}
