<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FacturaGasolinaTicketRule implements Rule
{

    private bool $tieneCombustible;

    public function __construct($tieneCombustible)
    {
        $this->tieneCombustible = $tieneCombustible;
    }


    public function passes($attribute, $value)
    {

        if (!$this->tieneCombustible && $value) {
            return true;
        }

        if ($this->tieneCombustible) return true;

        return false;
    }


    public function message()
    {
        return 'La factura de la gasolina es requerida';
    }
}
