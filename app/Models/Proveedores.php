<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Proveedores extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    //5265570036511336 06/27  015
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id',
        'tipo',
        'cedula',
        'nombres',
        'telefono',
        'direccion',
        'email',
        'nit',
        'razonSocial',
        'iva'

    ];
}
