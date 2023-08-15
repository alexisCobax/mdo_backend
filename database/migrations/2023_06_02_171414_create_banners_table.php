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
        Schema::create('banners', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('tipoUbicacion');
            $table->longText('codigo');
            $table->boolean('suspendido')->default(false);
            $table->integer('orden')->nullable();
            $table->string('tipoArchivo', 10)->nullable();
            $table->string('link', 500)->nullable();
            $table->string('nombre', 50)->nullable();
            $table->string('tipo', 3)->nullable();
            $table->string('texto1', 50)->nullable();
            $table->string('texto2', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
