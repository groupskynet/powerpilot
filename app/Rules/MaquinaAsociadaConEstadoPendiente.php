<?php

namespace App\Rules;

use App\Models\OrdenServicio;
use Illuminate\Contracts\Validation\Rule;

class MaquinaAsociadaConEstadoPendiente implements Rule
{

    public function passes($attribute, $value)
    {
        return OrdenServicio::where('estado', 'PENDIENTE')->where('maquina', $value)->count() === 0;
    }

    public function message()
    {
        return 'Esta maquina ya se encuentra asociada en otra Orden de Servicio Pendiente';
    }
}
