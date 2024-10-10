<?php

namespace App\Http\Controllers;

use App\Models\Operadores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class OperadoresController extends Controller
{
    public function index()
    {
        $operadores = Operadores::paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $operadores
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $operadores = Operadores::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $operadores
        ], Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nombres' => 'required',
            'apellidos' => 'required',
            'cedula' => 'numeric|required|unique:operadores',
            'telefono1' => 'numeric|required',
            'licencia' => 'required|mimes:pdf',
            'direccion' => 'required',
            'email' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        $path = '';

        if ($request->hasFile('licencia')) {
            $file = $request->file('licencia');
            $name = Uuid::uuid4() . "." . $file->getClientOriginalExtension();
            $path = 'licencias/' . $name;
            Storage::disk('s3')->put($path, file_get_contents($file));
        }

        $operador = new Operadores($request->all());
        $operador->nombres = strtoupper($request->nombres);
        $operador->apellidos = strtoupper($request->apellidos);
        $operador->direccion = strtoupper($request->direccion);
        $operador->email = strtoupper($request->email);
        $operador->licencia = $path;
        $result = $operador->save();

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'datos guardados correctamente',
                'data' => $operador
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
            'nombres' => 'required',
            'apellidos' => 'required',
            'cedula' => 'numeric|required|unique:operadores,cedula',
            'telefono1' => 'numeric|required',
            'telefono2' => 'numeric|required',
            'direccion' => 'required',
            'email' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }

        $operador = Operadores::find($id);
        $operador->fill($request->all());
        $operador->nombres = strtoupper($request->nombres);
        $operador->apellidos = strtoupper($request->apellidos);
        $operador->direccion = strtoupper($request->direccion);
        $operador->email = strtoupper($request->email);
        $result = $operador->save();

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardador correctamente',
                'data' => $operador
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error en el servidor'
        ], Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Operadores $operadores
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $operador = Operadores::find($id);
        if ($operador !== null) {
            $operador->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'operador eliminado coreectamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error en el servidor'
        ], Response::HTTP_OK);
    }
}
