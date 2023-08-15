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
        Schema::create('proveedor', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 30);
            $table->string('direccion', 250);
            $table->integer('ciudad')->nullable();
            $table->string('codigoPostal', 25)->nullable();
            $table->string('telefono', 50);
            $table->string('movil', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('contacto', 50)->nullable();
            $table->string('transportadora', 50)->nullable();
            $table->string('telefonoTransportadora', 50)->nullable();
            $table->longText('observaciones')->nullable();
            $table->string('formaDePago', 15)->nullable();
            $table->boolean('suspendido')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedor');
    }
};
