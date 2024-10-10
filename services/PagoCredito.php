<?php

namespace Services;

use App\Models\Deudas;

class PagoCredito implements Pago
{
    public function pago($data): void
    {
        $deuda = new Deudas();

        if(isset($data->mantenimiento)){
          $deuda->relation_id = $data->mantenimiento;
          $deuda->modelo = Mantenimientos::class;
        }

        if(isset($data->gasto)){
          $deuda->relation_id = $data->gasto;
          $deuda->modelo = Gastos::class;
        }

        $deuda->proveedor_id = $data->proveedor;
        $deuda->valor = $data->costo;
        $deuda->estado = 'PENDIENTE';
        $deuda->save();
    }
}
