<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Proveedor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "proveedores";// <-- El nombre personalizado

    public function Departamento(){
        return $this->belongsTo(DepartamentoParaguay::class, 'departamento_id');
    }

    public function Ciudad(){
        return $this->belongsTo(Ciudad::class);
    }

    public function Categoria(){
        return $this->belongsTo(CategoriaProveedor::class, 'categoria_id');
    }

    public function Banco(){
        return $this->belongsTo(Banco::class);
    }

    public function Moneda(){
        return $this->belongsTo(Moneda::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
