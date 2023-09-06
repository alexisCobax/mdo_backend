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
        Schema::create('pedidocupon', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cupon')->nullable();
            $table->integer('pedido')->nullable();
            $table->decimal('monto', 18)->nullable();
            $table->integer('cotizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidocupon');
    }
};
