<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('marker_id')->unsigned();
            $table->integer('clause_1_id')->unsigned();
            $table->string('clause_1_word_list');
            $table->integer('clause_1_word_id')->unsigned();
            $table->integer('conjunction')->unsigned()->nullable();
            $table->integer('clause_2_id')->unsigned()->nullable();
            $table->string('clause_2_word_list')->nullable();
            $table->integer('clause_2_word_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('marker_id')->references('id')->on('markers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messages');
    }
}
