<?php

namespace Services;

use App\Models\Abonos;
use App\Models\DetalleAbonos;
use App\Models\Deudas;
use App\Models\Gastos;
use App\Models\Mantenimientos;

class PagoParcial implements Pago
{
    public function pago($data): void
    {
        $deuda = new Deudas();
        if(isset($data->mantenimito)){
          $deuda->relation_id = $data->mantenimito;
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

        $abono = new Abonos();
        $abono->proveedor = $data->proveedor;
        $abono->valor = $data->abono;
        $abono->save();

        $detalle = new DetalleAbonos();
        $detalle->deuda = $deuda->id;
        $detalle->valor = $data->abono;
        $detalle->save();
    }
}
