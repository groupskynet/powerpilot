<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('deudas', function (Blueprint $table) {
            $table->id();
            $table->double('valor');
            $table->string('estado');
            $table->unsignedBigInteger('relation_id');
            $table->string('modelo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deudas');
    }
};
