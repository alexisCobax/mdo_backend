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
        Schema::create('compradetalle', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('compra');
            $table->integer('producto');
            $table->integer('cantidad');
            $table->decimal('precioUnitario', 18, 5)->nullable();
            $table->boolean('enDeposito')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compradetalle');
    }
};
