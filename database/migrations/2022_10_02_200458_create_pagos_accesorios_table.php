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
        Schema::create('pagos_accesorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accesorio_id');
            $table->foreign('accesorio_id')->references('id')->on('accesorios');
            $table->string('valor', 20);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('pagos_accesorios');
    }
};
