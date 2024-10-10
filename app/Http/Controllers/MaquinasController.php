<?php

namespace App\Http\Controllers;

use App\Models\Maquinas;
use App\Models\Consecutivo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaquinasController extends Controller
{

    public function index()
    {
        $maquinas = Maquinas::with([
            'pagos' => fn ($query) => $query->where('fecha_fin', NULL),
            'accesorios.pagos' => fn ($query) => $query->where('fecha_fin', NULL)
        ])->with('marca', 'accesorios.marca', 'operador')->paginate(10);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $maquinas
        ], Response::HTTP_OK);
    }
    public function all()
    {
        $maquinas = Maquinas::with('accesorios', 'operador')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $maquinas
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = validator::make($request->all(), [
            'nombre' => 'required',
            'serie' => 'required',
            'marca' => 'numeric|required',
            'modelo' => 'required',
            'linea' => 'required',
            'prefijo' => 'required',
            'registro' => 'required',
            'tipo' => 'required',
            'horometro' => 'numeric|required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' =>  Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'data' => $validate->errors()
            ], Response::HTTP_OK);
        }

        try {
            DB::beginTransaction();
            $existe = Consecutivo::where('prefijo', $request->prefijo)->count() > 0;

            if ($existe) return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Ya Existe un Consecutivo con este Prefijo'
            ], Response::HTTP_OK);

            $maquina = new Maquinas($request->all());
            $maquina->nombre = strtoupper($request->nombre);
            $maquina->serie = strtoupper($request->serie);
            $maquina->linea = strtoupper($request->linea);
            $maquina->registro = strtoupper($request->registro);
            $maquina->prefijo = strtoupper($request->prefijo);
            $result = $maquina->save();

            $consecutivo = new Consecutivo(['prefijo' => $request->prefijo, 'consecutivo' => 1]);
            $consecutivo->save();

            DB::commit();

            if ($result) {
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Datos guardados correctamente',
                    'data' => $maquina
                ], Response::HTTP_OK);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error de servidor',
            ], Response::HTTP_OK);
        }
    }

    public function update(Request $request, $id)
    {

        $validate = Validator::make($request->all(), [
            'nombre' => 'required',
            'serie' => 'required',
            'marca' => 'numeric|required',
            'modelo' => 'numeric|required',
            'prefijo' => 'required',
            'horometro' => 'numeric|required',
            'linea' => 'required',
            'registro' => 'required',
            'tipo' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' =>  Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        $maquina = Maquinas::find($id);
        $horometro = $maquina->horometro;
        $maquina->fill($request->all());
        $maquina->nombre = strtoupper($request->nombre);
        $maquina->serie = strtoupper($request->serie);
        $maquina->linea = strtoupper($request->linea);
        $maquina->registro = strtoupper($request->registro);
        $maquina->horometro = $horometro;
        $result = $maquina->save();

        if ($request->prefijo !== $maquina->prefijo) {
            $consecutivo = Consecutivo::where('prefijo', $maquina->prefijo)->first();
            $consecutivo->delete();
            $newConsecutivo = new Consecutivo(['prefifo' => $request->prefijo, 'consecutivo' => 1]);
            $newConsecutivo->save();
        }

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente',
                'data' => $maquina
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }


    public function destroy($id)
    {
        $maquina = Maquinas::find($id);

        if ($maquina) {
            $maquina->delete();
            $consecutivo = Consecutivo::where('prefijo', $maquina->prefijo)->first();
            if ($consecutivo) $consecutivo->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Maquina eliminada correctamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ], Response::HTTP_OK);
    }
}
