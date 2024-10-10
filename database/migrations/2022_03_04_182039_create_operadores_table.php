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
       Schema::create('operadores',function (Blueprint $table){
           $table->id();
           $table->string('nombres');
           $table->string('apellidos');
           $table->string('cedula');
           $table->string('telefono1');
           $table->string('telefono2')->nullable();
           $table->string('licencia');
           $table->string('direccion');
           $table->string('email');
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
        Schema::dropIfExists('operadores');
    }
};
