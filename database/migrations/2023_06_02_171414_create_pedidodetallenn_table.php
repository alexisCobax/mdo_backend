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
        Schema::create('pedidodetallenn', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('descripcion', 100)->nullable();
            $table->decimal('precio', 18);
            $table->integer('pedido');
            $table->integer('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidodetallenn');
    }
};
