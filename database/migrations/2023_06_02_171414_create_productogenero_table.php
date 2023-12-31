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
        Schema::create('productogenero', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nombre', 50);

            $table->unique(['id'], 'id_2');
            $table->unique(['id'], 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productogenero');
    }
};
