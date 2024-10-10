<?php

namespace App\Http\Controllers;

use App\Models\Maquinas;
use App\Models\Operadores;
use App\Models\OrdenServicio;
use App\Models\Tickets;
use App\Rules\MaquinaAsociadaConEstadoPendiente;
use App\Rules\OrdenConTicketsAsociados;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use SebastianBergmann\Environment\Console;

class OrdenServicioController extends Controller
{

    public function index()
    {
        $ordenServicio = OrdenServicio::with('cliente', 'maquina', 'accesorios')->paginate(10);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $ordenServicio
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $ordenServicio = OrdenServicio::with('cliente', 'maquina', 'accesorio')->all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $ordenServicio
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'cliente' => 'numeric|required',
            'maquina' => ['numeric', 'required', new MaquinaAsociadaConEstadoPendiente()],
            'horometroInicial' => 'required',
            'horasPromedio' => 'required',
            'valorXhora' => 'required',
            'pagare' => 'required|mimes:pdf',
            'valorIda' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'errors' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $path = '';

        if ($request->hasFile('pagare')) {
            $file = $request->file('pagare');
            $name = Uuid::uuid4() . "." . $file->getClientOriginalExtension();
            $path = 'pagares/' . $name;
            Storage::disk('s3')->put($path, file_get_contents($file));
        }

        $ordenServicio = new OrdenServicio($request->all());
        $ordenServicio->pagare = $path;
        $ordenServicio->estado = "PENDIENTE";
        $result = $ordenServicio->save();

        if ($result) {
            $arr = isset($request->accesorios) ? $request->accesorios : [];
            $relations = [];
            foreach ($arr as $value) {
                $relations[$value['id']] = ['valorXhora' => $value['valor']];
            }
            $ordenServicio->accesorios()->sync($relations);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'DAtos guardados correctamente'
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $orden = OrdenServicio::with('accesorios', 'maquina.accesorios', 'cliente')->where('id', $id)->first();
        if (!$orden)
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Orden no encontrada'
            ], Response::HTTP_NOT_FOUND);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Orden encontrada',
            'data' => $orden
        ]);
    }

    public function buscarOrdenDeServicioActiva($operador)
    {
        $modelo = Operadores::find($operador);

        if (!$modelo) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'El operador que esta intentando buscar no se encuentra '
            ]);
        }

        $orden = OrdenServicio::with('accesorios', 'cliente', 'maquina', 'tickets.operador')
            ->whereHas('maquina', function (Builder $query) use ($operador) {
                $query->where('operador', $operador);
            })->where('estado', 'PENDIENTE')->first();

        if (!$orden)
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "El Operador $modelo->nombres, no se encuentra con ordenes de servicio asociadas actualmente."
            ]);

        $maquina = Maquinas::find($orden->maquina);

        $horometro = count($orden->tickets) > 0 ? $orden->tickets[count($orden->tickets) - 1]->horometroFinal : $maquina->horometro;

        $orden->numero_orden = 'ORD-' . str_pad($orden->id, 4, '0', STR_PAD_LEFT);

        $orden->horometro = $horometro;

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Orden de Servicio Encontrada Correctamente.',
            'data' => $orden
        ]);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'id' => [new OrdenConTicketsAsociados()],
            'cliente' => 'numeric|required',
            'maquina' => ['numeric', 'required'],
            'horometroInicial' => 'required',
            'horasPromedio' => 'required',
            'valorXhora' => 'required',
            'valorIda' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'errors' => $validate->errors()
            ], Response::HTTP_OK);
        }

        $path = '';

        if ($request->hasFile('pagare')) {
            $path = $request->file('pagare')->storeAs(
                'pagares',
                'pagare-' . $request->cliente . '-' . date('Y-m-d-hh:mm') . '-' . $request->file('pagare')->getClientOriginalName()
            );
        }

        $ordenServicio = OrdenServicio::find($id);
        $ordenServicio->fill($request->all());
        $ordenServicio->pagare = $path;
        $result = $ordenServicio->save();
        if ($result) {
            $arr = isset($request->accesorios) ? $request->accesorios : [];
            $relations = [];
            foreach ($arr as $value) {
                $relations[$value['id']] = ['valorXhora' => $value['valor']];
            }
            $ordenServicio->accesorios()->sync($relations);
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos gusrdados correctamente'
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error de servidor'
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $ordenServicio = OrdenServicio::find($id);
        if ($ordenServicio) {
            if (Tickets::where('orden', $ordenServicio->id)->count() === 0) {
                $ordenServicio->delete();
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Orden de servicio eliminada correctamente'
                ]);
            } else {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'La Orden de Servicio tiene tickets asociados'
                ], Response::HTTP_OK);
            }
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ], Response::HTTP_OK);
    }

    public function confirmarOds($id)
    {
        $ordenServicio = OrdenServicio::find($id);
        if ($ordenServicio) {
            if (Tickets::where([['orden', $ordenServicio->id], ['estado', 'PENDIENTE']])->count() === 0) {
                $ordenServicio->estado = "CONFIRMADA";
                $ordenServicio->save();
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Orden de servicio confirmada correctamente'
                ]);
            } else {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'La Orden de Servicio ORD-' . str_pad($ordenServicio->id, 4, "0", STR_PAD_LEFT)  . ' tiene tickets pendientes por confirmar'
                ], Response::HTTP_OK);
            }
        }
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Error del servidor'
        ], Response::HTTP_OK);
    }
}
