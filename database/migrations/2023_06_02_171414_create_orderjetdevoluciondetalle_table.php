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
        Schema::create('orderjetdevoluciondetalle', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('idOrdenDevolucion');
            $table->string('order_item_id', 50);
            $table->string('alt_order_item_id', 50);
            $table->bigInteger('return_quantity');
            $table->string('merchant_sku', 50);
            $table->string('merchant_sku_title', 50);
            $table->string('reason', 50);
            $table->string('requested_refund_amount', 400);
            $table->decimal('amount_principal', 18);
            $table->decimal('amoun_tax', 18);
            $table->decimal('amount_shipping_cost', 18);
            $table->decimal('amount_shipping_tax', 18);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderjetdevoluciondetalle');
    }
};
