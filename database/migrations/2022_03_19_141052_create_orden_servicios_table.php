<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->float('horometroInicial');
            $table->float('horasPromedio');
            $table->float('valorXhora')->default(0);
            $table->float('Descuento')->nullable();
            $table->string('pagare');
            $table->float('valorIda')->default(0);
            $table->float('valorVuelta')->default(0);
            $table->foreignId('maquina');
            $table->foreign('maquina')->references('id')->on('maquinas');
            $table->foreignId('cliente');
            $table->foreign('cliente')->references('id')->on('clientes');
            $table->string('estado')->default('PENDIENTE');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rel_orden_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accesorio')->nullable();
            $table->foreign('accesorio')->references('id')->on('accesorios');
            $table->foreignId('orden')->nullable();
            $table->foreign('orden')->references('id')->on('orden_servicios');
            $table->float('valorXhora');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_servicios');
    }
};
