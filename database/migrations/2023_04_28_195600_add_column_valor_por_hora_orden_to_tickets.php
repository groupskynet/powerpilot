<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('valor_por_hora_orden', 20)->default('0');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('valor_por_hora_orden');
        });
    }
};
