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
        Schema::create('pedidodetalle', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('pedido');
            $table->integer('producto');
            $table->decimal('precio', 18);
            $table->integer('cantidad');
            $table->decimal('costo', 18)->nullable();
            $table->decimal('envio', 18)->nullable();
            $table->decimal('tax', 18)->nullable();
            $table->decimal('taxEnvio', 18)->nullable();
            $table->string('jet_order_item_id', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidodetalle');
    }
};
