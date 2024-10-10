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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('cedula');
            $table->string('nombres');
            $table->string('telefono');
            $table->string('direccion');
            $table->string('email');
            $table->string('nit')->nullable();
            $table->string('razonSocial')->nullable();
            $table->boolean('iva')->nullable();
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
        Schema::dropIfExists('proveedores');
    }
};
