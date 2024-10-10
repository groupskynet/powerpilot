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
        Schema::create('accesorios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('serie');
            $table->string('linea');
            $table->string('registro');
            $table->string('modelo');
            $table->foreignId('marca');
            $table->foreign('marca')->references('id')->on('marcas');
            $table->foreignId('maquina');
            $table->foreign('maquina')->references('id')->on('maquinas');
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
        Schema::dropIfExists('accesorios');
    }
};
