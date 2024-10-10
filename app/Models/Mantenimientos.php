<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Mantenimientos extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id',
        'tipo',
        'maquina',
        'proveedor',
        'descripcion',
        'horometro',
        'modalidad',
        'costo',
        'abono',
        'soporte'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor', 'id')->withTrashed();
    }

    public function maquina()
    {
        return $this->belongsTo(Maquinas::class, 'maquina', 'id')->withTrashed();
    }
}
