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
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('apellido', 200);
            $table->string('email', 300);
            $table->string('clave', 20);
            $table->integer('permisos');
            $table->boolean('suspendido');
            $table->string('token', 50)->nullable();
            $table->dateTime('token_exp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
};
