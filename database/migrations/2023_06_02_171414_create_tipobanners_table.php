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
        Schema::create('tipobanners', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->char('palabraClave', 30);
            $table->char('nombre', 30);
            $table->longText('descripcion');
            $table->integer('alto')->nullable()->default(0);
            $table->integer('ancho')->nullable()->default(0);
            $table->longText('codigo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipobanners');
    }
};
