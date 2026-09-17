<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExtensionUniversitaria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "extensiones_universitarias";// <-- El nombre personalizado

    public function TipoExtension(){
        return $this->belongsTo(TipoExtensionUniversitaria::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ExtensionUniversitariaDetalles(){
        return $this->hasMany(ExtensionUniversitariaDetalle::class);
    }

    /**
     * Carreras habilitadas para postularse a este proyecto. Sin filas acá
     * significa "abierto a todas las carreras".
     */
    public function carreras(){
        return $this->belongsToMany(Carrera::class, 'extension_universitaria_carreras', 'extension_universitaria_id', 'carrera_id');
    }

    public function estaAbiertaParaPostulacion(): bool
    {
        if ($this->estado !== 'AP') {
            return false;
        }
        if ($this->fecha_fin && now()->toDateString() > $this->fecha_fin) {
            return false;
        }
        return true;
    }

    public function cuposDisponibles(): ?int
    {
        if (!$this->cupo_maximo) {
            return null;
        }
        $aceptados = $this->ExtensionUniversitariaDetalles()->where('estado', 'AC')->count();
        return max(0, $this->cupo_maximo - $aceptados);
    }

}
