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
        Schema::create('clientecontacto', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 500);
            $table->string('telefono', 100);
            $table->string('email', 500);
            $table->date('fechaNacimiento')->nullable();
            $table->string('puesto', 100);
            $table->longText('comentarios');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientecontacto');
    }
};
