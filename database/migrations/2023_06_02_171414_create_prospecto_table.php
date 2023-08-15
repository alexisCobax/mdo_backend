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
        Schema::create('prospecto', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('codigoPostal', 50)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 250)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('contacto', 100)->nullable();
            $table->string('puestoContacto', 250)->nullable();
            $table->string('transportadora', 150)->nullable();
            $table->string('telefonoTransportadora', 50)->nullable();
            $table->longText('observaciones')->nullable();
            $table->string('web', 150)->nullable();
            $table->longText('direccionShape')->nullable();
            $table->longText('direccionBill')->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('pais', 50)->nullable();
            $table->date('fecha')->nullable();
            $table->string('tipo', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospecto');
    }
};
