<?php

namespace App\Http\Controllers;

use App\Models\PagoAccesorio;
use App\Models\PagoMaquina;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PagosController extends Controller
{
    public function maquina(Request $request)
    {
        try {
            DB::beginTransaction();
            $historico = PagoMaquina::where('maquina_id', $request->id)
                ->whereNull('fecha_fin')
                ->first();
            if ($historico) {
                $historico->fecha_fin = date('y-m-d H:i:s');
                $historico->save();
            }
            $pago = new PagoMaquina([
                'maquina_id' => $request->id,
                'valor' => $request->value,
                'fecha_inicio' => date('y-m-d H:i:s'),
            ]);
            $pago->save();
            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'pago actualizado correctamente.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Ha Ocurrido un error, por favor intenta más tarde.',
                'data' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }

    public function accesorio(Request $request)
    {
        try {
            DB::beginTransaction();
            $historico = PagoAccesorio::where('accesorio_id', $request->id)
                ->whereNull('fecha_fin')
                ->first();
            if ($historico) {
                $historico->fecha_fin = date('y-m-d H:i:s');
                $historico->save();
            }
            $pago = new PagoAccesorio([
                'accesorio_id' => $request->id,
                'valor' => $request->value,
                'fecha_inicio' => date('y-m-d H:i:s'),
            ]);
            $pago->save();
            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'pago actualizado correctamente.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Ha Ocurrido un error, por favor intenta más tarde.',
                'data' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
