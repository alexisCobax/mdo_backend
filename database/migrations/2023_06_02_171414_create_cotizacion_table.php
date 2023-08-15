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
        Schema::create('cotizacion', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha', 6);
            $table->integer('cliente');
            $table->decimal('total', 18)->default(0);
            $table->integer('estado')->nullable()->default(1);
            $table->integer('IdActiveCampaign')->nullable();
            $table->decimal('descuento', 18)->nullable()->default(0);
            $table->decimal('subTotal', 18)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizacion');
    }
};
