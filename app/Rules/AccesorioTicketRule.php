<?php

namespace App\Rules;

use App\Models\Tickets;
use Illuminate\Contracts\Validation\Rule;

class AccesorioTicketRule implements Rule
{
    public $maquina = null;
    public $fecha = null;

    public function __construct($maquina, $fecha)
    {
        $this->maquina = $maquina;
        $this->fecha = $fecha;
    }

    public function passes($attribute, $value)
    {
        if (!isset($value)) {
            return true;
        }

        return Tickets::where([
                ['maquina', $this->maquina],
                ['fecha', $this->fecha],
                ['accesorio', $value]
            ])->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El Accesorio ya se encuentra con un ticket asociado para la fecha seleccionada.';
    }
}
