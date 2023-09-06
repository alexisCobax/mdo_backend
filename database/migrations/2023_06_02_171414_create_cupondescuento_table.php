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
        Schema::create('cupondescuento', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 20);
            $table->longText('descripcion');
            $table->decimal('descuentoFijo', 16)->nullable();
            $table->decimal('descuentoPorcentual', 16)->nullable();
            $table->integer('marca')->nullable();
            $table->integer('producto')->nullable();
            $table->integer('cantidadMinima')->nullable();
            $table->decimal('montoMinimo', 16)->nullable();
            $table->date('vencimiento')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('suspendido')->nullable();
            $table->integer('cantidadUtilizados')->nullable();
            $table->date('inicio')->nullable();
            $table->boolean('combinable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupondescuento');
    }
};
