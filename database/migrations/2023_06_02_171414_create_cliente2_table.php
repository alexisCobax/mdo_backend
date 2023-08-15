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
        Schema::create('cliente2', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 100)->nullable();
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
            $table->integer('usuario');
            $table->boolean('suspendido');
            $table->string('web', 150)->nullable();
            $table->longText('direccionShape')->nullable();
            $table->longText('direccionBill')->nullable();
            $table->integer('vendedor')->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('pais', 50)->nullable();
            $table->string('usuarioVIP', 50)->nullable();
            $table->string('claveVIP', 50)->nullable();
            $table->boolean('VIP')->nullable();
            $table->decimal('ctacte', 18)->nullable();
            $table->string('cpShape', 50)->nullable();
            $table->string('paisShape', 50)->nullable();
            $table->date('primeraCompra')->nullable();
            $table->integer('cantidadDeCompras')->nullable();
            $table->string('idAgile', 30)->nullable();
            $table->decimal('montoMaximoDePago', 18)->nullable();
            $table->string('WhatsApp', 50)->nullable();
            $table->longText('Notas')->nullable();
            $table->integer('tipoDeEnvio')->nullable();
            $table->string('nombreEnvio', 50)->nullable();
            $table->string('regionEnvio', 50)->nullable();
            $table->string('ciudadEnvio', 50)->nullable();
            $table->date('fechaAlta')->nullable();
            $table->string('ipAlta', 30)->nullable();
            $table->date('ultimoLogin')->nullable();
            $table->string('ipUltimoLogin', 30)->nullable();
            $table->boolean('prospecto')->nullable();
            $table->string('contactoApellido', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente2');
    }
};
