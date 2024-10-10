<?php

namespace App\Http\Controllers;

use App\Models\Accesorios;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AccesoriosController extends Controller
{

    public function index()
    {
        $accesorios= Accesorios::with('marca','maquina')->paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data'=> $accesorios
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $accesorios= Accesorios::with('marca','maquina')->all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data'=> $accesorios
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'nombre' => 'required',
            'serie' => 'required',
            'marca' => 'numeric|required',
            'modelo' => 'numeric|required',
            'linea' => 'required',
            'registro' => 'required',
            'maquina' => 'numeric|required',
        ]);
        if($validate->fails()){
            return response()->json($validate->errors());
            return response()->json([
                'status' =>  Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ],Response::HTTP_OK);
        }

        $accesorio = new Accesorios($request->all());
        $accesorio->nombre = strtoupper($request->nombre);
        $accesorio->serie = strtoupper($request->serie);
        $accesorio->linea = strtoupper($request->linea);
        $accesorio->registro= strtoupper($request->registro);
        $result = $accesorio->save();
        if($result){
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente',
                'data' => $accesorio
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(),[
            'nombre' => 'required',
            'serie' => 'required',
            'marca' => 'numeric|required',
            'modelo' => 'required',
            'linea' => 'required',
            'registro' => 'required',
            'maquina' => 'numeric|required',
        ]);

        if($validate->fails()){
            return response()->json([
                'status' =>  Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ],Response::HTTP_OK);
        }

        $accesorio = Accesorios::find($id);
        $accesorio->fill($request->all());
        $accesorio->nombre = strtoupper($request->nombre);
        $accesorio->serie= strtoupper($request->serie);
        $accesorio->linea= strtoupper($request->linea);
        $accesorio->registro= strtoupper($request->registro);
        $result = $accesorio->save();

        if($result){
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente',
                'data' => $accesorio
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
         $accesorio = Accesorios::find($id);
        if($accesorio){
            $accesorio->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Accesorio eliminado correctamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ],Response::HTTP_OK);
    }
}
