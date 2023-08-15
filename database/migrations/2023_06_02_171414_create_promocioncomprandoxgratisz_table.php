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
        Schema::create('promocioncomprandoxgratisz', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 1000);
            $table->integer('idMarca');
            $table->integer('Cantidad');
            $table->integer('CantidadBonificada');
            $table->boolean('activa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocioncomprandoxgratisz');
    }
};
