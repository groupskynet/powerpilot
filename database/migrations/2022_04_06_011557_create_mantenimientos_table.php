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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('descripcion');
            $table->double('horometro');
            $table->string('modalidad');
            $table->double('costo');
            $table->double('abono')->nullable();
            $table->string('soporte');
            $table->foreignId('maquina');
            $table->foreign('maquina')->references('id')->on('maquinas');
            $table->foreignId('proveedor');
            $table->foreign('proveedor')->references('id')->on('proveedores');
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
        Schema::dropIfExists('mantenimientos');
    }
};
