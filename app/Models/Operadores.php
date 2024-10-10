<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Operadores extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable   = [
        'id',
        'cedula',
        'nombres',
        'apellidos',
        'cedula',
        'telefono1',
        'telefono2',
        'licencia',
        'direccion',
        'email'
    ];
}
