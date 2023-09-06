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
        Schema::create('orderjetdevolucion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('enlace', 400);
            $table->longText('detalle');
            $table->boolean('agree_to_return_charge');
            $table->string('alt_order_id', 50);
            $table->string('alt_return_authorization_id', 50);
            $table->string('merchant_order_id', 50);
            $table->string('merchant_return_authorization_id', 50)->nullable();
            $table->string('merchant_return_charge', 50)->nullable();
            $table->string('reference_order_id', 50)->nullable();
            $table->string('reference_return_authorization_id', 50)->nullable();
            $table->boolean('refund_without_return')->nullable();
            $table->string('return_date', 50)->nullable();
            $table->string('return_status', 50)->nullable();
            $table->string('shipping_carrier', 50)->nullable();
            $table->string('tracking_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderjetdevolucion');
    }
};
