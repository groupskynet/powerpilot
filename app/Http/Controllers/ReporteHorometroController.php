<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReporteHorometroController extends Controller
{

    public function index(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'maquina_id' => 'required',
            'fechaInicio' => 'required',
            'fechaFinal' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' =>  Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'data' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $tickets = Tickets::with('orden.cliente', 'orden.maquina', 'orden.accesorios', 'accesorio')
            ->where('maquina', $request->maquina_id)
            ->where('fecha', '>=', $request->fechaInicio)
            ->where('fecha', '<=', $request->fechaFinal)
            ->paginate(8);

        $totales = DB::table('tickets')
            ->where('tickets.maquina', $request->maquina_id)
            ->where('tickets.fecha', '>=', $request->fechaInicio)
            ->where('tickets.fecha', '<=', $request->fechaFinal)
            ->whereNull('tickets.deleted_at')
            ->select(
                DB::raw('sum((tickets.horometroFinal - tickets.horometroInicial) * tickets.valor_por_hora_orden) as total'), //pendiente
                DB::raw('sum(tickets.galones * tickets.costo) / sum(tickets.horometroFinal - tickets.horometroInicial) as combustible'),
                DB::raw('avg(tickets.horometroFinal - tickets.horometroInicial) as horas_por_dia')
            )
            ->first();

        $accesorios = DB::table('tickets')
            ->join('accesorios', 'accesorios.id', '=', 'tickets.accesorio')
            ->where('tickets.maquina', $request->maquina_id)
            ->where('tickets.fecha', '>=', $request->fechaInicio)
            ->where('tickets.fecha', '<=', $request->fechaFinal)
            ->whereNull('tickets.deleted_at')
            ->select('accesorios.nombre as accesorio', DB::raw('sum(horometroFinal - horometroInicial) as horas'))
            ->groupBy('accesorios.id')
            ->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'datos encontrados',
            'tickets' => $tickets,
            'totales' => $totales,
            'accesorios' => $accesorios,
        ]);
    }
}
