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
        Schema::create('paypal', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('idPedido')->nullable();
            $table->string('token', 30)->nullable();
            $table->longText('respuesta')->nullable();
            $table->integer('estado');
            $table->longText('respuestaFinal')->nullable();
            $table->integer('idCotizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paypal');
    }
};
