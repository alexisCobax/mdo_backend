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
        Schema::create('jet', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('token', 2000);
            $table->dateTime('vencimiento', 6);
            $table->string('tokenType', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jet');
    }
};
