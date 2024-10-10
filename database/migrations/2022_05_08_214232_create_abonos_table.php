<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor');
            $table->foreign('proveedor')->references('id')->on('proveedores');
            $table->double('valor');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('detalle-abono', function (Blueprint $table){
             $table->id();
             $table->double('valor');
             $table->foreignId('deuda');
             $table->foreign('deuda')->references('id')->on('deudas');
             $table->softDeletes();
             $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('abonos');
    }
};
