<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Tickets;

class TicketsPosterioresAlaFechaRule implements Rule
{
    protected int $maquina;

    public function __construct(int $id)
    {
        $this->maquina = $id;
    }

    public function passes($attribute, $value)
    {
        return Tickets::where([['fecha', '>', $value], ['maquina', $this->maquina]])->count() === 0;
    }

    public function message()
    {
        return 'No se pueden crear tickets con fecha anterior a los ya existentes en el sistema.';
    }
}
