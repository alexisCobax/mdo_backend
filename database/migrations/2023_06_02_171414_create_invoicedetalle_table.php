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
        Schema::create('invoicedetalle', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('qordered');
            $table->integer('qshipped');
            $table->integer('qborder');
            $table->string('itemNumber', 30);
            $table->string('Descripcion', 500)->nullable();
            $table->decimal('listPrice', 18)->nullable();
            $table->decimal('netPrice', 18)->nullable();
            $table->integer('invoice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoicedetalle');
    }
};
