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
        Schema::create('pagostarjeta', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('idPedido');
            $table->longText('respuesta');
            $table->string('CC', 20);
            $table->string('Vencimiento', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagostarjeta');
    }
};
