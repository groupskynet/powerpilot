<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Abonos extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'abonos';


    protected $fillable = ['proveedor', 'valor', 'created_at', 'updated_at'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor', 'id');
    }
}
