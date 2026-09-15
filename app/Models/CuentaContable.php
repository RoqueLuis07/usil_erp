<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CuentaContable extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = "cuentas_contables";// <-- El nombre personalizado

    public function Padre(){
        return $this->belongsTo(CuentaContable::class, 'padre_id');
    }

    public function ObtenerPadres(){
        $padres = collect();
        $cuenta = $this;
        $vistos = [];

        while ($cuenta->padre && !in_array($cuenta->id, $vistos)) {
            if ($cuenta->padre) {
                $padres->prepend($cuenta->padre);
            }
            $vistos[] = $cuenta->id;
            $cuenta = $cuenta->padre;
        }

        return $padres;
    }

    public function Hijos(){
        return $this->hasMany(CuentaContable::class, 'padre_id');
    }

    public function HijosRecursivos(){
        return $this->hijos()->with('hijosRecursivos');
    }

    public function CantidadHijosRecursivos(){
        return $this->hijos()->with('hijosRecursivos')->get()->sum(function ($hijo) {
            return 1 + $hijo->CantidadHijosRecursivos();
        });
    }

    public function ActualizarHijosRecursivos(){
        foreach ($this->hijos as $hijo) {
            $hijo->tipo = $this->tipo;
            $hijo->cuenta = preg_replace('/^\d/', $this->tipo, $hijo->cuenta, 1);
            $hijo->save();

            $hijo->ActualizarHijosRecursivos();
        }
    }

    public function obtenerSaldoRecursivo($anho_seleccionado, &$procesados = [])
{
    // Si ya fue procesada, evitar la recursividad infinita
    if (in_array($this->id, $procesados)) {
        return [
            'cuenta_id' => $this->id,
            'debe' => 0,
            'haber' => 0,
            'saldo' => 0,
        ];
    }

    // Marcar como procesada
    $procesados[] = $this->id;

    // Obtener el saldo de los detalles para esta cuenta
    $saldosCuentaContable = SaldoCuentaContableDetalle::where('saldo_cuenta_contable_id', $this->id)
                                                     ->where('anho', $anho_seleccionado)
                                                     ->get();

    // Sumar los saldos de los detalles
    $debe = $saldosCuentaContable->sum('debe');
    $haber = $saldosCuentaContable->sum('haber');
    $saldo = $debe - $haber;

    // Inicializamos los valores de saldo de los hijos
    $debeHijos = 0;
    $haberHijos = 0;
    $saldoHijos = 0;

    // Obtener los hijos de la cuenta contable
    foreach ($this->hijos as $hijo) {
        $saldoHijo = $hijo->obtenerSaldoRecursivo($anho_seleccionado, $procesados);
        $debeHijos += $saldoHijo['debe'];
        $haberHijos += $saldoHijo['haber'];
        $saldoHijos += $saldoHijo['saldo'];
    }

    // El saldo total es la suma de la cuenta actual más los saldos de los hijos
    $totalDebe = $debe + $debeHijos;
    $totalHaber = $haber + $haberHijos;
    $totalSaldo = $saldo + $saldoHijos;

    return [
        'cuenta_id' => $this->id,
        'debe' => $totalDebe,
        'haber' => $totalHaber,
        'saldo' => $totalSaldo,
    ];
}

    public function CargadoPor(){
        return $this->belongsTo(User::class);
    }

    public function ActualizadoPor(){
        return $this->belongsTo(User::class);
    }
}
