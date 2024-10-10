<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Asignaciones extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    protected $table = 'asignaciones';

    protected $fillable = ['operador', 'maquina', 'fechaInicio', 'fechaFin'];

    public function operadorRelation()
    {
        return $this->belongsTo(Operadores::class, 'operador', 'id')->withTrashed();
    }
    public function maquinaRelation()
    {
        return $this->belongsTo(Maquinas::class, 'maquina', 'id')->withTrashed();
    }
}
