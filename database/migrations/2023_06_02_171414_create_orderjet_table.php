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
        Schema::create('orderjet', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('merchant_order_id', 50)->nullable();
            $table->string('reference_order_id', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('hash_email', 100)->nullable();
            $table->integer('idPedido')->nullable();
            $table->longText('detalle')->nullable();
            $table->date('fecha')->nullable();
            $table->string('enlace', 400)->nullable();
            $table->dateTime('response_shipment_date', 6)->nullable();
            $table->string('response_shipment_method', 50)->nullable();
            $table->dateTime('expected_delivery_date', 6)->nullable();
            $table->string('ship_from_zip_code', 50)->nullable();
            $table->dateTime('carrier_pick_up_date', 6)->nullable();
            $table->string('carrier', 50)->nullable();
            $table->string('enlaceDevolucion', 400)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderjet');
    }
};
