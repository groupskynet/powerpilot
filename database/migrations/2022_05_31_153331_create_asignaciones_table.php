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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador');
            $table->foreign('operador')->references('id')->on('operadores');
            $table->foreignId('maquina');
            $table->foreign('maquina')->references('id')->on('maquinas');
            $table->timestamp('fechaInicio');
            $table->timestamp('fechaFin')->nullable();
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
        Schema::dropIfExists('asignaciones');
    }
};
