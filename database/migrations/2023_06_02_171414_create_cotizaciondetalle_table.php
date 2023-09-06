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
        Schema::create('cotizaciondetalle', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('cotizacion');
            $table->integer('producto');
            $table->decimal('precio', 18);
            $table->integer('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciondetalle');
    }
};
