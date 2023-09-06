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
        Schema::create('pedidodescuentospromocion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('idPedido');
            $table->integer('idPromocion');
            $table->string('descripcion', 200);
            $table->decimal('montoDescuento', 18);
            $table->integer('idTipoPromocion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidodescuentospromocion');
    }
};
