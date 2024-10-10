<?php

namespace App\Http\Controllers;

use App\Models\Marcas;
use App\Models\Maquinas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class MarcasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $marcas = Marcas::paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $marcas
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $marcas = Marcas::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $marcas
        ], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        $marca = new Marcas();
        $marca->nombre = strtoupper($request->nombre);
        $result = $marca->save();
        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente'
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        $marca = Marcas::find($id);
        $marca->nombre = strtoupper($request->nombre);
        $result = $marca->save();
        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente'
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $marca = Marcas::find($id);
        $maquina = Maquinas::where('marca', $marca->id)->first();
        if($maquina){
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Error, No se puede eliminar marcas que tengan acesorios asociados.'
            ]);
        }
        if ($marca) {
            $marca->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Marca eliminada correctamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ], Response::HTTP_OK);
    }
}
