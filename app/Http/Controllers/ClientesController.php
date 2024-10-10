<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class ClientesController extends Controller
{

    public function index()
    {
        $clientes=Clientes::paginate(10);
        return response()->json([
            'status'=> Response::HTTP_OK,
            'message'=>'succes',
            'data'=>$clientes
        ],Response::HTTP_OK);
    }

    public function all()
    {
        $clientes=Clientes::all();
        return response()->json([
            'status'=> Response::HTTP_OK,
            'message'=>'succes',
            'data'=>$clientes
        ],Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'tipo'=>'required',
            'cedula'=>'numeric|required|unique:clientes',
            'nombres'=>'required',
            'telefono'=>'numeric|required',
            'direccion'=>'required',
            'email'=>'required'
        ]);
        if($validate->fails()){
            return response()->json([
              'status'=>Response::HTTP_BAD_REQUEST,
              'message'=>'invalid data'
            ],Response::HTTP_OK);
        }

        $cliente = new Clientes($request->all());
        $cliente->nombres = strtoupper($request->nombres);
        $cliente->direccion = strtoupper($request->direccion);
        $cliente->email= strtoupper($request->email);
        if($cliente->razonSocial !== null){
            $cliente->razonSocial= strtoupper($request->razonSocial);
        }
        $result = $cliente->save();

        if($result){
            return response()->json([
                'status'=>Response::HTTP_OK,
                'message'=>'datos guardados correctamente',
                'data' => $cliente
            ],Response::HTTP_OK);
        }
        return response()->json([
            'status'=>Response::HTTP_INTERNAL_SERVER_ERROR,
            'message'=>'Error de servidor'
        ],Response::HTTP_OK);
}


    public function update(Request $request,$id)
    {
        $validate=Validator::make($request->all(),[
            'tipo'=>'required',
            'cedula'=>'numeric|required',
            'nombres'=>'required',
            'telefono'=>'numeric|required',
            'direccion'=>'required',
            'email'=>'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status'=>Response::HTTP_BAD_REQUEST,
                'message'=> $validate->failed()
            ],Response::HTTP_OK);
        }

        $clientes=Clientes::find($id);
        $clientes->fill($request->all());
        $clientes->nombres=strtoupper($request->nombres);
        $clientes->direccion=strtoupper($request->direccion);
        $clientes->email=strtoupper($request->email);

        if($clientes->razonSocial !== null){
            $clientes->razonSocial=strtoupper($request->razonSocial);
        }
        $result = $clientes->save();

        if($result){
            return response()->json([
                'status'=>Response::HTTP_OK,
                'message'=>'Datos Actualizados correctamente'
            ],Response::HTTP_OK);
            return response()->json([
                'status'=>Response::HTTP_INTERNAL_SERVER_ERROR,
                'message'=>'Error de servidor'
            ],Response::HTTP_OK);
        }

    }

    public function destroy($id)
    {
        $cliente =Clientes::find($id);
        if($cliente!==null){
            $cliente->delete();
            return response()->json([
                'status'=>Response::HTTP_OK,
                'message'=>'cliente eliminado correctamente'
            ]);
        }
        return response()->json([
            'status'=>Response::HTTP_INTERNAL_SERVER_ERROR,
            'message'=>'Error de servidor'
        ],Response::HTTP_OK);
    }
}
