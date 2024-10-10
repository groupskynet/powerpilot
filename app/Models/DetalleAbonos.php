<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DetalleAbonos extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'detalle-abono';
    protected $fillable = ['deuda', 'valor', 'created_at', 'updated_at'];

    public function deuda()
    {
        return $this->belongsTo(Deudas::class, 'deuda', 'id');
    }
}
