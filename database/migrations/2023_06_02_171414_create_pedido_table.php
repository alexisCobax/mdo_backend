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
        Schema::create('pedido', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha', 6);
            $table->integer('cliente');
            $table->integer('estado');
            $table->integer('vendedor');
            $table->integer('formaDePago');
            $table->longText('observaciones')->nullable();
            $table->integer('invoice')->nullable()->default(0);
            $table->decimal('total', 18)->nullable()->default(0);
            $table->decimal('descuentoPorcentual', 18)->nullable()->default(0);
            $table->decimal('descuentoNeto', 18)->nullable()->default(0);
            $table->decimal('totalEnvio', 18)->nullable();
            $table->integer('recibo')->nullable();
            $table->integer('origen')->nullable();
            $table->integer('etapa')->nullable();
            $table->integer('tipoDeEnvio')->nullable();
            $table->string('envioNombre', 50)->nullable();
            $table->string('envioPais', 50)->nullable();
            $table->string('envioRegion', 50)->nullable();
            $table->string('envioCiudad', 50)->nullable();
            $table->string('envioDomicilio', 300)->nullable();
            $table->string('envioCp', 50)->nullable();
            $table->string('idAgile', 30)->nullable();
            $table->integer('IdActiveCampaign')->nullable();
            $table->integer('idTransportadora')->nullable();
            $table->string('transportadoraNombre', 50)->nullable();
            $table->string('transportadoraTelefono', 50)->nullable();
            $table->string('codigoSeguimiento', 50)->nullable();
            $table->boolean('MailSeguimientoEnviado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido');
    }
};
