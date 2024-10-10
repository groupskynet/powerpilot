<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 10);
            $table->string('nombre');
            $table->string('serie');
            $table->string('modelo');
            $table->string('prefijo', 10);
            $table->double('horometro')->default('0');
            $table->string('linea');
            $table->string('registro');
            $table->string('estado')->default('ACTIVA');
            $table->string('placa', 10)->nullable();
            $table->foreignId('marca');
            $table->foreign('marca')->references('id')->on('marcas');
            $table->foreignId('operador')->nullable();
            $table->foreign('operador')->references('id')->on('operadores');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maquinas');
    }
};
