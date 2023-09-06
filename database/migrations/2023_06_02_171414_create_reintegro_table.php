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
        Schema::create('reintegro', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cliente');
            $table->dateTime('fecha', 6);
            $table->decimal('total', 18);
            $table->boolean('anulado');
            $table->string('observaciones', 1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reintegro');
    }
};
