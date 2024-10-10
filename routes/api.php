<?php

use App\Http\Controllers\AbonosController;
use App\Http\Controllers\AccesoriosController;
use App\Http\Controllers\AsignacionesController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DeudasController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\MantenimientosController;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\MarcasController;
use App\Http\Controllers\OperadoresController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ReporteHorometroController;
use App\Http\Controllers\StatusCheckController;
use App\Http\Controllers\TicketsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['Cors']], function () {
    Route::get('status', [StatusCheckController::class, 'status']);
    Route::resource('marcas', MarcasController::class);
    Route::resource('maquinas', MaquinasController::class);
    Route::resource('accesorios', AccesoriosController::class);
    Route::resource('operadores', OperadoresController::class);
    Route::resource('clientes', ClientesController::class);
    Route::resource('proveedores', ProveedoresController::class);
    Route::resource('gastos', GastosController::class);
    Route::resource('orden-servicio', OrdenServicioController::class);
    Route::resource('tickets', TicketsController::class);
    Route::resource('mantenimientos', MantenimientosController::class);
    Route::resource('deudas', DeudasController::class);
    Route::resource('abonos', AbonosController::class);
    Route::get('asignaciones', [AsignacionesController::class, 'index']);
    Route::post('maquina/asignar', [AsignacionesController::class, 'store']);
    Route::get('orden/{operador}', [OrdenServicioController::class, 'buscarOrdenDeServicioActiva']);
    Route::put('orden/confirmarOds/{id}', [OrdenServicioController::class, 'confirmarOds']);
    Route::post('file', [FilesController::class, 'getFile']);
    Route::group(['prefix' => 'pagos'], function () {
        Route::post('maquina', [PagosController::class, 'maquina']);
        Route::post('accesorios', [PagosController::class, 'accesorio']);
    });
    Route::group(['prefix' => 'reportes'], function () {
        Route::post('horometro', [ReporteHorometroController::class, 'index']);
    });
    Route::group(['prefix' => 'literales'], function () {
        Route::get('marcas/all', [MarcasController::class, 'all']);
        Route::get('maquinas/all', [MaquinasController::class, 'all']);
        Route::get('clientes/all', [ClientesController::class, 'all']);
        Route::get('operadores/all', [OperadoresController::class, 'all']);
        Route::get('accesorios/all', [AccesoriosController::class, 'all']);
        Route::get('proveedores/all', [ProveedoresController::class, 'all']);
        Route::get('ordenServicio/all', [OrdenServicioController::class, 'all']);
    });
});
