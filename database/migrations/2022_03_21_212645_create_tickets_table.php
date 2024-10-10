<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('orden');
            $table->foreign('orden')->references('id')->on('orden_servicios');
            $table->string('horometroInicial');
            $table->string('horometroFinal');
            $table->foreignId('cliente');
            $table->foreign('cliente')->references('id')->on('clientes');
            $table->foreignId('maquina');
            $table->foreign('maquina')->references('id')->on('maquinas');
            $table->foreignId('operador');
            $table->foreign('operador')->references('id')->on('operadores');
            $table->foreignId('accesorio')->nullable();
            $table->foreign('accesorio')->references('id')->on('accesorios');
            $table->string('soporte');
            $table->string('galones')->nullable();
            $table->string('costo')->nullable();
            $table->string('factura')->nullable();
            $table->string('estado')->default('PENDIENTE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
