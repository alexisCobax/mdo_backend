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
        Schema::create('categoriafalabella', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('Name', 50);
            $table->integer('CategoryId');
            $table->string('GlobalIdentifier', 50);
            $table->integer('AttributeSetId');
            $table->integer('PadreCategoryId');
            $table->string('Pais', 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categoriafalabella');
    }
};
