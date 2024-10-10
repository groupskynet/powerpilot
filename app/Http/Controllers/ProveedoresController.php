<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{

    public function index()
    {
        $proveedores = Proveedores::paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'succes',
            'data' => $proveedores
        ], Response::HTTP_OK);

    }

    public function all()
    {
        $proveedores = Proveedores::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'succes',
            'data' => $proveedores
        ], Response::HTTP_OK);

    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tipo' => 'required',
            'cedula' => 'numeric|required|unique:proveedores',
            'nombres' => 'required',
            'telefono' => 'numeric|required',
            'direccion' => 'required',
            'email' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'errors' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $proveedor = new Proveedores($request->all());
        $proveedor->nombres = strtoupper($request->nombres);
        $proveedor->direccion = strtoupper($request->direccion);
        $proveedor->email = strtoupper($request->email);
        if ($proveedor->razonSocial !== null) {
            $proveedor->razonSocial = strtoupper($request->razonSocial);
        }
        $result = $proveedor->save();

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'datos guardados correctamente',
                'data' => $proveedor
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);

    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'tipo' => 'required',
            'cedula' => 'numeric|required',
            'nombres' => 'required',
            'telefono' => 'numeric|required',
            'direccion' => 'required',
            'email' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'errors' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $proveedores = Proveedores::find($id);
        $proveedores->fill($request->all());
        $proveedores->nombres = strtoupper($request->nombres);
        $proveedores->direccion = strtoupper($request->direccion);
        $proveedores->email = strtoupper($request->email);

        if ($proveedores->razonSocial !== null) {
            $proveedores->razonSocial = strtoupper($request->razonSocial);
        }
        $result = $proveedores->save();

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos Actualizados correctamente'
            ], Response::HTTP_OK);
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Errod de servidor'
            ], Response::HTTP_OK);
        }

    }

    public function destroy($id)
    {
        $proveedor = Proveedores::find($id);
        if ($proveedor !== null) {
            $proveedor->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Proveedor eliminado correctamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }
}
