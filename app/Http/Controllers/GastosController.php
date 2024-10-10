<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Services\FactoryPago as ServicesFactoryPago;

class GastosController extends Controller
{

    public function index()
    {
        $gastos = Gastos::with('maquina')->paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $gastos
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $gastos = Gastos::with('maquina')->all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $gastos
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'maquina' => 'numeric|required',
            'proveedor' => 'numeric|required',
            'modalidad' => 'required',
            'valor' => 'required',
            'descripcion' => 'required',
            'soporte' => 'required|mimes:pdf'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data',
                'data' => $validate->failed()
            ], Response::HTTP_OK);
        }
        $path = '';

        if ($request->hasFile('soporte')) {
            $file = $request->file('soporte');
            $name = Uuid::uuid4() . "." . $file->getClientOriginalExtension();
            $path = 'gastos/' . $name;
            Storage::disk('s3')->put($path, file_get_contents($file));
        }

        $gastos = new Gastos($request->all());
        $gastos->descripcion = strtoupper($request->descripcion);
        $gastos->soporte = $path;
        $result = $gastos->save();
    
        //adapter data for the payment
        $request->gasto = $gastos->id;
        $request->costo = $request->valor;

        $factory = new ServicesFactoryPago($request->modalidad);
        $pago = $factory->create();
        $pago->pago($request);

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
     * Display the specified resource.
     *
     * @param  \App\Models\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function show(Gastos $gastos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function edit(Gastos $gastos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'maquina' => 'numeric|required',
            'valor' => 'required',
            'descripcion' => 'required',
            'soporte' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'invalid data'
            ], Response::HTTP_OK);
        }


        $gastos = Gastos::find($id);
        $gastos->fill($request->all());
        $gastos->descripcion = strtoupper($request->descripcion);
        $result = $gastos->save();

        if ($result) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente'
            ], Response::HTTP_OK);;
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gasto = Gastos::find($id);
        if ($gasto) {
            $gasto->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Gasto eliminado correctamente'
            ]);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ], Response::HTTP_OK);
    }
}
