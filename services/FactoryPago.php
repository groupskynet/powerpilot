<?php

namespace Services;

use Exception;

class FactoryPago
{

  private string $type;

  public function __construct(string $type)
  {
    $this->type = $type;
  }

  public function create(): Pago
  {
    if ($this->type === "EFECTIVO")
      return new PagoEfectivo();

    if ($this->type === "PAGO PARCIAL")
      return new PagoParcial();

    if ($this->type === "CREDITO")
      return new PagoCredito();

    throw new Exception('TYPE NOT ALLOWED');
  }
}
