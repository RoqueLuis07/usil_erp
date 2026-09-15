<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Curso extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function Programa(){
        return $this->belongsTo(Programa::class);
    }

    public function Facultad(){
        return $this->belongsTo(Facultad::class);
    }

    public function TipoCurso(){
        return $this->belongsTo(TipoCurso::class);
    }

    public function Modalidad(){
        return $this->belongsTo(Modalidad::class);
    }

    public function Articulo(){
        return $this->belongsTo(Articulo::class);
    }

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }

    public function Precios(){
        return $this->hasOne(CursoPrecio::class);
    }

    public function Modulos(){
        return $this->hasMany(CursoModulo::class);
    }

    public function Inscripciones(){
        return $this->hasMany(InscripcionUbs::class);
    }

    public function Asistencias(){
        return $this->hasMany(AlumnoAsistenciaUbs::class);
    }

    public function Puntajes(){
        return $this->hasMany(AlumnoPuntajeUbs::class);
    }

    public function Notas(){
        return $this->hasMany(AlumnoNotaUbs::class);
    }

    public function CertificadosGenerados(){
        return $this->hasMany(CursoCertificadoGenerado::class);
    }

}
