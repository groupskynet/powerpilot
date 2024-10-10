<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class Tickets extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id',
        'orden',
        'cliente',
        'fecha',
        'maquina',
        'accesorio',
        'consecutivo',
        'horometroInicial',
        'horometroFinal',
        'galones',
        'costo',
        'soporte',
        'operador',
        'estado',
        'valor_por_hora'
    ];

    public function getNumeroOrdenAttribute(): string
    {
        return str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente', 'id');
    }

    public function orden()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden', 'id');
    }

    public function operador()
    {
        return $this->belongsTo(Operadores::class, 'operador', 'id');
    }

    public function maquina()
    {
        return $this->belongsTo(Maquinas::class, 'maquina', 'id');
    }

    public function accesorio()
    {
        return $this->belongsTo(Accesorios::class, 'accesorio', 'id');
    }
}
