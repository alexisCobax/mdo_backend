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
        Schema::create('invoice', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->dateTime('fecha', 6);
            $table->integer('cliente');
            $table->decimal('total', 18)->nullable();
            $table->integer('formaDePago');
            $table->integer('estado');
            $table->longText('observaciones');
            $table->boolean('anulada');
            $table->string('billTo', 250);
            $table->string('shipTo', 250);
            $table->string('shipVia', 50);
            $table->string('FOB', 50);
            $table->string('Terms', 50);
            $table->dateTime('fechaOrden', 6);
            $table->string('salesPerson', 50);
            $table->integer('orden');
            $table->decimal('peso', 18)->nullable();
            $table->integer('cantidad');
            $table->decimal('DescuentoNeto', 18)->nullable()->default(0);
            $table->decimal('DescuentoPorcentual', 18)->nullable()->default(0);
            $table->string('UPS', 50)->nullable();
            $table->decimal('TotalEnvio', 18)->nullable();
            $table->string('codigoUPS', 50)->nullable();
            $table->decimal('subTotal', 18)->nullable();
            $table->decimal('DescuentoPorPromociones', 18)->default(0);
            $table->integer('IdActiveCampaign')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice');
    }
};
