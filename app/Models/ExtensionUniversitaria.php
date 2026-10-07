<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class ExtensionUniversitaria extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "extensiones_universitarias";// <-- El nombre personalizado

    /** Problemáticas posibles; la sugerida sale del número de línea del tipo de actividad. */
    public const PROBLEMATICAS = [
        1 => 'Comunitaria',
        2 => 'Técnica',
        3 => 'Difusión',
        4 => 'Educativa',
        5 => 'Social',
    ];

    public function TipoExtension(){
        return $this->belongsTo(TipoExtensionUniversitaria::class);
    }

    public function Docente(){
        return $this->belongsTo(Docente::class);
    }

    public function Materia(){
        return $this->belongsTo(Materia::class);
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

    /** Período académico de la actividad ("2026-1" de enero a julio, "2026-2" el resto), según la fecha de inicio. */
    public function getPeriodoAttribute(): ?string
    {
        if (!$this->fecha_inicio) {
            return null;
        }
        $fecha = Carbon::parse($this->fecha_inicio);
        return $fecha->year . '-' . ($fecha->month <= 7 ? 1 : 2);
    }

    /** "Línea 3 · Difusión": la línea de extensión es la del tipo de actividad. */
    public function getLineaExtensionAttribute(): ?string
    {
        $tipo = $this->tipoExtension;
        if (!$tipo || !$tipo->linea_numero) {
            return null;
        }
        return 'Línea ' . $tipo->linea_numero . ' · ' . $tipo->nombre;
    }

    public function getProblematicaNombreAttribute(): ?string
    {
        return self::PROBLEMATICAS[$this->problematica] ?? null;
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
