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
        Schema::create('carrito', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha', 6)->nullable();
            $table->integer('cliente')->nullable();
            $table->integer('estado')->nullable();
            $table->integer('vendedor')->nullable();
            $table->integer('formaDePago')->nullable();
            $table->string('session', 50)->nullable();
            $table->longText('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrito');
    }
};
