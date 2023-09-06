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
        Schema::create('compra', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('proveedor');
            $table->dateTime('fechaDeIngreso', 6);
            $table->dateTime('fechaDePago', 6);
            $table->decimal('precio', 18)->nullable();
            $table->string('numeroLote', 30);
            $table->longText('observaciones')->nullable();
            $table->boolean('pagado');
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
        Schema::dropIfExists('compra');
    }
};
