<?php

namespace App\Http\Controllers;

use App\Models\Abonos;
use App\Models\DetalleAbonos;
use App\Models\Deudas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AbonosController extends Controller
{

    public function index()
    {
        $abonos = Abonos::with('proveedor')->paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $abonos
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'proveedor' => 'numeric|required',
            'valor' => 'numeric|min:0|required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data',
                'data' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $deudas = DB::table('deudas as d')
            ->join('mantenimientos as m', 'm.id', '=', 'd.mantenimiento')
            ->join('proveedores as p', 'p.id', '=', 'm.proveedor')
            ->select(DB::raw("d.id as id, (sum(d.valor) - (select COALESCE(sum(de.valor), 0) from `detalle-abono` as de  where de.deuda = d.id)) as valor"))
            ->groupBy(['id'])
            ->having('valor', '>', 0)
            ->get();


        try {
            $restante = $request->valor;

            DB::beginTransaction();

            $abono = new Abonos(['proveedor' => $request->proveedor, 'valor' => $request->valor]);
            $abono->save();


            foreach ($deudas as  $deuda) {
                if ($restante <= 0) break;
                $detalle = new DetalleAbonos();
                $detalle->deuda = $deuda->id;
                $detalle->valor  = $restante >= $deuda->valor ? $deuda->valor : $restante;
                $restante = $restante - $deuda->valor;
                $detalle->save();
            }

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Abono guardado correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Ha ocurrido un error inesperado, por favor intete más tarde.',
            ], Response::HTTP_OK);
        }
    }

    public function all()
    {
        $abonos = Abonos::with('proveedor')->all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $abonos
        ], Response::HTTP_OK);
    }
}
