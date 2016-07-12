<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('min_latitude', 8, 5);
            $table->decimal('min_longitude', 8, 5);
            $table->decimal('max_latitude', 8, 5);
            $table->decimal('max_longitude', 8, 5);
            $table->string('name');
            $table->timestamps();

            $table->index(['min_latitude', 'max_latitude']);
            $table->index(['min_longitude', 'max_longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('locations');
    }
}
