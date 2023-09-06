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
        Schema::create('tipoproducto', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->integer('CantidadMinima')->nullable()->default(0);
            $table->boolean('suspendido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipoproducto');
    }
};
