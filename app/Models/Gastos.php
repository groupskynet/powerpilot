<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Gastos extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id',
        'maquina',
        'proveedor',
        'modalidad',
        'valor',
        'abono',
        'descripcion',
        'soporte',
    ];

    public function maquina()
    {
        return $this->belongsTo(Maquinas::class, 'maquina', 'id');
    }
}
