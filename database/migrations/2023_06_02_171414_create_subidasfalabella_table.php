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
        Schema::create('subidasfalabella', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha', 6)->nullable();
            $table->string('accion', 50)->nullable();
            $table->integer('idProducto')->nullable();
            $table->longText('resultado')->nullable();
            $table->string('feed', 50)->nullable();
            $table->string('pais', 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subidasfalabella');
    }
};
