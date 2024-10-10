<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Deudas extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'deudas';

    protected $fillable = [
        'relation_id', 
        'modelo', 
        'proveedor',
        'valor',
        'estado',
        'created_at',
        'updated_at'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimientos::class, 'mantenimiento', 'id');
    }
}
