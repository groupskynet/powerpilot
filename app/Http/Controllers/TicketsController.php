<?php

namespace App\Http\Controllers;

use App\Models\Consecutivo;
use App\Models\Maquinas;
use App\Models\OrdenServicio;
use App\Models\PagoAccesorio;
use App\Models\PagoMaquina;
use App\Models\Tickets;
use App\Rules\AccesorioTicketRule;
use App\Rules\FacturaGasolinaTicketRule;
use App\Rules\TicketsPosterioresAlaFechaRule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class TicketsController extends Controller
{

    public function index()
    {

        $tickets = Tickets::with('operador', 'cliente', 'maquina', 'accesorio', 'orden')
            ->paginate(10);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $tickets
        ], Response::HTTP_OK);
    }

    public function all()
    {
        $tickets = Tickets::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $tickets
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'cliente' => 'numeric|required',
            'maquina' => 'numeric|required',
            'fecha' => ['required', 'date', 'before:tomorrow', new TicketsPosterioresAlaFechaRule($request->maquina)],
            'nOrden' => 'required',
            'operador' => 'required',
            'accesorio' => [new AccesorioTicketRule($request->maquina, $request->fecha)],
            'horometroInicial' => 'required',
            'horometroFinal' => 'required',
            'tieneCombustible' => 'required',
            'soporte' => 'required|mimes:png,jpg,jpeg|max:1000',
            'factura' => [new FacturaGasolinaTicketRule($request->tieneCombustible)],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $validate->errors()->first(),
                'data' => $validate->errors()
            ], Response::HTTP_OK);
        }

        try {

            $path = '';

            if ($request->hasFile('soporte')) {
                $file = $request->file('soporte');
                $name = Uuid::uuid4() . "." . $file->getClientOriginalExtension();
                $path = 'tickets/' . $name;
                Storage::disk('s3')->put($path, file_get_contents($file));
            }

            if ($request->tieneCombustible && $request->hasFile('factura')) {
                $file = $request->file('factura');
                $name = time() . $file->getClientOriginalName();
                $path = 'combustible/' . $name;
                Storage::disk('s3')->put($path, file_get_contents($file));
            }

            DB::beginTransaction();
            $ticket = new Tickets($request->all());
            $ticket->orden = $request->nOrden;
            $ticket->soporte = $path;

            $maquina = Maquinas::find($ticket->maquina);

            $orden = OrdenServicio::find($ticket->orden);
            $ticket->valor_por_hora_orden =  $orden->valorXhora;

            $pagoMaquina = PagoMaquina::where('maquina_id', $maquina->id)
            ->whereNull('fecha_fin')
            ->first();

            if (!$pagoMaquina) return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Tabla de pagos sin configurar para esta maquina.',
            ], Response::HTTP_OK);

            $ticket->valor_por_hora =  $pagoMaquina->valor;

            if (isset($request->accesorio)) {

                $pagoAccesorio = DB::table('rel_orden_servicio')
                ->where('accesorio', $request->accesorio)
                ->where('orden', $orden->id)
                ->first();

                if (!$pagoAccesorio) return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Tabla de pagos sin configurar para este accesorio.',
                ]);

                $ticket->valor_por_hora_orden =  $pagoAccesorio->valorXhora;

                $pagoAccesorio = PagoAccesorio::where('accesorio_id', $request->accesorio)
                    ->whereNull('fecha_fin')
                    ->first();

                if (!$pagoAccesorio) return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Tabla de pagos sin configurar para este accesorio.',
                ]);

                $ticket->valor_por_hora =  $pagoAccesorio->valor;
            }

            $consecutivo = Consecutivo::where('prefijo', $maquina->prefijo)->first();
            $ticket->consecutivo = $consecutivo->prefijo . '-' . str_pad(++$consecutivo->consecutivo, 4, 0, STR_PAD_LEFT);
            $consecutivo->save();
            $ticket->save();

            $ticket = $ticket->with('operador', 'maquina', 'accesorio', 'cliente')->find($ticket->id);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Datos guardados correctamente',
                'data' => $ticket
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error del servidor'
            ], Response::HTTP_OK);
        }
    }

    public function update($id)
    {
        try {
            DB::beginTransaction();
            $ticket = Tickets::find($id);
            if ($ticket === null) {
                return response()->json([
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'El ticket no fue confirmado, por favor intentelo nuevamente'
                ]);
            }
            $ticket->estado = 'CONFIRMADO';
            $ticket->save();
            $maquina = Maquinas::find($ticket->maquina);
            $maquina->horometro = $ticket->horometroFinal;
            $maquina->save();

            $ticket = Tickets::with('operador', 'cliente', 'maquina', 'accesorio')->find($ticket->id);

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Ticket confirmado correctamente',
                'data' => $ticket
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error del servidor',
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $ticket = Tickets::find($id);
            if ($ticket) {
                $aux = $ticket;
                $ticket->delete();
                $tickets = Tickets::where([['orden', $ticket->orden], ['fecha', '>=', $ticket->fecha], ['id', '>=', $ticket->id]])->get();
                foreach ($tickets as $item) {
                    $item->delete();
                }
                $ticket = Tickets::where([['orden', $ticket->orden], ['estado', 'CONFIRMADO']])->orderBy('fecha')->get()->last();
                $maquina = Maquinas::findOrFail($aux->maquina);
                if ($ticket) {
                    $maquina->horometro = $ticket->horometroFinal;
                    $maquina->save();
                } else {
                    $orden = OrdenServicio::find($aux->orden);
                    $maquina->horometro = $orden->horometroInicial;
                    $maquina->save();
                }
            }
            DB::commit();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Ticket eliminado correctamente',
                'data' => Tickets::with('operador', 'cliente', 'maquina', 'accesorio')->paginate(10)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error del servidor'
            ], Response::HTTP_OK);
        }
    }
}
