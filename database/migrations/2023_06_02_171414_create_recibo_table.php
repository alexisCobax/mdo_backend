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
        Schema::create('recibo', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cliente');
            $table->date('fecha');
            $table->integer('formaDePago');
            $table->decimal('total', 18);
            $table->boolean('anulado');
            $table->string('observaciones', 1000);
            $table->integer('pedido')->nullable();
            $table->boolean('garantia')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recibo');
    }
};
