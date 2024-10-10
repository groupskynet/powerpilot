<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoAccesorio extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pagos_accesorios';

    protected $fillable = ['accesorio_id', 'valor', 'fecha_inicio', 'fecha_fin'];

    public function accesorio()
    {
        return $this->belongsTo(Accesorios::class, 'accesorio_id', 'id')->withTrashed();
    }
}
