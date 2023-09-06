<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientoproductos', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->date('fecha');
            $table->integer('origen');
            $table->integer('destino');
            $table->integer('cantidad');
            $table->integer('idProducto');
            $table->longText('comentarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientoproductos');
    }
};
