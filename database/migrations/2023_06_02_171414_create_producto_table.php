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
        Schema::create('producto', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('descripcion', 500);
            $table->integer('tipo');
            $table->integer('categoria');
            $table->integer('marca');
            $table->integer('material');
            $table->integer('estuche');
            $table->integer('sexo');
            $table->integer('proveedor');
            $table->decimal('precio', 18);
            $table->boolean('suspendido');
            $table->integer('comision');
            $table->integer('stock');
            $table->integer('stockMinimo');
            $table->string('codigo', 30)->nullable();
            $table->boolean('alarmaStockMinimo');
            $table->string('color', 40);
            $table->integer('tamano')->default(1);
            $table->string('ubicacion', 50)->default('sin Datos');
            $table->integer('grupo')->nullable()->default(1);
            $table->boolean('pagina')->default(true);
            $table->decimal('costo', 18)->default(0);
            $table->boolean('bestBrasil')->default(false);
            $table->integer('posicion')->default(500);
            $table->integer('stockRoto')->nullable()->default(0);
            $table->date('ultimoIngresoDeMercaderia')->nullable();
            $table->date('ultimaVentaDeMercaderia')->nullable();
            $table->integer('genero')->nullable();
            $table->integer('imagenPrincipal')->nullable();
            $table->string('UPCreal', 20)->nullable();
            $table->boolean('mdoNet')->nullable();
            $table->boolean('jet')->nullable();
            $table->decimal('precioJet', 18)->nullable();
            $table->integer('stockJet')->nullable();
            $table->integer('multipack')->nullable();
            $table->string('nodeJet', 50)->nullable();
            $table->string('nombreEN', 500)->nullable();
            $table->string('descripcionEN', 2000)->nullable();
            $table->decimal('peso', 18)->nullable();
            $table->boolean('enviadoAJet')->nullable();
            $table->integer('stockFalabella')->nullable();
            $table->decimal('precioFalabella', 18)->nullable();
            $table->boolean('verEnFalabella')->nullable();
            $table->boolean('enviadoAFalabella')->nullable();
            $table->integer('categoriaFalabella')->nullable();
            $table->string('marcaFalabella', 50)->nullable();
            $table->longText('descripcionFalabella')->nullable();
            $table->double('precioPromocional');
            $table->boolean('destacado');
            $table->double('largo');
            $table->double('alto');
            $table->double('ancho');
            $table->text('descripcionLarga');
            $table->integer('colorPrincipal');
            $table->integer('colorSecundario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto');
    }
};
