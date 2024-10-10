<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Tickets;

class OrdenConTicketsAsociados implements Rule
{

    public function passes($attribute, $value)
    {
        return Tickets::where('orden', $value)->count() === 0;
    }

    public function message()
    {
        return 'Operación inválida, No es posible editar ordenes con tickets asociados.';
    }
}
