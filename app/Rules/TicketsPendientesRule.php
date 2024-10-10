<?php

namespace App\Rules;

use App\Models\Tickets;
use Illuminate\Contracts\Validation\Rule;

class TicketsPendientesRule implements Rule
{

    public function passes($attribute, $value)
    {
        return Tickets::where([['maquina', $value], ['estado', 'PENDIENTE']])->count() === 0;
    }

    public function message()
    {
        return 'La maquina tiene tickets pendientes por confirmar, reviselos he intente nuevamente.';
    }
}
