<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()
    {
        Schema::table('deudas', function (Blueprint $table) {
            $table->foreignId('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');
        });
    }

    public function down()
    {
        Schema::table('deudas', function (Blueprint $table) {
            $table->dropForeign('deudas_proveedor_id_foreign');
        });
    }
};
