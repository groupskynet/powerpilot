<?php

namespace App\Http\Controllers;

use App\Models\Deudas;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DeudasController extends Controller
{

    public function index()
    {
        $deudas = DB::table('deudas as d')
            ->join('proveedores as p', 'd.proveedor_id', '=', 'p.id')
            ->select(DB::raw('p.id as id, p.nombres as proveedor,
                (sum(d.valor) - (select COALESCE(sum(a.valor), 0) from abonos a where a.proveedor = p.id))  as cantidad'))
            ->groupBy(['proveedor', 'id'])
            ->having('cantidad', '>', 0)
            ->paginate(8);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $deudas
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $deudas = Deudas::with('mantenimiento.proveedor')->all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $deudas
        ], Response::HTTP_OK);
    }
}
