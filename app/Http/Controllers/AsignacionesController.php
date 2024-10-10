<?php

namespace App\Http\Controllers;

use App\Models\Asignaciones;
use App\Models\Maquinas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AsignacionesController extends Controller
{

    public function index()
    {
        $asignaciones = Asignaciones::with(['operadorRelation', 'maquinaRelation'])
            ->orderBy('maquina')->paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $asignaciones
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $asignaciones = Asignaciones::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $asignaciones
        ], Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'operador' => 'required',
            'maquina' => 'required',
        ]);


        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        try {
            DB::beginTransaction();
            $asignacion = new Asignaciones();
            $anteriorAsignacion = Asignaciones::where(
                'maquina',
                $request->maquina
            )->whereNull('fechaFin')->first();

            $currentDate =  date('Y-m-d h:i:s');
            if ($anteriorAsignacion) {
                if ($anteriorAsignacion->operador === $request->operador) {
                    return response()->json([
                        'status' => Response::HTTP_OK,
                        'message' => 'Datos guardados correctamente',
                        'data' => Maquinas::with('operador')->find($request->maquina),
                    ], Response::HTTP_OK);
                }
                $anteriorAsignacion->fechaFin = $currentDate;
                $anteriorAsignacion->save();
            }
            $asignacion->operador = $request->operador;
            $asignacion->maquina = $request->maquina;
            $asignacion->fechaInicio = $currentDate;
            $asignacion->save();
            $maquina = Maquinas::find($request->maquina);
            $maquina = $maquina->setAttribute('operador', $request->operador);
            $maquina->save();
            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente',
                'data' => Maquinas::with('operador')->find($request->operador),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error de servidor'
            ], Response::HTTP_OK);
        }
    }
}
