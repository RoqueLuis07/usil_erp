<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PuntoImpresion extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "puntos_impresiones";// <-- El nombre personalizado

    public function TipoDocumento(){
        return $this->belongsTo(TipoDocumentoContable::class);
    }

    public function Timbrado(){
        return $this->belongsTo(Timbrado::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
